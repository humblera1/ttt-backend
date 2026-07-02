<?php

namespace App\Models;

use App\Policies\UserNotificationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use \Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * @property int $id
 * @property int $user_id
 * @property int $notification_type_id
 * @property string $title
 * @property string $body
 * @property array<string, mixed>|null $data
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read NotificationType $type
 * @property-read NotificationCategory $category
 * @property-read User $user
 */
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
