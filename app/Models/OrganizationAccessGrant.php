<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationAccessGrant extends Model
{
    use HasUuids;

    protected $fillable = ['parent_organization_id', 'subsidiary_organization_id', 'scope', 'granted_by_user_id'];

    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'parent_organization_id');
    }

    public function subsidiaryOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'subsidiary_organization_id');
    }
}
