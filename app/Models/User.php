<?php

namespace App\Models;

use App\Models\Scopes\User\NotBannedScope;
use App\Policies\v1\UserPolicy;
use App\Traits\User\HasFilamentAccess;
use App\Traits\User\HasFilamentName;
use App\Traits\User\WithBanned;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $password
 * @property string|null $email
 * @property Carbon|null $email_verified_at
 * @property string|null $avatar_url
 * @property Carbon|null $birthday
 * @property Carbon|null $career_start
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $banned_at
 * @property Carbon|null $deleted_at
 * @property-read string $full_name Computed from first_name and last_name.
 * @property-read Collection<int, Statistic> $statistics
 * @property-read Collection<int, UserNotification> $notifications
 * @property-read Collection<int, Vote> $votes
 * @property-read Collection<int, Role> $roles
 */
#[ScopedBy([NotBannedScope::class])]
#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,
        HasApiTokens,
        Notifiable,
        SoftDeletes,
        WithBanned,
        HasRoles,
        HasFilamentName,
        HasFilamentAccess;

    protected $with = ['roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(Statistic::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Filter by username, email, or full name (first name + last name).
     */
    public function scopeMatchingSearchTerm(Builder $query, string $search): Builder
    {
        $like = '%' . $search . '%';

        return $query->where(function (Builder $query) use ($like): void {
            $query
                ->where('username', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$like]);
        });
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name'],
        );
    }
}
