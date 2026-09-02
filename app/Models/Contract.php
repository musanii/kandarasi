<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contract extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'title', 'contract_type_id', 'description', 'organization_unit_id', 'status',
        'value', 'currency', 'effective_date', 'expiry_date', 'created_by_user_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function parties(): HasMany
    {
        return $this->hasMany(ContractParty::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContractDocument::class);
    }

    public function workflowProcess(): MorphMany
    {
        return $this->morphMany(WorkflowProcess::class, 'workflowable');
    }

    public function reminderPolicies(): HasMany
    {
        return $this->hasMany(ReminderPolicy::class);
    }

    public function caseLogs(): HasMany
    {
        return $this->hasMany(CaseLog::class);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }
}
