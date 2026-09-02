# Kandarasi — Laravel Starter Package

This is a hand-written scaffold of Kandarasi's core schema and models, matching the
locked decisions in the SRS and product spec. It is **not** a full Laravel install —
drop it into a fresh one.

## Setup

```bash
composer create-project laravel/laravel kandarasi
cd kandarasi
composer require laravel/sanctum spatie/laravel-permission owen-it/laravel-auditing
```

Then copy this package's contents in:

```bash
cp -r database/migrations/* /path/to/kandarasi/database/migrations/
cp -r app/Models/* /path/to/kandarasi/app/Models/
```

Set your DB connection to PostgreSQL in `.env`, then:

```bash
php artisan migrate
```

## Subdomain-per-org tenancy (nexcore.kandarasi.app)

No tenancy package — subdomain resolution is handled by `IdentifyTenant`
middleware + `TenantContext`, both included in this package. Wire them in:

**1. Register the middleware** in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(prepend: [
        \App\Http\Middleware\IdentifyTenant::class,
    ]);
})
```

**2. Bind `TenantContext` as a singleton** in `AppServiceProvider::register()`:

```php
$this->app->singleton(\App\Support\Tenancy\TenantContext::class);
```

**3. Local development** — subdomains need real DNS or `/etc/hosts` entries
per org, since browsers won't resolve `nexcore.kandarasi.test` on their own.
If you're using Laravel Herd or Valet, wildcard subdomains usually work out
of the box (`*.kandarasi.test`). Otherwise add entries manually:

```
127.0.0.1  kandarasi.test
127.0.0.1  nexcore.kandarasi.test
```

**4. The apex domain (`kandarasi.app`, no subdomain)** is treated as the
marketing/signup site, not a tenant — `IdentifyTenant` passes it through
with no organization bound. That's where a new org picks their subdomain
slug and signs up.

**5. Branding** — once `TenantContext` is populated, pull the org's
`organization_branding` row and inject `--primary-color` etc. as CSS custom
properties in your shared layout, falling back to Kandarasi's default
palette when a tenant hasn't set one.

## Seeding NexCore as the first tenant

`database/seeders/OrganizationSeeder.php` is included — it creates NexCore
Systems (slug `nexcore`), its branding (obsidian/volcanic-orange/electric-mint,
pulled from the existing NexCore design system), an internal subscription,
four `organization_units` (Engineering, Operations, Finance, Procurement),
an org-admin user, and the org-level default reminder policy (90/30/7 days,
email + SMS).

Run it directly, without touching your project's `DatabaseSeeder.php`:

```bash
php artisan db:seed --class=OrganizationSeeder
```

**Change the seeded admin password immediately** (`change-me-immediately` is
a placeholder, not meant to reach anything real) — either update it in the
seeder before running, or change it via `php artisan tinker` after.

Once seeded, `nexcore.kandarasi.test` (or whatever local domain you've set
up) should resolve through `IdentifyTenant` to this organization — a good
checkpoint before building the login page itself.

## Auth (subdomain-aware login)

`LoginRequest`, `AuthenticatedSessionController`, `routes/auth.php`, and a
minimal branded `resources/views/auth/login.blade.php` are included.

**1. Register the routes** — inside a domain-scoped group in `routes/web.php`:

```php
Route::domain('{organization}.kandarasi.test') // .kandarasi.app in production
    ->group(base_path('routes/auth.php'));
```

(`IdentifyTenant` middleware, already prepended to the `web` group per the
tenancy section above, resolves the organization before these routes run —
the `{organization}` route parameter here is just for Laravel's router, the
actual lookup happens in the middleware via `TenantContext`.)

**2. Critical: set the session cookie domain** in `.env`, or sessions will
break in confusing ways across subdomain navigation:

```
SESSION_DOMAIN=.kandarasi.test
```

(`.kandarasi.app` in production — the leading dot matters, it's what makes
the cookie valid across all subdomains rather than locked to one.)

**3. Test it** — after seeding NexCore, visit
`http://nexcore.kandarasi.test/login`. You should see NexCore's branding
(obsidian background, volcanic-orange button) rendered from
`organization_branding`, not hardcoded. Log in with the seeded admin
credentials (and change that password immediately, per the seeding section
above).

**Not yet built:** a `dashboard` route/view to redirect to after login (the
controller assumes one exists — `route('dashboard')` — add a placeholder or
the real dashboard next), and registration/invite flow (an org admin
inviting a new user, which is also where seat-limit enforcement via
`Subscription::hasAvailableSeat()` belongs).

## Self-serve organization signup

`OrganizationRegistrationRequest`, `OrganizationRegistrationController`,
`routes/marketing.php`, and `resources/views/auth/register-organization.blade.php`
are included — implementing the locked decision: anyone can pick a subdomain
slug and become that org's `org_admin` instantly. No platform-admin approval
step (deliberately deferred — see "Not yet built" below).

**1. Add a config key** for the base tenant domain, in `config/app.php`:

```php
'tenant_base_domain' => env('TENANT_BASE_DOMAIN', 'kandarasi.test'),
```

and in `.env`:

```
TENANT_BASE_DOMAIN=kandarasi.test
```

(`kandarasi.app` in production.)

**2. Register the signup routes on the APEX domain only** — in `routes/web.php`:

```php
Route::domain(config('app.tenant_base_domain'))
    ->group(base_path('routes/marketing.php'));
```

