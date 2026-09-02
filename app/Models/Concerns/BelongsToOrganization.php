<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Enforces tenant isolation at the QUERY layer, not just the UI.
 *
 * Every model using this trait automatically scopes to the current
 * organization. Resolution order:
 *   1. TenantContext -- set by IdentifyTenant middleware from the subdomain,
 *      available even before authentication (e.g. a branded login page).
 *   2. auth()->user()->organization_id -- fallback for contexts without
 *      subdomain resolution (queue jobs, console commands).
 *
 * A subsidiary user can never see a sibling subsidiary's rows this way, and
 * cross-org visibility only happens through an explicit
 * OrganizationAccessGrant check in a service class -- never by omission here.
 */
trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $orgId = app(TenantContext::class)->id() ?? auth()->user()?->organization_id;

            if ($orgId) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', $orgId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $model->organization_id = app(TenantContext::class)->id()
                    ?? auth()->user()?->organization_id;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
