<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuration_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('configuration_categories');
            $table->foreignId('item_id')->nullable()->constrained('configuration_items');
            $table->string('action'); // added, updated, deleted, activated, deactivated
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_histories');
    }
};
