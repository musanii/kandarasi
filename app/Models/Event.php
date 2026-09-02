<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Event extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'eventable_type', 'eventable_id', 'type', 'title', 'starts_at', 'location'];

    protected $casts = ['starts_at' => 'datetime'];


    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }
}
