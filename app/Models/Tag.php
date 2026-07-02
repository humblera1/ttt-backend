<?php

namespace App\Models;

use App\Enums\Status;
use App\Interfaces\v1\Status\StatusInterface;
use App\Policies\TagPolicy;
use App\Traits\Models\WithNormalizedName;
use App\Traits\Models\WithStatus;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $normalized_name
 * @property string $status {@see Status}
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $updatedBy
 * @property-read Collection<int, Question> $questions
 */
#[UsePolicy(TagPolicy::class)]
class Tag extends Model implements StatusInterface
{
    use HasFactory,
        WithStatus,
        WithNormalizedName,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function questions(): MorphToMany
    {
        return $this->morphedByMany(Question::class, 'taggables');
    }
}
