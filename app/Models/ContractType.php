<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractType extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'name'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
