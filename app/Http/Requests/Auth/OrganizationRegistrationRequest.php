<?php

namespace App\Http\Requests\Auth;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OrganizationRegistrationRequest extends FormRequest
{
    // Slugs that would collide with real infrastructure or Kandarasi's own
    // pages if someone claimed them as an org subdomain.
    protected const RESERVED_SLUGS = [
        'www', 'api', 'app', 'admin', 'mail', 'ftp', 'blog', 'help',
        'support', 'status', 'docs', 'cdn', 'static', 'assets',
        'dashboard', 'login', 'signup', 'kandarasi',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:63',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', // lowercase, digits, single hyphens
                Rule::unique(Organization::class, 'slug'),
                Rule::notIn(self::RESERVED_SLUGS),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'Subdomain can only contain lowercase letters, numbers, and single hyphens.',
            'slug.not_in' => 'That subdomain is reserved. Please choose another.',
        ];
    }
}
