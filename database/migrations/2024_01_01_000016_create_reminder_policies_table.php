<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A policy with contract_id = null is the ORG-LEVEL DEFAULT.
        // A policy with contract_id set OVERRIDES the default for just that contract.
        // Replaces v1's hardcoded WeeklyReminder / MonthlyReminder / YearlyReminder classes
        // with one configurable table each organization tunes for itself.
        Schema::create('reminder_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('contract_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('offsets_days'); // e.g. [90, 30, 7] -- days before expiry
            $table->json('channels');     // e.g. ["email", "sms"]
            $table->boolean('digest')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_policies');
    }
};
