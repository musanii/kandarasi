<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Tenant subdomain group -- IdentifyTenant (already prepended to the web
// middleware group in bootstrap/app.php) resolves the organization from
// the subdomain before any of these routes run.
Route::domain('{organization}.' . config('app.tenant_base_domain', 'kandarasi.test'))
    ->group(function () {
        require base_path('routes/auth.php');
        require base_path('routes/tenant.php');
    });

// Apex domain only (kandarasi.test itself, no subdomain) -- self-serve
// org signup lives here, deliberately unreachable from any tenant subdomain.
Route::domain(config('app.tenant_base_domain', 'kandarasi.test'))
    ->group(base_path('routes/marketing.php'));