This is deliberate and important: `routes/marketing.php` is wrapped in
`Route::domain()` matching the *exact* apex host, not a wildcard — so
`nexcore.kandarasi.test/signup` does **not** resolve to this controller.
A tenant subdomain should never be able to reach the "create a new org"
form.

**3. Reserved slugs** — `OrganizationRegistrationRequest` blocks slugs like
`www`, `api`, `admin`, `app`, `dashboard`, `login`, `kandarasi` etc. from
being claimed as an org subdomain. Extend that list if you add more
apex-level routes later (anything that would collide with a real path).

**4. Flow:** signup creates the `Organization`, a `trial` `Subscription`
(14 days, 5 seats), the first `User` as `org_admin`, and their `Seat` — all
in one transaction — then logs them in and redirects to
`{slug}.{tenant_base_domain}/dashboard`. This redirect relies on
`SESSION_DOMAIN=.kandarasi.test` (set in the auth section above) so the
session carries across from the apex domain to the new subdomain without a
second login.

## Dashboard and team invites

`DashboardController` + `resources/views/dashboard.blade.php` — the landing
page after login/signup, per the locked "dashboard is the front door"
decision. Shows contract stats and a recent-contracts table; deliberately
skips a full calendar grid (v1's layout mistake, per the earlier dashboard
critique) — events belong in their own compact list, not this page.

`InvitationController` + `AcceptInvitationController` — org-admin-only
invite flow. **Seat-limit enforcement happens at invite time**
(`Subscription::hasAvailableSeat()`), with a second check at accept time in
case the last seat filled in between. An `Invitation` row is created either
way; **the actual email send is not wired** — no mailer/notification is
configured yet, so right now an admin would need to manually share the
`invite/{token}` URL. Wiring `Illuminate\Notifications\Notification` (or a
Mailable) to fire on `InvitationController::store()` is the natural next
step once you've picked a mail provider.

**Register `routes/tenant.php`** in `routes/web.php`, inside the same
`Route::domain(...)` group as `routes/auth.php`:

```php
Route::domain('{organization}.kandarasi.test')
    ->group(function () {
        require base_path('routes/auth.php');
        require base_path('routes/tenant.php');
    });
```

## Still not built

- **Platform Admin** — a separate NexCore-staff-only panel (own auth guard,
  apex domain, no default access to tenant data) for viewing/managing
  subscriptions across all orgs. Deferred since self-serve-only was the
  locked decision — revisit if/when assisted enterprise onboarding is needed.
- **Invitation emails** — the `Invitation` row and accept flow work; sending
  the actual email doesn't yet.
- **Trial-to-paid conversion** — nothing currently happens when
  `current_period_ends_at` passes.
- **Contract CRUD itself** — dashboard reads `Contract` rows but nothing yet
  creates one through the UI.

## What's here

- **`database/migrations/`** — every table from the product spec's data model section:
  organizations (self-referential for subsidiaries), organization_units (departments/
  sub-units), users, branding, subscriptions/seats, access grants, contracts + parties +
  documents, the generalized workflow engine (workflow_processes / workflow_steps /
  workflow_actions, polymorphic so it drives both contracts and procurement tenders),
  procurement (tenders, evaluation committees, tender evaluations), reminder_policies
  (org default + per-contract override) with user_notification_preferences layered on
  top, case_logs, audit_logs, and events.
- **`app/Models/`** — matching Eloquent models with relationships wired up.
- **`app/Models/Concerns/BelongsToOrganization.php`** — the tenant-isolation trait.
  Apply it to any model that must never leak across organizations (`Contract`,
  `ProcurementTender` already use it). It adds a global scope filtering every query
  to the current organization — resolved from the subdomain via `TenantContext` when
  available, falling back to the authenticated user. This is the fix for v1's manual,
  easy-to-forget per-controller ownership checks.
- **`app/Support/Tenancy/TenantContext.php`** + **`app/Http/Middleware/IdentifyTenant.php`**
  — subdomain-based tenant resolution, working before authentication.

## What's deliberately NOT here yet

- Auth scaffolding (Sanctum config, login/register controllers) — standard Laravel/
  Sanctum setup, no Kandarasi-specific logic needed.
- The `OrganizationAccessGrant` check for parent-org cross-subsidiary visibility —
  this is a service-layer concern (e.g. `ContractVisibilityService`), not something
  the global scope alone should handle, since it's opt-in and grant-scoped.
- Digital signature provider integration (routing to a licensed E-CSP vs. DocuSeal).
- AI service layer (clause extraction, natural-language query) — calls out to the
  Claude API and should live in `app/Services/AI/`, kept separate from the models.
- Seat-limit enforcement on login/invite (checks `Subscription::hasAvailableSeat()`,
  which exists on the model already — wire it into the invite/login flow).
- Frontend — Vue 3 + Inertia, not started yet.

## Seeding NexCore as the first tenant

Since NexCore is the pilot organization (not a placeholder "Company ABC"), the first
seeder should create:

```php
Organization::create(['name' => 'NexCore Systems', 'slug' => 'nexcore', 'type' => 'company']);
```

with branding pulled from the existing NexCore design system (obsidian #0C0910,
volcanic orange #F0521E, electric mint #00E5A0) as the default `organization_branding`
row — dogfooding the white-label branding feature from day one.
