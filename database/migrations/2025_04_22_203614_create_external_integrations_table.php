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
        Schema::create('external_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->boolean('is_new_vendor')->default(true);
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->string('connection_method')->nullable(); // VPN, Internet, Direct Connect
            $table->text('network_requirements')->nullable();
            $table->string('authentication_method')->nullable(); // OAuth, API Key, Certificate
            $table->text('data_encryption_requirements')->nullable();
            $table->text('api_documentation_url')->nullable();
            $table->text('rate_limiting')->nullable();
            $table->json('data_formats')->nullable(); // JSON, XML, etc.
            $table->date('contract_expiration')->nullable();
            $table->text('sla_terms')->nullable();
            $table->boolean('legal_approval')->default(false);
            $table->boolean('compliance_approval')->default(false);
            $table->enum('sit_outcome', ['wip', 'successful', 'failed'])->nullable();
            $table->text('test_plan')->nullable();
            $table->text('issue_log')->nullable();
            $table->text('business_impact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_integrations');
    }
};
