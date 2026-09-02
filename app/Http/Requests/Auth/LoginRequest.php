<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate, scoped to the organization resolved from the
     * subdomain. A user whose organization_id doesn't match the current
     * tenant is rejected with the SAME generic error as a wrong password --
     * never reveal that the email exists under a different organization.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $tenant = app(TenantContext::class)->get();

        $user = User::where('email', $this->input('email'))
            ->when($tenant, fn ($q) => $q->where('organization_id', $tenant->id))
            ->first();

        if (! $user || ! $user->is_active || ! Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Auth::login($user, $this->boolean('remember'));
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
