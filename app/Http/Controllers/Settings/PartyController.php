<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function index(): View
    {
        return view('settings.parties', [
            'parties' => Party::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['organization', 'individual', 'vendor', 'client'])],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
        ]);

        Party::create(['organization_id' => Auth::user()->organization_id, ...$validated]);

        return back()->with('status', 'Party added to your directory.');
    }

    public function destroy(string $party): RedirectResponse
    {
        $party = Party::findOrFail($party);

        if ($party->contractParties()->exists()) {
            return back()->withErrors(['name' => 'Cannot remove a party still referenced on existing contracts.']);
        }

        $party->delete();

        return back()->with('status', 'Party removed.');
    }
}
