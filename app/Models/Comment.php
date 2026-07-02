<?php

namespace App\Models;

use App\Enums\Comment\ReasonForDeletion;
use App\Interfaces\v1\Vote\ModelVotesInterface;
use App\Policies\CommentPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property int|null $parent_id
 * @property string $body
 * @property int $likes_count
 * @property Carbon|null $deleted_at
 * @property int|null $deleted_by_id
 * @property string|null $deleted_reason_code {@see ReasonForDeletion}
 * @property string|null $deleted_reason_comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Question $question
 * @property-read Comment|null $parent
 * @property-read Collection<int, Comment> $children
 * @property-read User|null $deletedBy
 * @property-read Collection<int, Vote> $votes
 */
#[UsePolicy(CommentPolicy::class)]
class Comment extends Model implements ModelVotesInterface
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'body',
        'parent_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }
}
