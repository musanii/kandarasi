<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderPolicy extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'contract_id', 'offsets_days', 'channels', 'digest'];

    protected $casts = [
        'offsets_days' => 'array',
        'channels' => 'array',
        'digest' => 'boolean',
    ];


    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function isOrgDefault(): bool
    {
        return is_null($this->contract_id);
    }
}
