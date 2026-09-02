<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationCommittee extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $table = 'evaluation_committees';

    protected $fillable = ['organization_id', 'procurement_tender_id', 'user_id', 'role'];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(ProcurementTender::class, 'procurement_tender_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
