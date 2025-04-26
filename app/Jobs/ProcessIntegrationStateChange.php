<?php

namespace App\Jobs;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessIntegrationStateChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The integration instance.
     *
     * @var \App\Models\Integration
     */
    protected $integration;

    /**
     * The action that occurred.
     *
     * @var string
     */
    protected $action;

    /**
     * The stage where the action occurred.
     *
     * @var string
     */
    protected $fromStage;

    /**
     * The stage the integration is moving to.
     *
     * @var string
     */
    protected $toStage;

    /**
     * The roles to notify
     *
     * @var array
     */
    protected $notifyRoles;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Integration  $integration
     * @param  string  $action
     * @param  string  $fromStage
     * @param  string  $toStage
     * @param  array   $notifyRoles
     * @return void
     */
    public function __construct(
        Integration $integration,
        string $action,
        string $fromStage,
        string $toStage,
        array $notifyRoles
    ) {
        $this->integration = $integration;
        $this->action = $action;
        $this->fromStage = $fromStage;
        $this->toStage = $toStage;
        $this->notifyRoles = $notifyRoles;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Processing integration state change', [
                'integration_id' => $this->integration->id,
                'action' => $this->action,
                'from_stage' => $this->fromStage,
                'to_stage' => $this->toStage,
                'notify_roles' => $this->notifyRoles
            ]);

            // Refresh the integration from database to get the latest data
            $this->integration = Integration::find($this->integration->id);

            // Find all users with the roles that need notification
            $usersToNotify = User::whereHas('roles', function ($query) {
                $query->whereIn('name', $this->notifyRoles);
            })->get();

            Log::info('Found users to notify', [
                'count' => $usersToNotify->count(),
                'user_ids' => $usersToNotify->pluck('id')->toArray()
            ]);

            // Dispatch individual notification jobs for each user
            foreach ($usersToNotify as $user) {
                SendApprovalNotification::dispatch(
                    $this->integration,
                    $user,
                    $this->action,
                    $this->fromStage,
                    $this->toStage
                );
            }

            // Perform any additional processing based on the state change
            $this->performAdditionalProcessing();

            Log::info('Integration state change processed successfully');
        } catch (\Exception $e) {
            Log::error('Failed to process integration state change', [
                'integration_id' => $this->integration->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Retry or fail based on attempts
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts() - 1] ?? 60);
            } else {
                $this->fail($e);
            }
        }
    }

    /**
     * Perform any additional processing based on the state change.
     * This method can be extended for specific business rules.
     *
     * @return void
     */
    protected function performAdditionalProcessing(): void
    {
        // Example: Update statistics, trigger integrations, etc.
        if ($this->action === 'approved' && $this->toStage === null) {
            // Final approval - could trigger additional processes
            Log::info('Integration fully approved - triggering final processes', [
                'integration_id' => $this->integration->id
            ]);

            // e.g., Update dashboards, trigger implementation tasks, etc.
        }

        // Additional business logic based on state transitions can be added here
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Integration state change job failed permanently', [
            'integration_id' => $this->integration->id,
            'error' => $exception->getMessage()
        ]);

        // Could notify administrators or log to a monitoring system
    }
}
