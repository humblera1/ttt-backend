<?php

namespace App\Models;

use App\Policies\NotificationTypePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $notification_category_id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property string|null $template_title
 * @property string|null $template_body
 * @property array<string, mixed>|null $placeholders
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read NotificationCategory $category
 */
#[UsePolicy(NotificationTypePolicy::class)]
class NotificationType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'name',
        'description',
        'template_title',
        'template_body',
        'placeholders',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(NotificationCategory::class, 'notification_category_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'placeholders' => 'array',
        ];
    }
}
