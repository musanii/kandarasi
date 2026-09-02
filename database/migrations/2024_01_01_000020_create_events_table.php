<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Deliberately lightweight (FR-19) -- deadlines and meetings tied to a
        // contract or tender, surfaced as a dashboard list, synced outward to
        // Google Calendar / Outlook rather than rebuilding a calendar UI.
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->uuidMorphs('eventable'); // contract or procurement_tender
            $table->string('type'); // submission_deadline | evaluation_meeting | review_meeting
            $table->string('title');
            $table->timestamp('starts_at');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
