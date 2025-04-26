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
        Schema::create('approval_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->string('stage'); // app_owner, idi, security, infrastructure
            $table->enum('action', ['approved', 'rejected', 'returned','submitted']);
            $table->foreignId('user_id')->constrained(); // Who performed the action
            $table->text('comments')->nullable();
            $table->string('return_to_stage')->nullable(); // If returned, which stage
            $table->timestamps(); // Created_at will be used for time tracking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_histories');
    }
};
