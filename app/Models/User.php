<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable;

    protected $fillable = ['organization_id', 'name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function seat(): HasOne
    {
        return $this->hasOne(Seat::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }

    public function isOrgAdmin(): bool
    {
        return $this->role === 'org_admin';
    }
}
