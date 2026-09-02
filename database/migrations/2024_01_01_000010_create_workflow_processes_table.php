<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Generalized workflow engine: usable by both contracts AND procurement tenders
        // via the polymorphic workflowable_type / workflowable_id pair below.
        Schema::create('workflow_processes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->uuidMorphs('workflowable'); // contract or procurement_tender
            $table->string('status')->default('in_progress'); // in_progress | approved | rejected
            $table->unsignedInteger('current_step_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_processes');
    }
};
