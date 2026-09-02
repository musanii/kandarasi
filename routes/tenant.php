<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Auth\AcceptInvitationController;
use Illuminate\Support\Facades\Route;

// Everything here runs on a tenant subdomain (nexcore.kandarasi.test),
// AFTER IdentifyTenant has resolved the organization. Register alongside
// routes/auth.php inside the same Route::domain('{organization}...') group
// in web.php.

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('team', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('team/invite', [InvitationController::class, 'store'])->name('invitations.store');
});

// Accept-invitation is deliberately OUTSIDE the 'auth' group -- the
// invitee doesn't have an account yet, that's the whole point.
Route::middleware('guest')->group(function () {
    Route::get('invite/{token}', [AcceptInvitationController::class, 'show'])->name('invitations.accept');
    Route::post('invite/{token}', [AcceptInvitationController::class, 'store'])->name('invitations.accept.store');
});
