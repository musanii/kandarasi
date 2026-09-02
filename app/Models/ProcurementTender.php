<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProcurementTender extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'title', 'description', 'estimated_value',
        'status', 'submission_deadline', 'resulting_contract_id',
    ];

    protected $casts = ['submission_deadline' => 'datetime'];

    public function committee(): HasMany
    {
        return $this->hasMany(EvaluationCommittee::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(TenderEvaluation::class);
    }

    public function workflowProcess(): MorphMany
    {
        return $this->morphMany(WorkflowProcess::class, 'workflowable');
    }

    public function resultingContract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'resulting_contract_id');
    }

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }
}
