<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Organization extends Model
{
    use HasUuids;

    protected $fillable = ['parent_organization_id', 'name', 'slug', 'type', 'is_active'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'parent_organization_id');
    }

    public function subsidiaries(): HasMany
    {
        return $this->hasMany(Organization::class, 'parent_organization_id');
    }

    public function branding(): HasOne
    {
        return $this->hasOne(OrganizationBranding::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class);
    }

    /** Subsidiaries this org has been explicitly granted access to view. */
    public function accessibleSubsidiaries(): HasMany
    {
        return $this->hasMany(OrganizationAccessGrant::class, 'parent_organization_id');
    }

    public function hasAccessTo(Organization $subsidiary): bool
    {
        return $this->accessibleSubsidiaries()
            ->where('subsidiary_organization_id', $subsidiary->id)
            ->exists();
    }
}
