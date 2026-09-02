<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Each organization -- including each subsidiary -- has its own subscription,
            // never a shared pool with a parent org.
            $table->foreignUuid('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('plan')->default('trial');
            $table->unsignedInteger('seat_limit')->default(5);
            $table->string('status')->default('active'); // active | past_due | canceled | trialing
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
