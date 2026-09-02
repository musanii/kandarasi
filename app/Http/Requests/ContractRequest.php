<?php

namespace App\Http\Requests;

use App\Support\Currencies;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // any authenticated user in the org can draft a contract;
                     // approval (not creation) is where the workflow engine gates things
    }

    public function rules(): array
    {
        $orgId = $this->user()->organization_id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'contract_type_id' => [
                'required',
                Rule::exists('contract_types', 'id')->where('organization_id', $orgId),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'organization_unit_id' => [
                'nullable',
                Rule::exists('organization_units', 'id')->where('organization_id', $orgId),
            ],
            'value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', Rule::in(Currencies::codes())],
            'effective_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:effective_date'],
            'parties' => ['nullable', 'array'],
            'parties.*.party_id' => [
                'nullable',
                Rule::exists('parties', 'id')->where('organization_id', $orgId),
            ],
            'parties.*.name' => ['required_without:parties.*.party_id', 'nullable', 'string', 'max:255'],
            'parties.*.role' => ['nullable', 'string', 'max:100'],
            'parties.*.contact_email' => ['nullable', 'email'],
        ];
    }
}
