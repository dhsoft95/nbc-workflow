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
use Illuminate\Support\Facades\Mail;
use App\Mail\IntegrationStatusChanged;

class SendApprovalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The integration instance.
     *
     * @var \App\Models\Integration
     */
    protected $integration;

    /**
     * The user to notify.
     *
     * @var \App\Models\User|null
     */
    protected $user;

    /**
     * The email to send the notification to (used if user is null)
     *
     * @var string|null
     */
    protected $email;

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
    protected $stage;

    /**
     * The stage the integration was returned to (if applicable).
     *
     * @var string|null
     */
    protected $returnToStage;

    /**
     * Number of attempts for this job.
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
     * @param  \App\Models\User|string|null  $userOrEmail
     * @param  string  $action
     * @param  string  $stage
     * @param  string|null  $returnToStage
     * @return void
     */
    public function __construct(
        Integration $integration,
                    $userOrEmail,
        string $action,
        string $stage,
        ?string $returnToStage = null
    ) {
        $this->integration = $integration;

        // Handle different types for the recipient parameter
        if ($userOrEmail instanceof User) {
            $this->user = $userOrEmail;
            $this->email = $userOrEmail->email;
        } elseif (is_string($userOrEmail) && filter_var($userOrEmail, FILTER_VALIDATE_EMAIL)) {
            $this->user = null;
            $this->email = $userOrEmail;
        } else {
            Log::warning('Invalid recipient for integration notification', [
                'integration_id' => $integration->id,
                'recipient_type' => gettype($userOrEmail),
                'recipient' => $userOrEmail
            ]);
            $this->user = null;
            $this->email = null;
        }

        $this->action = $action;
        $this->stage = $stage;
        $this->returnToStage = $returnToStage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Skip processing if we don't have a valid email
        if (empty($this->email)) {
            Log::error('Cannot send notification - no valid email address', [
                'integration_id' => $this->integration->id
            ]);
            return;
        }

        try {
            Log::info('Sending integration notification email', [
                'integration_id' => $this->integration->id,
                'user_id' => $this->user ? $this->user->id : 'N/A',
                'email' => $this->email,
                'action' => $this->action,
                'stage' => $this->stage
            ]);

            // Refresh the integration from database to get the latest data
            $this->integration = Integration::find($this->integration->id);

            // Refresh the user if we have one
            if ($this->user) {
                $this->user = User::find($this->user->id);

                // Update email in case it changed
                if ($this->user) {
                    $this->email = $this->user->email;
                }
            }

            // If we still have a valid email, send the notification
            if (!empty($this->email)) {
                Mail::to($this->email)
                    ->send(new IntegrationStatusChanged(
                        $this->integration,
                        $this->action,
                        $this->stage,
                        $this->returnToStage
                    ));

                Log::info('Integration notification email sent successfully');
            } else {
                Log::warning('Skipped sending email - no valid email after refresh');
            }

        } catch (\Exception $e) {
            Log::error('Failed to send integration notification email', [
                'integration_id' => $this->integration->id,
                'email' => $this->email,
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
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Integration notification job failed permanently', [
            'integration_id' => $this->integration->id,
            'email' => $this->email,
            'error' => $exception->getMessage()
        ]);

        // Could notify administrators or log to a monitoring system
    }
}
