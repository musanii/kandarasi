<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationUnitController extends Controller
{
    public function index(): View
    {
        return view('settings.units', [
            'units' => OrganizationUnit::orderBy('name')->withCount('contracts')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->isOrgAdmin(), 403);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique(OrganizationUnit::class, 'name')->where('organization_id', Auth::user()->organization_id),
            ],
        ]);

        OrganizationUnit::create([
            'organization_id' => Auth::user()->organization_id,
            'name' => $validated['name'],
        ]);

        return back()->with('status', 'Department/unit added.');
    }

    public function destroy(string $organizationUnit): RedirectResponse
    {
        abort_unless(Auth::user()->isOrgAdmin(), 403);

        $unit = OrganizationUnit::findOrFail($organizationUnit);

        if ($unit->contracts()->exists()) {
            return back()->withErrors(['name' => 'Cannot remove a unit that is still used by existing contracts.']);
        }

        $unit->delete();

        return back()->with('status', 'Unit removed.');
    }
}
