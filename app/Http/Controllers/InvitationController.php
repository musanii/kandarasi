<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteUserRequest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Org-admin-only: invite a teammate within their OWN organization.
 * Enforces the seat limit at invite time (not at accept time) so an admin
 * gets an immediate, clear error rather than a confusing failure once the
 * invitee tries to accept.
 */
class InvitationController extends Controller
{
    public function index(): View
    {
        $organization = Auth::user()->organization;

        return view('invitations.index', [
            'organization' => $organization,
            'subscription' => $organization->subscription,
            'invitations' => Invitation::whereNull('accepted_at')->latest()->get(),
        ]);
    }

    public function store(InviteUserRequest $request): RedirectResponse
    {
        $user = $request->user();
        $subscription = $user->organization->subscription;

        if (! $subscription || ! $subscription->hasAvailableSeat()) {
            return back()->withErrors([
                'email' => 'No seats available on your current plan. Upgrade your subscription or remove an existing user before inviting another.',
            ]);
        }

        Invitation::create([
            'organization_id' => $user->organization_id,
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'invited_by_user_id' => $user->id,
        ]);

        // Email delivery for the invite link is not wired yet -- see README.
        // The invitation row (with its token) exists and AcceptInvitationController
        // is ready to consume it once a mailer is configured.

        return back()->with('status', 'Invitation created.');
    }
}
