<?php

namespace App\Models;

use App\Enums\Grade as GradeName;
use App\Policies\GradePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name {@see GradeName}
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Question> $questions
 */
#[UsePolicy(GradePolicy::class)]
class Grade extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    public function questions(): MorphToMany
    {
        return $this->morphedByMany(Question::class, 'taggables');
    }
}
