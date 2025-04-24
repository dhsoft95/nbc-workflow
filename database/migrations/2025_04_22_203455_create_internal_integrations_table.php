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
        Schema::create('internal_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->string('middleware_connection')->nullable();
            $table->boolean('cms_binding')->default(false);
            $table->text('cms_binding_details')->nullable();
            $table->text('api_specifications')->nullable();
            $table->string('security_classification')->nullable();
            $table->string('responsible_team')->nullable(); // Digital Team, IDI Team
            $table->json('features_supported')->nullable(); // Stored as JSON for multi-select
            $table->json('system_dependencies')->nullable(); // Stored as JSON for searchable selection
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_integrations');
    }
};
