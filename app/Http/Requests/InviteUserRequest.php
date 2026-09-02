<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOrgAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required', 'email',
                Rule::unique(User::class, 'email'),
                Rule::unique(Invitation::class, 'email')
                    ->where('organization_id', $this->user()->organization_id)
                    ->whereNull('accepted_at'),
            ],
            'role' => ['required', Rule::in(['member', 'approver', 'org_admin', 'auditor'])],
        ];
    }
}
