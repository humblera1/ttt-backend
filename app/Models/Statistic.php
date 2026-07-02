<?php

namespace App\Models;

use App\Enums\Period;
use App\Policies\StatisticPolicy;
use Database\Factories\StatisticFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property bool $met_in_real_interview
 * @property int|null $company_id
 * @property int|null $position_id
 * @property string|null $when_asked {@see Period}
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Question $question
 * @property-read Company|null $company
 * @property-read Position|null $position
 */
#[UsePolicy(StatisticPolicy::class)]
class Statistic extends Model
{
    /** @use HasFactory<StatisticFactory> */
    use HasFactory;

    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    #[Scope]
    public function byUserAndQuestion(Builder $query, int $userId, int $questionId): Builder
    {
        return $query->where('user_id', $userId)
            ->where('question_id', $questionId);
    }

    #[Scope]
    public function byCompanyAndPosition(Builder $query, int $companyId, int $positionId): Builder
    {
        return $query->where('company_id', $companyId)
            ->where('position_id', $positionId);
    }
}
