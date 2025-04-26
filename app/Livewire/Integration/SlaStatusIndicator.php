<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use App\Models\SlaConfiguration;
use App\Services\SlaTrackingService;
use Livewire\Component;

class SlaStatusIndicator extends Component
{
    public $integration;
    public $stage;
    public $hoursInStage;
    public $slaConfig;
    public $percentage;
    public $status = 'normal'; // normal, warning, critical

    public function mount(Integration $integration)
    {
        $this->integration = $integration;
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->stage = $this->integration->getCurrentApprovalStage();

        if (!$this->stage) {
            $this->status = 'normal';
            $this->hoursInStage = 0;
            $this->percentage = 0;
            return;
        }

        // Get SLA configuration for this stage
        $this->slaConfig = SlaConfiguration::where('stage', $this->stage)->first();

        if (!$this->slaConfig) {
            $this->status = 'normal';
            $this->hoursInStage = 0;
            $this->percentage = 0;
            return;
        }

        // Use the service to calculate business hours
        $slaService = app(SlaTrackingService::class);

        // Get last approval history for current stage
        $latestHistory = $this->integration->approvalHistories()
            ->where('stage', $this->stage)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestHistory) {
            $this->status = 'normal';
            $this->hoursInStage = 0;
            $this->percentage = 0;
            return;
        }

        // Calculate business hours in current stage
        $enteredStageAt = $latestHistory->created_at;
        $this->hoursInStage = $slaService->calculateBusinessHours($enteredStageAt, now(), $this->stage);

        // Calculate percentage of SLA used
        $this->percentage = min(100, round(($this->hoursInStage / $this->slaConfig->warning_hours) * 100));

        // Determine status based on hours
        if ($this->hoursInStage >= $this->slaConfig->critical_hours) {
            $this->status = 'critical';
        } elseif ($this->hoursInStage >= $this->slaConfig->warning_hours) {
            $this->status = 'warning';
        } else {
            $this->status = 'normal';
        }
    }

    public function getStageNameProperty()
    {
        $stageNames = [
            'request' => 'Initial Request',
            'app_owner' => 'App Owner Approval',
            'idi' => 'IDI Team Approval',
            'security' => 'Security Team Approval',
            'infrastructure' => 'Infrastructure Team Approval'
        ];

        return $stageNames[$this->stage] ?? ucfirst($this->stage);
    }

    public function render()
    {
        return view('livewire.integration.sla-status-indicator');
    }
}
