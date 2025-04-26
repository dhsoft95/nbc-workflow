<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->boolean('recurring')->default(false);
            $table->timestamps();
        });

        // Seed some common holidays
        $this->seedCommonHolidays();
    }

    /**
     * Seed common holidays
     */
    private function seedCommonHolidays()
    {
        $currentYear = date('Y');

        $holidays = [
            // Common US holidays as examples - replace with your country's holidays
            [
                'name' => 'New Year\'s Day',
                'date' => $currentYear . '-01-01',
                'recurring' => true
            ],
            [
                'name' => 'Independence Day',
                'date' => $currentYear . '-07-04',
                'recurring' => true
            ],
            [
                'name' => 'Christmas Day',
                'date' => $currentYear . '-12-25',
                'recurring' => true
            ],
            [
                'name' => 'Boxing Day',
                'date' => $currentYear . '-12-26',
                'recurring' => true
            ],
            // Example non-recurring specific holiday (like a one-time company-wide day off)
            [
                'name' => 'Company Retreat',
                'date' => $currentYear . '-10-15',
                'recurring' => false
            ]
        ];

        foreach ($holidays as $holiday) {
            DB::table('holidays')->insert([
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'recurring' => $holiday['recurring'],
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
        Schema::dropIfExists('holidays');
    }
};
