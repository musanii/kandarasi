<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_tenders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('estimated_value', 14, 2)->nullable();
            $table->string('status')->default('draft');
            // draft | published | evaluation | awarded | closed | disposed
            $table->timestamp('submission_deadline')->nullable();
            $table->foreignUuid('resulting_contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_tenders');
    }
};
