<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /** Show the branded login page for the current tenant. */
    public function create(): \Illuminate\View\View
    {
        $tenant = app(TenantContext::class)->get();

        // If no tenant resolved (apex domain, no subdomain), this route
        // shouldn't be reachable -- IdentifyTenant would already have
        // passed through without setting one, so bounce to the marketing
        // site's own login/signup entry instead.
        abort_if(! $tenant, 404);

        return view('auth.login', [
            'organization' => $tenant,
            'branding' => $tenant->branding,
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
