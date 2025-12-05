<?php

namespace App\Models;

use App\Interfaces\v1\Status\StatusWithReviewInterface;
use App\Policies\QuestionCompanySuggestionPolicy;
use App\Traits\Models\HasStatusWithReview;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
