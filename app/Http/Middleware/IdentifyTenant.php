<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the organization from the request subdomain (e.g. "nexcore" from
 * nexcore.kandarasi.app) and binds it into TenantContext for the rest of the
 * request lifecycle. Run this BEFORE auth middleware on tenant-scoped routes.
 *
 * A request to the bare apex domain (kandarasi.app, no subdomain) is treated
 * as the marketing/signup site -- not a tenant -- and passes through with no
 * organization bound.
 */
class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // e.g. "nexcore.kandarasi.app" -> ["nexcore", "kandarasi", "app"]
        // Anything with fewer than 3 parts (kandarasi.app, localhost, an IP)
        // has no subdomain -- treat as the non-tenant marketing/signup site.
        if (count($parts) < 3) {
            return $next($request);
        }

        $slug = $parts[0];

        $organization = Organization::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $organization) {
            abort(404, 'No organization found for this address.');
        }

        app(TenantContext::class)->set($organization);

        // Route::domain('{organization}.kandarasi.test') makes "organization"
        // a required parameter for every named route in that group -- without
        // this, route('login') etc. would throw UrlGenerationException asking
        // for it explicitly on every call, everywhere in every view.
        URL::defaults(['organization' => $slug]);

        return $next($request);
    }
}
