<?php

namespace App\Models;

use App\Enums\QuestionRejectionReason;
use App\Enums\Status;
use App\Interfaces\v1\Status\StatusInterface;
use App\Interfaces\v1\Vote\ModelVotesInterface;
use App\Policies\QuestionPolicy;
use App\Traits\Models\WithStatus;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $duplicate_of_id
 * @property string $title
 * @property string|null $answer
 * @property bool $is_premium
 * @property bool $is_anonymous
 * @property string $status {@see Status}
 * @property string|null $rejection_reason {@see QuestionRejectionReason}
 * @property string|null $rejection_comment
 * @property int|null $user_id
 * @property int $views_count
 * @property int $likes_count
 * @property int $comments_count
 * @property int $met_in_real_interview_count Denormalized count of {@see Statistic} rows with met_in_real_interview = true.
 * @property int $rating
 * @property bool $rating_needs_recalculation Set when denormalized aggregates change and rating must be recalculated.
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Question|null $duplicateOf
 * @property-read Collection<int, Question> $duplicates
 * @property-read Collection<int, Tag> $tags
 * @property-read Collection<int, Grade> $grades
 * @property-read Collection<int, Vote> $votes
 * @property-read Collection<int, Company> $companies
 * @property-read Collection<int, Position> $positions
 * @property-read User|null $user
 * @property-read Collection<int, Statistic> $statistics
 * @property-read Collection<int, Comment> $comments
 */
#[UsePolicy(QuestionPolicy::class)]
class Question extends Model implements ModelVotesInterface, StatusInterface
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory,
        SoftDeletes,
        WithStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'answer',
        'is_premium',
        'is_anonymous',
        'status',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'likes_count' => 'integer',
            'met_in_real_interview_count' => 'integer',
            'rating_needs_recalculation' => 'boolean',
        ];
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_id');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(self::class, 'duplicate_of_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggables');
    }

    public function grades(): MorphToMany
    {
        return $this->morphToMany(Grade::class, 'gradables');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(Statistic::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
