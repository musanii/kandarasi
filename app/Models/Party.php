<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'name', 'type', 'contact_email', 'contact_phone'];

    public function contractParties(): HasMany
    {
        return $this->hasMany(ContractParty::class);
    }
}
