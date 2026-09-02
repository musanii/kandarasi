<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\Settings\ContractTypeController;
use App\Http\Controllers\Settings\OrganizationUnitController;
use App\Http\Controllers\Settings\PartyController;
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

    Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function () {
            return view('settings.index');
        })->name('index');

        Route::get('contract-types', [ContractTypeController::class, 'index'])->name('contract-types.index');
        Route::post('contract-types', [ContractTypeController::class, 'store'])->name('contract-types.store');
        Route::delete('contract-types/{contractType}', [ContractTypeController::class, 'destroy'])->name('contract-types.destroy');

        Route::get('units', [OrganizationUnitController::class, 'index'])->name('units.index');
        Route::post('units', [OrganizationUnitController::class, 'store'])->name('units.store');
        Route::delete('units/{organizationUnit}', [OrganizationUnitController::class, 'destroy'])->name('units.destroy');

        Route::get('parties', [PartyController::class, 'index'])->name('parties.index');
        Route::post('parties', [PartyController::class, 'store'])->name('parties.store');
        Route::delete('parties/{party}', [PartyController::class, 'destroy'])->name('parties.destroy');
    });
});

// Accept-invitation is deliberately OUTSIDE the 'auth' group -- the
// invitee doesn't have an account yet, that's the whole point.
Route::middleware('guest')->group(function () {
    Route::get('invite/{token}', [AcceptInvitationController::class, 'show'])->name('invitations.accept');
    Route::post('invite/{token}', [AcceptInvitationController::class, 'store'])->name('invitations.accept.store');
});
