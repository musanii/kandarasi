<?php

use App\Http\Controllers\Auth\OrganizationRegistrationController;
use Illuminate\Support\Facades\Route;

// Registered under Route::domain(config('app.apex_domain')) in web.php --
// i.e. ONLY the bare apex (kandarasi.app / kandarasi.test), never a
// subdomain. A request to nexcore.kandarasi.test should never be able to
// reach the signup form for a brand-new org.
Route::middleware('guest')->group(function () {
    Route::get('signup', [OrganizationRegistrationController::class, 'create'])->name('signup');
    Route::post('signup', [OrganizationRegistrationController::class, 'store']);
});
