<?php

namespace App\Mail;

use App\Models\Integration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IntegrationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The integration instance.
     *
     * @var \App\Models\Integration
     */
    public $integration;

    /**
     * The action that occurred.
     *
     * @var string
     */
    public $action;

    /**
     * The stage where the action occurred.
     *
     * @var string
     */
    public $stage;

    /**
     * The stage the integration was returned to (if applicable).
     *
     * @var string|null
     */
    public $returnToStage;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Integration  $integration
     * @param  string  $action
     * @param  string  $stage
     * @param  string|null  $returnToStage
     * @return void
     */
    public function __construct(
        Integration $integration,
        string $action,
        string $stage,
        ?string $returnToStage = null
    ) {
        $this->integration = $integration;
        $this->action = $action;
        $this->stage = $stage;
        $this->returnToStage = $returnToStage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->getEmailSubject();

        return $this->subject($subject)
            ->markdown('emails.integration.status-changed')
            ->with([
                'integration' => $this->integration,
                'action' => $this->action,
                'stage' => $this->stageToReadable($this->stage),
                'returnToStage' => $this->returnToStage ? $this->stageToReadable($this->returnToStage) : null,
                'viewUrl' => route('integrations.show', $this->integration),
                'actionText' => $this->getActionText(),
                'actionColor' => $this->getActionColor(),
            ]);
    }

    /**
     * Get the email subject based on the action.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        $integrationName = $this->integration->name;

        switch ($this->action) {
            case 'approved':
                if ($this->integration->status === 'approved') {
                    return "Integration Request Approved: {$integrationName}";
                }
                return "Integration Ready for Next Review: {$integrationName}";

            case 'rejected':
                return "Integration Request Rejected: {$integrationName}";

            case 'returned':
                return "Integration Returned for Revision: {$integrationName}";

            default:
                return "Integration Status Update: {$integrationName}";
        }
    }

    /**
     * Get the call to action text based on the action.
     *
     * @return string
     */
    protected function getActionText()
    {
        switch ($this->action) {
            case 'approved':
                if ($this->integration->status === 'approved') {
                    return 'View Approved Integration';
                }
                return 'Review Integration';

            case 'rejected':
                return 'View Rejected Integration';

            case 'returned':
                return 'Review Returned Integration';

            default:
                return 'View Integration';
        }
    }

    /**
     * Get the action button color based on the action.
     *
     * @return string
     */
    protected function getActionColor()
    {
        switch ($this->action) {
            case 'approved':
                return 'success';

            case 'rejected':
                return 'danger';

            case 'returned':
                return 'warning';

            default:
                return 'primary';
        }
    }

    /**
     * Convert stage identifier to human-readable text.
     *
     * @param string $stage
     * @return string
     */
    protected function stageToReadable($stage)
    {
        $stageMap = [
            'request' => 'Request',
            'app_owner' => 'App Owner',
            'idi' => 'IDI Team',
            'security' => 'Security Team',
            'infrastructure' => 'Infrastructure Team',
        ];

        return $stageMap[$stage] ?? ucfirst($stage);
    }
}
