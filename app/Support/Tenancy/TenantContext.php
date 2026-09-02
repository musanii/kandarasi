<?php

namespace App\Support\Tenancy;

use App\Models\Organization;

/**
 * Holds the organization resolved from the request's subdomain for the
 * duration of the request -- bound as a singleton in the container.
 *
 * This exists specifically so pre-authentication pages (a branded login
 * screen at nexcore.kandarasi.app/login) can know which org they're
 * rendering for before there's an authenticated user to read
 * organization_id from.
 */
class TenantContext
{
    protected ?Organization $organization = null;

    public function set(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function get(): ?Organization
    {
        return $this->organization;
    }

    public function id(): ?string
    {
        return $this->organization?->id;
    }

    public function has(): bool
    {
        return $this->organization !== null;
    }
}
