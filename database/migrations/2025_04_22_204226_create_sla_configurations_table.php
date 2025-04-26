<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sla_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('stage');
            $table->integer('warning_hours')->default(24);
            $table->integer('critical_hours')->default(48);
            $table->boolean('include_weekends')->default(false);
            $table->timestamps();

            // Add unique constraint on stage to ensure only one config per stage
            $table->unique('stage');
        });

        // Insert default SLA configurations
        $this->seedDefaultConfigurations();
    }

    /**
     * Seed default SLA configurations for each stage
     */
    private function seedDefaultConfigurations()
    {
        $stages = [
            [
                'stage' => 'request',
                'warning_hours' => 24,
                'critical_hours' => 48,
                'include_weekends' => false
            ],
            [
                'stage' => 'app_owner',
                'warning_hours' => 16,
                'critical_hours' => 32,
                'include_weekends' => false
            ],
            [
                'stage' => 'idi',
                'warning_hours' => 24,
                'critical_hours' => 48,
                'include_weekends' => false
            ],
            [
                'stage' => 'security',
                'warning_hours' => 32,
                'critical_hours' => 64,
                'include_weekends' => false
            ],
            [
                'stage' => 'infrastructure',
                'warning_hours' => 24,
                'critical_hours' => 48,
                'include_weekends' => false
            ]
        ];

        // Insert the records
        foreach ($stages as $config) {
            DB::table('sla_configurations')->insert([
                'stage' => $config['stage'],
                'warning_hours' => $config['warning_hours'],
                'critical_hours' => $config['critical_hours'],
                'include_weekends' => $config['include_weekends'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_configurations');
    }
};
