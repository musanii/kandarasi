<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'workflow_process_id', 'step_order', 'name', 'min_approvers', 'mandatory', 'status'];

    public function workflowProcess(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcess::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class);
    }
}
