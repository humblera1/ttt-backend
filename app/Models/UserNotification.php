<?php

namespace App\Models;

use App\Policies\UserNotificationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;
use Znck\Eloquent\Relations\BelongsToThrough;

#[UsePolicy(UserNotificationPolicy::class)]
class UserNotification extends Model
{
    use SoftDeletes, BelongsToThroughTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'body',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }

    public function category(): BelongsToThrough
    {
        return $this->belongsToThrough(NotificationCategory::class, NotificationType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
