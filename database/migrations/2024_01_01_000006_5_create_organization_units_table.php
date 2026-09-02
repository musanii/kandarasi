<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Departments/sub-units within an organization (HR, ICT, Finance,
        // Procurement, Marketing, etc. from the v1 dashboard's department
        // breakdown) -- self-referential so a unit can optionally nest under
        // another (e.g. a regional office under a national department).
        Schema::create('organization_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->uuid('parent_unit_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });

        // Same self-reference ordering issue as organizations.parent_organization_id --
        // added as a second step so the primary key exists before the FK references it.
        Schema::table('organization_units', function (Blueprint $table) {
            $table->foreign('parent_unit_id')
                ->references('id')->on('organization_units')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_units');
    }
};
