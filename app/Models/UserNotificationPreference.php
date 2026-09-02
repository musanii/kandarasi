<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasUuids;

    protected $table = 'user_notification_preferences';

    protected $fillable = ['user_id', 'channels', 'digest'];

    protected $casts = [
        'channels' => 'array',
        'digest' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
