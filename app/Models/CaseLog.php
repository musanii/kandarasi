<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseLog extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'contract_id', 'issue', 'description', 'status', 'raised_by_user_id'];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
