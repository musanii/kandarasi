<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->foreignUuid('contract_type_id')->constrained('contract_types');
            $table->text('description')->nullable();
            $table->foreignUuid('organization_unit_id')->nullable()->constrained('organization_units')->nullOnDelete();
            $table->string('status')->default('drafting');
            // drafting | pending_approval | negotiating | approved | active | pending_renewal | expired
            $table->decimal('value', 14, 2)->nullable();
            $table->string('currency', 3)->default('KES');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignUuid('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
