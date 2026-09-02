<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationBranding extends Model
{
    use HasUuids;

    protected $table = 'organization_branding';

    protected $fillable = ['organization_id', 'logo_url', 'primary_color', 'secondary_color', 'accent_color'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
