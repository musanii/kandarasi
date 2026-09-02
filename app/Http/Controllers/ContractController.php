<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\OrganizationUnit;
use App\Models\Party;
use App\Support\Currencies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * All queries here are already scoped to the current organization via
 * BelongsToOrganization's global scope -- no manual organization_id
 * filtering needed in this controller, which is the whole point of
 * enforcing isolation at the model layer instead of per-controller.
 */
class ContractController extends Controller
{
    public function index(): View
    {
        return view('contracts.index', [
            'contracts' => Contract::with('organizationUnit', 'contractType')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('contracts.create', [
            'units' => OrganizationUnit::orderBy('name')->get(),
            'types' => ContractType::orderBy('name')->get(),
            'parties' => Party::orderBy('name')->get(),
            'currencies' => Currencies::options(),
        ]);
    }

    public function store(ContractRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $parties = $validated['parties'] ?? [];
        unset($validated['parties']);

        $contract = DB::transaction(function () use ($validated, $parties) {
            $contract = Contract::create([
                ...$validated,
                'status' => 'drafting',
                'currency' => $validated['currency'] ?? 'KES',
                'created_by_user_id' => Auth::id(),
            ]);

            foreach ($parties as $partyInput) {
                if (! empty($partyInput['party_id'])) {
                    // Selected from the reusable directory -- snapshot the
                    // directory's current details onto this contract's row
                    // (so later edits to the directory entry don't silently
                    // rewrite history on contracts already signed).
                    $directoryParty = Party::find($partyInput['party_id']);

                    $contract->parties()->create([
                        'party_id' => $directoryParty->id,
                        'name' => $directoryParty->name,
                        'role' => $partyInput['role'] ?? null,
                        'contact_email' => $directoryParty->contact_email,
                        'contact_phone' => $directoryParty->contact_phone,
                    ]);
                } elseif (! empty($partyInput['name'])) {
                    // Ad-hoc, one-off party not (yet) in the directory.
                    $contract->parties()->create([
                        'name' => $partyInput['name'],
                        'role' => $partyInput['role'] ?? null,
                        'contact_email' => $partyInput['contact_email'] ?? null,
                    ]);
                }
            }

            return $contract;
        });

        return redirect()->route('contracts.show', $contract)->with('status', 'Contract created.');
    }

    public function show(string $contract): View
    {
        // Explicit lookup rather than implicit route-model-binding -- the
        // global scope from BelongsToOrganization still applies here (it's
        // enforced on the Eloquent query, not on binding resolution), so
        // this is just as safe, and sidesteps whatever's blocking implicit
        // binding on this route.
        $contract = Contract::with('parties', 'organizationUnit', 'contractType', 'workflowProcess.steps', 'caseLogs')
            ->findOrFail($contract);

        return view('contracts.show', ['contract' => $contract]);
    }
}
