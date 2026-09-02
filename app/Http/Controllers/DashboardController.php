<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Support\Tenancy\TenantContext;
use Illuminate\View\View;

/**
 * The dashboard is the landing page on login (locked MVP decision -- no
 * single narrow wedge feature, org-wide from day one). Kept intentionally
 * light on v1's mistake of sinking most of the layout into a full calendar
 * grid -- expiries/approvals get priority, events are a compact list.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $tenant = app(TenantContext::class)->get();

        // Contract queries are already scoped by BelongsToOrganization --
        // no manual organization_id filter needed here.
        $stats = [
            'pending_expiries' => Contract::where('status', 'pending_renewal')->count(),
            'contract_drafts' => Contract::where('status', 'drafting')->count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'pending_approvals' => Contract::where('status', 'pending_approval')->count(),
        ];

        $recentContracts = Contract::latest()->limit(8)->get();

        return view('dashboard', [
            'organization' => $tenant,
            'branding' => $tenant?->branding,
            'stats' => $stats,
            'recentContracts' => $recentContracts,
        ]);
    }
}
