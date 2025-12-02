<?php

namespace App\Models;

use App\Policies\StatisticPolicy;
use Database\Factories\StatisticFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
