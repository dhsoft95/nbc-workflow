<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use App\Services\IntegrationApprovalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ShowRequest extends Component
{
    public $integration;
    public $approvalHistories;
    public $comment = '';

    // For debugging
    public $debug = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * Initialize the component
     *
     * @param Integration $integration
     * @return void
     */
    public function mount(Integration $integration)
    {
        $this->integration = $integration;
        $this->loadApprovalHistory();
    }

    /**
     * Load approval history records
     *
     * @return void
     */
    public function loadApprovalHistory()
    {
        $this->approvalHistories = $this->integration->approvalHistories()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Approve the integration
     *
     * @return void
     */
    public function approve()
    {
        $approvalService = app(IntegrationApprovalService::class);

        Log::info('Approval button clicked', [
            'integration_id' => $this->integration->id,
            'user_id' => Auth::id()
        ]);

        $result = $approvalService->approve($this->integration, Auth::user(), $this->comment);

        if ($result) {
            session()->flash('message', 'Integration request approved successfully!');
            // Refresh component data
            $this->integration = Integration::find($this->integration->id);
            $this->loadApprovalHistory();
            $this->comment = '';
        } else {
            session()->flash('error', 'Failed to approve the integration request.');
        }
    }

    /**
     * Reject the integration
     *
     * @return void
     */
    public function reject()
    {
        $approvalService = app(IntegrationApprovalService::class);

        Log::info('Reject button clicked', [
            'integration_id' => $this->integration->id,
            'user_id' => Auth::id()
        ]);

        $result = $approvalService->reject($this->integration, Auth::user(), $this->comment);

        if ($result) {
            session()->flash('message', 'Integration request rejected.');
            // Refresh component data
            $this->integration = Integration::find($this->integration->id);
            $this->loadApprovalHistory();
            $this->comment = '';
        } else {
            session()->flash('error', 'Failed to reject the integration request.');
        }
    }

    /**
     * Return the integration to a previous stage
     *
     * @param string $returnToStage
     * @return void
     */
    public function return($returnToStage)
    {
        $approvalService = app(IntegrationApprovalService::class);

        Log::info('Return button clicked', [
            'integration_id' => $this->integration->id,
            'return_to_stage' => $returnToStage,
            'user_id' => Auth::id()
        ]);

        $result = $approvalService->returnToStage($this->integration, Auth::user(), $returnToStage, $this->comment);

        if ($result) {
            session()->flash('message', 'Integration request returned for revision.');
            // Refresh component data
            $this->integration = Integration::find($this->integration->id);
            $this->loadApprovalHistory();
            $this->comment = '';
        } else {
            session()->flash('error', 'Failed to return the integration request.');
        }
    }

    /**
     * Check if current user can approve this integration
     *
     * @return bool
     */
    public function canApprove()
    {
        $approvalService = app(IntegrationApprovalService::class);
        return $approvalService->canApprove(Auth::user(), $this->integration);
    }

    /**
     * Get current stage in approval workflow
     *
     * @return string|null
     */
    public function getCurrentStage()
    {
        $approvalService = app(IntegrationApprovalService::class);
        return $approvalService->getCurrentStage($this->integration);
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $canApprove = $this->canApprove();
        $currentStage = $this->getCurrentStage();

        Log::info('Rendering show request component', [
            'integration_id' => $this->integration->id,
            'integration_name' => $this->integration->name,
            'integration_status' => $this->integration->status,
            'canApprove' => $canApprove,
            'currentStage' => $currentStage
        ]);

        return view('livewire.integration.show-request', [
            'canApprove' => $canApprove,
            'currentStage' => $currentStage,
            'debug' => $this->debug
        ])->layout('layouts.app', ['title' => 'Integration Request Details']);
    }
}
