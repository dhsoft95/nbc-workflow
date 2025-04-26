<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\ApprovalHistory;
use App\Models\User;
use App\Jobs\SendApprovalNotification;
use App\Jobs\ProcessIntegrationStateChange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IntegrationApprovalService
{
    /**
     * The approval workflow matrix defining the stages, roles, and transitions
     */
    protected $approvalMatrix = [
        'request' => [
            'roles' => ['app_owner', 'administrator'],
            'next' => 'app_owner',
            'status' => 'submitted',
            'next_status' => 'app_owner_approval',
            'notification_recipients' => ['app_owner']
        ],
        'app_owner' => [
            'roles' => ['idi_team', 'administrator'],
            'next' => 'idi',
            'status' => 'app_owner_approval',
            'next_status' => 'idi_approval',
            'notification_recipients' => ['idi_team']
        ],
        'idi' => [
            'roles' => ['security_team', 'administrator'],
            'next' => 'security',
            'status' => 'idi_approval',
            'next_status' => 'security_approval',
            'notification_recipients' => ['security_team']
        ],
        'security' => [
            'roles' => ['infrastructure_team', 'administrator'],
            'next' => 'infrastructure',
            'status' => 'security_approval',
            'next_status' => 'infrastructure_approval',
            'notification_recipients' => ['infrastructure_team']
        ],
        'infrastructure' => [
            'roles' => ['administrator'],
            'next' => null,
            'status' => 'infrastructure_approval',
            'next_status' => 'approved',
            'notification_recipients' => ['requester', 'administrator']
        ]
    ];

    /**
     * Get the current stage based on integration status
     *
     * @param Integration $integration
     * @return string|null
     */
    public function getCurrentStage(Integration $integration)
    {
        $statusToStage = [
            'draft' => 'request',
            'submitted' => 'request',
            'app_owner_approval' => 'app_owner',
            'idi_approval' => 'idi',
            'security_approval' => 'security',
            'infrastructure_approval' => 'infrastructure',
            'rejected' => 'request',
            'approved' => 'infrastructure',
        ];

        $currentStage = $statusToStage[$integration->status] ?? null;

        Log::info('Getting current stage', [
            'integration_id' => $integration->id,
            'integration_status' => $integration->status,
            'current_stage' => $currentStage
        ]);

        return $currentStage;
    }

    /**
     * Get the next stage in the approval workflow
     *
     * @param string $currentStage
     * @return string|null
     */
    public function getNextStage($currentStage)
    {
        if (!isset($this->approvalMatrix[$currentStage])) {
            return null;
        }

        return $this->approvalMatrix[$currentStage]['next'];
    }

    /**
     * Check if a user can approve an integration at its current stage
     *
     * @param User $user
     * @param Integration $integration
     * @return bool
     */
    public function canApprove(User $user, Integration $integration)
    {
        // Administrators can approve anything
        if ($user->hasRole('administrator')) {
            return true;
        }

        $currentStage = $this->getCurrentStage($integration);
        if (!$currentStage || !isset($this->approvalMatrix[$currentStage])) {
            Log::warning('No current stage found or not in approval matrix', [
                'integration_id' => $integration->id,
                'status' => $integration->status,
                'stage' => $currentStage
            ]);
            return false;
        }

        // Check if user has any of the roles allowed to approve this stage
        $allowedRoles = $this->approvalMatrix[$currentStage]['roles'];
        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Approve an integration request
     *
     * @param Integration $integration
     * @param User $user
     * @param string|null $comments
     * @return bool
     */
    public function approve(Integration $integration, User $user, ?string $comments = null)
    {
        // Capture the current stage before any status changes
        $currentStage = $this->getCurrentStage($integration);
        if (!$currentStage) {
            Log::error('Cannot approve - current stage not determined', [
                'integration_id' => $integration->id,
                'status' => $integration->status
            ]);
            return false;
        }

        // Make sure the user can approve
        if (!$this->canApprove($user, $integration)) {
            Log::warning('User not authorized to approve', [
                'user_id' => $user->id,
                'integration_id' => $integration->id
            ]);
            return false;
        }

        try {
            // Determine the next stage and status
            $nextStage = $this->getNextStage($currentStage);
            $nextStatus = $nextStage
                ? $this->approvalMatrix[$nextStage]['status']
                : 'approved';

            // Create approval history record
            $history = new ApprovalHistory([
                'integration_id' => $integration->id,
                'stage' => $currentStage,
                'action' => 'approved',
                'user_id' => $user->id,
                'comments' => $comments,
            ]);
            $history->save();

            // Update integration status
            $integration->status = $nextStatus;
            $integration->save();

            // Dispatch notification job
            $this->dispatchNotifications($integration, $currentStage, 'approved', $nextStage);

            Log::info('Integration approved successfully', [
                'integration_id' => $integration->id,
                'from_stage' => $currentStage,
                'to_stage' => $nextStage,
                'new_status' => $integration->status
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error approving integration', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Reject an integration request
     *
     * @param Integration $integration
     * @param User $user
     * @param string|null $comments
     * @return bool
     */
    public function reject(Integration $integration, User $user, ?string $comments = null)
    {
        // Capture the current stage before any status changes
        $currentStage = $this->getCurrentStage($integration);
        if (!$currentStage) {
            Log::error('Cannot reject - current stage not determined', [
                'integration_id' => $integration->id,
                'status' => $integration->status
            ]);
            return false;
        }

        // Make sure the user can approve/reject
        if (!$this->canApprove($user, $integration)) {
            Log::warning('User not authorized to reject', [
                'user_id' => $user->id,
                'integration_id' => $integration->id
            ]);
            return false;
        }

        try {
            // Create rejection history record
            $history = new ApprovalHistory([
                'integration_id' => $integration->id,
                'stage' => $currentStage,
                'action' => 'rejected',
                'user_id' => $user->id,
                'comments' => $comments,
            ]);
            $history->save();

            // Update integration status
            $integration->status = 'rejected';
            $integration->save();

            // Dispatch notification job
            $this->dispatchNotifications($integration, $currentStage, 'rejected');

            Log::info('Integration rejected', [
                'integration_id' => $integration->id,
                'stage' => $currentStage,
                'user_id' => $user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error rejecting integration', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Return an integration request to a previous stage
     *
     * @param Integration $integration
     * @param User $user
     * @param string $returnToStage
     * @param string|null $comments
     * @return bool
     */
    public function returnToStage(Integration $integration, User $user, string $returnToStage, ?string $comments = null)
    {
        // Capture the current stage before any status changes
        $currentStage = $this->getCurrentStage($integration);
        if (!$currentStage) {
            Log::error('Cannot return - current stage not determined', [
                'integration_id' => $integration->id,
                'status' => $integration->status
            ]);
            return false;
        }

        // Make sure the user can approve/return
        if (!$this->canApprove($user, $integration)) {
            Log::warning('User not authorized to return integration', [
                'user_id' => $user->id,
                'integration_id' => $integration->id
            ]);
            return false;
        }

        // Make sure return stage is valid
        if (!isset($this->approvalMatrix[$returnToStage])) {
            Log::error('Invalid return stage', [
                'integration_id' => $integration->id,
                'return_stage' => $returnToStage
            ]);
            return false;
        }

        try {
            // Create return history record
            $history = new ApprovalHistory([
                'integration_id' => $integration->id,
                'stage' => $currentStage,
                'action' => 'returned',
                'user_id' => $user->id,
                'return_to_stage' => $returnToStage,
                'comments' => $comments,
            ]);
            $history->save();

            // Determine the status for the return stage
            $returnStatus = $this->approvalMatrix[$returnToStage]['status'];

            // Special case: if returning to request stage, set to draft
            if ($returnToStage === 'request') {
                $returnStatus = 'draft';
            }

            // Update integration status
            $integration->status = $returnStatus;
            $integration->save();

            // Dispatch notification job
            $this->dispatchNotifications($integration, $currentStage, 'returned', $returnToStage);

            Log::info('Integration returned to previous stage', [
                'integration_id' => $integration->id,
                'from_stage' => $currentStage,
                'to_stage' => $returnToStage,
                'new_status' => $integration->status
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error returning integration', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Dispatch notification jobs based on action and stage
     *
     * @param Integration $integration
     * @param string $currentStage
     * @param string $action
     * @param string|null $nextStage
     * @return void
     */
    protected function dispatchNotifications(Integration $integration, string $currentStage, string $action, ?string $nextStage = null)
    {
        // Notify requester on rejections and approvals
        if ($action === 'rejected' || ($action === 'approved' && $nextStage === null)) {
            // Final approval or rejection - notify requester
            SendApprovalNotification::dispatch(
                $integration,
                $integration->createdBy, // Requester
                $action,
                $currentStage
            );
        }

        // For approvals with next stage, notify the next approver role
        if ($action === 'approved' && $nextStage && isset($this->approvalMatrix[$nextStage])) {
            $notifyRoles = $this->approvalMatrix[$nextStage]['notification_recipients'];

            // Dispatch job to process notifications to the next stage's roles
            ProcessIntegrationStateChange::dispatch(
                $integration,
                $action,
                $currentStage,
                $nextStage,
                $notifyRoles
            );
        }

        // For returns, notify person who should receive it
        if ($action === 'returned' && $nextStage) {
            $notifyRoles = [];

            // If returned to requester, notify them
            if ($nextStage === 'request') {
                SendApprovalNotification::dispatch(
                    $integration,
                    $integration->createdBy, // Requester
                    $action,
                    $currentStage,
                    $nextStage
                );
            } else {
                // Otherwise notify the roles responsible for that stage
                $notifyRoles = $this->approvalMatrix[$nextStage]['roles'];

                // Dispatch job to process notifications
                ProcessIntegrationStateChange::dispatch(
                    $integration,
                    $action,
                    $currentStage,
                    $nextStage,
                    $notifyRoles
                );
            }
        }
    }
}
