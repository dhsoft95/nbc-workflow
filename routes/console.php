<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('integration:check-sla', function () {
    $this->call('integration:check-sla');
})->purpose('Check all integrations against SLA thresholds and send notifications');
Schedule::command('integration:check-sla')->hourly();
