<?php

namespace App\Mail;

use App\Models\Integration;
use App\Models\SlaConfiguration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SlaCriticalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $integration;
    public $stage;
    public $hoursInStage;
    public $slaConfig;
    public $stageNames = [
        'request' => 'Initial Request',
        'app_owner' => 'App Owner Approval',
        'idi' => 'IDI Team Approval',
        'security' => 'Security Team Approval',
        'infrastructure' => 'Infrastructure Team Approval'
    ];

    /**
     * Create a new message instance.
     *
     * @param Integration $integration
     * @param string $stage
     * @param float $hoursInStage
     * @param SlaConfiguration $slaConfig
     */
    public function __construct(Integration $integration, $stage, $hoursInStage, SlaConfiguration $slaConfig)
    {
        $this->integration = $integration;
        $this->stage = $stage;
        $this->hoursInStage = $hoursInStage;
        $this->slaConfig = $slaConfig;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $stageName = $this->stageNames[$this->stage] ?? ucfirst($this->stage);

        return new Envelope(
            subject: "🚨 CRITICAL SLA ALERT: Integration '{$this->integration->name}' Requires Immediate Attention",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sla-critical',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
