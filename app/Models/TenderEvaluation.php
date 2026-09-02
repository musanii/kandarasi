<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderEvaluation extends Model
{
    use HasUuids, BelongsToOrganization;

    protected $fillable = ['organization_id', 'procurement_tender_id', 'bidder_name', 'evaluated_by_user_id', 'score', 'comments'];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(ProcurementTender::class, 'procurement_tender_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by_user_id');
    }
}
