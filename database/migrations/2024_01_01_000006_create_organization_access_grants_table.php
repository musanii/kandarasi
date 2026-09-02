<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Explicit, opt-in visibility: a parent org can see a subsidiary's contracts
        // ONLY if a row exists here. No row = no visibility. Never implicit.
        Schema::create('organization_access_grants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('subsidiary_organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('scope')->default('read'); // read | read_reports_only
            $table->foreignUuid('granted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['parent_organization_id', 'subsidiary_organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_access_grants');
    }
};
