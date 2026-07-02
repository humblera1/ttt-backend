<?php

namespace App\Models;

use App\Enums\Suggestion\Status as SuggestionStatus;
use App\Interfaces\v1\Status\StatusWithReviewInterface;
use App\Policies\QuestionCompanySuggestionPolicy;
use App\Traits\Models\HasStatusWithReview;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $question_id
 * @property int $company_id
 * @property int|null $moderated_by_id
 * @property int $evidence_count
 * @property string $status {@see SuggestionStatus}
 * @property Carbon|null $last_seen_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Question $question
 * @property-read Company $company
 */
#[UsePolicy(QuestionCompanySuggestionPolicy::class)]
class QuestionCompanySuggestion extends Model implements StatusWithReviewInterface
{
    use HasStatusWithReview;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'question_id',
        'company_id',
        'status',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
