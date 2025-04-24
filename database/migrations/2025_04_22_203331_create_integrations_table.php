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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('purpose');
            $table->string('department');
            $table->enum('type', ['internal', 'external']);
            $table->enum('status', ['draft', 'submitted', 'app_owner_approval', 'idi_approval', 'security_approval', 'infrastructure_approval', 'approved', 'rejected'])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->text('priority_justification')->nullable();
            $table->text('resource_requirements')->nullable();
            $table->date('estimated_timeline')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
