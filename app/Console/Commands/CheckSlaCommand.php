<?php

namespace App\Console\Commands;

use App\Services\SlaTrackingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSlaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration:check-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all integrations against SLA thresholds and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting SLA check for all integrations...');

        try {
            $slaService = app(SlaTrackingService::class);
            $slaService->checkAllIntegrations();

            $this->info('SLA check completed successfully.');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error checking SLAs: ' . $e->getMessage());
            Log::error('SLA check failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return self::FAILURE;
        }
    }
}
