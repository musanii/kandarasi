<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowAction extends Model
{
    use HasUuids, BelongsToOrganization;

    public $timestamps = false; // immutable -- created_at only, never updated

    protected $fillable = ['organization_id', 'workflow_step_id', 'user_id', 'action', 'comment'];

    protected static function booted(): void
    {
        static::creating(fn ($model) => $model->created_at ??= now());
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
