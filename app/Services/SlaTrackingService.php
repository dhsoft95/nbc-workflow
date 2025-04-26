<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\SlaConfiguration;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SlaWarningMail;
use App\Mail\SlaCriticalMail;
use Spatie\Permission\Models\Role;

class SlaTrackingService
{
    /**
     * Check all active integrations against SLA thresholds
     * This should be called by a scheduled task
     */
    public function checkAllIntegrations()
    {
        Log::info('Starting SLA check for all integrations');

        // Get all integrations in approval stages
        $statuses = [
            'submitted',
            'app_owner_approval',
            'idi_approval',
            'security_approval',
            'infrastructure_approval'
        ];

        $integrations = Integration::whereIn('status', $statuses)->get();

        foreach ($integrations as $integration) {
            $this->checkIntegrationSla($integration);
        }

        Log::info('Completed SLA check for ' . $integrations->count() . ' integrations');
    }

    /**
     * Check a single integration against SLA thresholds
     *
     * @param Integration $integration
     * @return void
     */
    public function checkIntegrationSla(Integration $integration)
    {
        $currentStage = $this->mapStatusToStage($integration->status);
        if (!$currentStage) {
            Log::info("Integration #{$integration->id} is not in an approval stage");
            return;
        }

        // Get last approval history for current stage
        $latestHistory = $integration->approvalHistories()
            ->where('stage', $currentStage)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestHistory) {
            Log::warning("No approval history found for integration #{$integration->id} at stage {$currentStage}");
            return;
        }

        // Calculate hours spent in current stage
        $enteredStageAt = $latestHistory->created_at;
        $hoursInStage = $this->calculateBusinessHours($enteredStageAt, now(), $currentStage);

        // Get SLA configuration for this stage
        $slaConfig = SlaConfiguration::where('stage', $currentStage)->first();
        if (!$slaConfig) {
            Log::warning("No SLA configuration found for stage {$currentStage}");
            return;
        }

        Log::info("Integration #{$integration->id} has been in {$currentStage} stage for {$hoursInStage} hours. Warning: {$slaConfig->warning_hours}, Critical: {$slaConfig->critical_hours}");

        // Check if warning threshold is exceeded
        if ($hoursInStage >= $slaConfig->warning_hours && $hoursInStage < $slaConfig->critical_hours) {
            $this->sendWarningNotification($integration, $currentStage, $hoursInStage, $slaConfig);
        }

