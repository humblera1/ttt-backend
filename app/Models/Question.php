<?php

namespace App\Models;

use App\Interfaces\v1\Status\StatusInterface;
use App\Interfaces\v1\Vote\ModelVotesInterface;
use App\Policies\QuestionPolicy;
use App\Traits\Models\WithStatus;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $met_in_real_interview_count Denormalized count of {@see Statistic} rows with met_in_real_interview = true.
 * @property bool $rating_needs_recalculation Set when denormalized aggregates change and rating must be recalculated.
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
