<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('workflow_process_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step_order');
            $table->string('name');
            $table->unsignedInteger('min_approvers')->default(1);
            $table->boolean('mandatory')->default(true);
            $table->string('status')->default('pending'); // pending | approved | rejected | skipped
            $table->timestamps();

            $table->unique(['workflow_process_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
