<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AcceptInvitationRequest;
use App\Models\Invitation;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AcceptInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        abort_if($invitation->isAccepted(), 410, 'This invitation has already been used.');
        abort_if($invitation->isExpired(), 410, 'This invitation has expired.');

        return view('auth.accept-invitation', ['invitation' => $invitation]);
    }

    public function store(AcceptInvitationRequest $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        abort_if($invitation->isAccepted(), 410, 'This invitation has already been used.');
        abort_if($invitation->isExpired(), 410, 'This invitation has expired.');

        // Re-check the seat limit at accept time too -- the org could have
        // filled its last seat with someone else between invite and accept.
        $subscription = $invitation->organization->subscription;

        if (! $subscription || ! $subscription->hasAvailableSeat()) {
            abort(409, 'No seats available on this organization\'s plan anymore.');
        }

        $user = DB::transaction(function () use ($invitation, $request, $subscription) {
            $user = User::create([
                'organization_id' => $invitation->organization_id,
                'name' => $request->validated('name'),
                'email' => $invitation->email,
                'password' => Hash::make($request->validated('password')),
                'role' => $invitation->role,
                'is_active' => true,
            ]);

            Seat::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
            ]);

            $invitation->update(['accepted_at' => now()]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
