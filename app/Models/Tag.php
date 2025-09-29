<?php

namespace App\Models;

use App\Interfaces\v1\Status\StatusInterface;
use App\Policies\TagPolicy;
use App\Traits\Models\WithStatus;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(TagPolicy::class)]
class Tag extends Model implements StatusInterface
{
    use HasFactory,
        WithStatus,
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
