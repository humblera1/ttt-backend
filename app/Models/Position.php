<?php

namespace App\Models;

use App\Interfaces\v1\Status\StatusInterface;
use App\Policies\PositionPolicy;
use App\Traits\Models\WithNormalizedName;
use App\Traits\Models\WithStatus;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function statistics(): HasMany
    {
        return $this->hasMany(Statistic::class);
    }
}