        // Check if critical threshold is exceeded
        if ($hoursInStage >= $slaConfig->critical_hours) {
            $this->sendCriticalNotification($integration, $currentStage, $hoursInStage, $slaConfig);
        }
    }

    /**
     * Map integration status to approval stage
     *
     * @param string $status
     * @return string|null
     */
    private function mapStatusToStage($status)
    {
        $statusToStageMap = [
            'submitted' => 'request',
            'app_owner_approval' => 'app_owner',
            'idi_approval' => 'idi',
            'security_approval' => 'security',
            'infrastructure_approval' => 'infrastructure'
        ];

        return $statusToStageMap[$status] ?? null;
    }

    /**
     * Calculate business hours between two dates considering SLA configuration and holidays
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param string $stage
     * @return float
     */
    public function calculateBusinessHours(Carbon $start, Carbon $end, string $stage)
    {
        // Get SLA configuration for this stage
        $slaConfig = SlaConfiguration::where('stage', $stage)->first();

        if (!$slaConfig) {
            // Default to including only business days
            $includeWeekends = false;
        } else {
            $includeWeekends = $slaConfig->include_weekends;
        }

        // Get all holidays between start and end dates
        $holidays = Holiday::where(function($query) use ($start, $end) {
            // One-time holidays
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->where('recurring', false);
        })
            ->orWhere(function($query) use ($start, $end) {
                // Recurring holidays (check month and day)
                $query->where('recurring', true);
            })
            ->get();

        $businessHours = 0;
        $current = $start->copy();

        while ($current->lt($end)) {
            $isWeekend = $current->isWeekend();
            $isHoliday = false;

            // Check if current date is a holiday
            foreach ($holidays as $holiday) {
                if ($holiday->recurring) {
                    // Check month and day for recurring holidays
                    if ($holiday->date->month == $current->month && $holiday->date->day == $current->day) {
                        $isHoliday = true;
                        break;
                    }
                } else {
                    // Check exact date for one-time holidays
                    if ($holiday->date->isSameDay($current)) {
                        $isHoliday = true;
                        break;
                    }
                }
            }

            if (($includeWeekends || !$isWeekend) && !$isHoliday) {
                // Count this hour if it's not a weekend (unless weekends are included) and not a holiday
                $businessHours++;
            }

            $current->addHour();
        }

        return $businessHours;
    }

    /**
     * Send warning notification to appropriate users
     *
     * @param Integration $integration
     * @param string $stage
     * @param float $hoursInStage
     * @param SlaConfiguration $slaConfig
     * @return void
     */
    private function sendWarningNotification(Integration $integration, $stage, $hoursInStage, $slaConfig)
    {
        $users = $this->getUsersForStage($stage);

        Log::info("Sending SLA warning notifications for integration #{$integration->id} at stage {$stage}");

        foreach ($users as $user) {
            // Only send if we haven't already sent a warning recently (check meta data)
            $lastWarning = $integration->getMetaData('last_warning_' . $stage . '_' . $user->id);

            if (!$lastWarning || Carbon::parse($lastWarning)->diffInHours(now()) > 8) {
                // Send warning email
                Mail::to($user)->send(new SlaWarningMail($integration, $stage, $hoursInStage, $slaConfig));

                // Store metadata to avoid sending too many warnings
                $integration->setMetaData('last_warning_' . $stage . '_' . $user->id, now());

                Log::info("Sent SLA warning to user #{$user->id} ({$user->email}) for integration #{$integration->id}");
            }
        }
    }

    /**
     * Send critical notification to appropriate users and their supervisors
     *
     * @param Integration $integration
     * @param string $stage
     * @param float $hoursInStage
     * @param SlaConfiguration $slaConfig
     * @return void
     */
    private function sendCriticalNotification(Integration $integration, $stage, $hoursInStage, $slaConfig)
    {
        $users = $this->getUsersForStage($stage);

        // Add administrators to critical notifications
        $administrators = User::role('administrator')->get();
        $users = $users->merge($administrators)->unique('id');

        Log::info("Sending SLA critical notifications for integration #{$integration->id} at stage {$stage}");

        foreach ($users as $user) {
            // Only send if we haven't already sent a critical alert recently
            $lastCritical = $integration->getMetaData('last_critical_' . $stage . '_' . $user->id);

            if (!$lastCritical || Carbon::parse($lastCritical)->diffInHours(now()) > 12) {
                // Send critical email
                Mail::to($user)->send(new SlaCriticalMail($integration, $stage, $hoursInStage, $slaConfig));

                // Store metadata to avoid sending too many alerts
                $integration->setMetaData('last_critical_' . $stage . '_' . $user->id, now());

                Log::info("Sent SLA critical alert to user #{$user->id} ({$user->email}) for integration #{$integration->id}");
            }
        }
    }

    /**
     * Get users responsible for the current stage
     *
     * @param string $stage
     * @return \Illuminate\Support\Collection
     */
    private function getUsersForStage($stage)
    {
        $permissionMap = [
            'request' => 'view integration',  // Requester
            'app_owner' => 'approve as app owner',
            'idi' => 'approve as idi',
            'security' => 'approve as security',
            'infrastructure' => 'approve as infrastructure'
        ];

        $permission = $permissionMap[$stage] ?? null;

        if ($permission) {
            return User::permission($permission)->get();
        }

        return collect([]);
    }
}
