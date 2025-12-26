<?php

namespace App\Models;

use App\Policies\NotificationTypePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return $this->belongsTo(NotificationCategory::class);
    }
}
