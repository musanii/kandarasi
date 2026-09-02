<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_organization_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('company'); // company | parastatal | subsidiary
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Self-referencing FK must be added AFTER the table (and its primary key)
        // fully exists -- Laravel compiles ->primary() as an "implied command" that
        // runs after the create() closure, but ->constrained() registers its FK
        // immediately. Inside the same Schema::create(), that means the FK statement
        // would run before the PK it depends on exists. Splitting into a second
        // Schema::table() call guarantees correct ordering.
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreign('parent_organization_id')
                ->references('id')->on('organizations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
