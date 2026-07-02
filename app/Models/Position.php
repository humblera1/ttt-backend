<?php

namespace App\Models;

use App\Enums\Status;
use App\Interfaces\v1\Status\StatusInterface;
use App\Policies\PositionPolicy;
use App\Traits\Models\WithNormalizedName;
use App\Traits\Models\WithStatus;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $normalized_name
 * @property string $status {@see Status}
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property Carbon|null $deleted_at
 * @property-read User|null $updatedBy
 * @property-read Collection<int, Question> $questions
 * @property-read Collection<int, Statistic> $statistics
 */
#[UsePolicy(PositionPolicy::class)]
class Position extends Model implements StatusInterface
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

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(Statistic::class);
    }
}
