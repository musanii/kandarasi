<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasUuids;

    protected $fillable = ['organization_id', 'plan', 'seat_limit', 'status', 'current_period_ends_at'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function hasAvailableSeat(): bool
    {
        return $this->seats()->count() < $this->seat_limit;
    }
}
