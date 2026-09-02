<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContractTypeController extends Controller
{
    public function index(): View
    {
        return view('settings.contract-types', [
            'types' => ContractType::orderBy('name')->withCount('contracts')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->isOrgAdmin(), 403);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique(ContractType::class, 'name')->where('organization_id', Auth::user()->organization_id),
            ],
        ]);

        ContractType::create([
            'organization_id' => Auth::user()->organization_id,
            'name' => $validated['name'],
        ]);

        return back()->with('status', 'Contract type added.');
    }

    public function destroy(string $contractType): RedirectResponse
    {
        abort_unless(Auth::user()->isOrgAdmin(), 403);

        $contractType = ContractType::findOrFail($contractType);

        if ($contractType->contracts()->exists()) {
            return back()->withErrors(['name' => 'Cannot remove a type that is still used by existing contracts.']);
        }

        $contractType->delete();

        return back()->with('status', 'Contract type removed.');
    }
}
