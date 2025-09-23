<?php

namespace App\Models;

use App\Enums\Status;
use App\Interfaces\v1\Status\StatusInterface;
use App\Policies\PositionPolicy;
use App\Traits\Status\HasStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(PositionPolicy::class)]
class Position extends Model implements StatusInterface
{
    use HasFactory,
        HasStatus,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', Status::Approved->value);
    }
}
