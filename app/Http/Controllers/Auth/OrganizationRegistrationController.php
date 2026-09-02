<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OrganizationRegistrationRequest;
use App\Models\Organization;
use App\Models\Seat;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Self-serve org creation, per the locked decision: anyone can pick a
 * subdomain slug and become that organization's org_admin instantly.
 * No platform-admin approval step -- that's deliberately deferred until
 * (if ever) an assisted/enterprise onboarding path is needed.
 */
class OrganizationRegistrationController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.register-organization');
    }

    public function store(OrganizationRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $organization = Organization::create([
                'name' => $validated['organization_name'],
                'slug' => $validated['slug'],
                'type' => 'company',
                'is_active' => true,
            ]);

            $subscription = Subscription::create([
                'organization_id' => $organization->id,
                'plan' => 'trial',
                'seat_limit' => 5,
                'status' => 'trialing',
                'current_period_ends_at' => now()->addDays(14),
            ]);

            $user = User::create([
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'org_admin',
                'is_active' => true,
            ]);

            Seat::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
            ]);

            return $user;
        });

        Auth::login($user);

        // Session cookie is shared across subdomains (SESSION_DOMAIN=.kandarasi.test),
        // so this redirect carries the authenticated session onto the new org's
        // own subdomain rather than requiring a second login immediately after signup.
        $baseDomain = config('app.tenant_base_domain', 'kandarasi.test');

        return redirect()->away(
            $request->getScheme() . '://' . $user->organization->slug . '.' . $baseDomain . '/dashboard'
        );
    }
}
