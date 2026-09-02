<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * The reusable approval engine -- one implementation drives both contract
 * approval (workflowable = Contract) and procurement tender approval
 * (workflowable = ProcurementTender), matching how v1's "Workflows" nav
 * section was already conceptually separate from "Contracts".
 */
class WorkflowProcess extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'workflowable_type', 'workflowable_id', 'status', 'current_step_order'];

    public function workflowable(): MorphTo
    {
        return $this->morphTo();
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function currentStep(): ?WorkflowStep
    {
        return $this->steps()->where('step_order', $this->current_step_order)->first();
    }

    /** Advance to the next step once the current step's min_approvers is met. */
    public function advance(): void
    {
        $current = $this->currentStep();

        if (! $current) {
            return;
        }

        $approvals = $current->actions()->where('action', 'approve')->count();

        if ($approvals >= $current->min_approvers) {
            $current->update(['status' => 'approved']);

            $next = $this->steps()->where('step_order', '>', $this->current_step_order)->first();

            if ($next) {
                $this->update(['current_step_order' => $next->step_order]);
            } else {
                $this->update(['status' => 'approved']);
            }
        }
    }
}
