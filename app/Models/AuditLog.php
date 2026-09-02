<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasUuids, BelongsToOrganization;

    public $timestamps = false; // append-only, tamper-evident (NFR 4.1) -- created_at only

    protected $fillable = ['organization_id', 'auditable_type', 'auditable_id', 'user_id', 'action', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    protected static function booted(): void
    {
        static::creating(fn ($model) => $model->created_at ??= now());
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
