<?php

namespace App\Models;

use App\Enums\Settings\Section;
use App\Enums\Type;
use App\Policies\SettingPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property string|null $label
 * @property string $section {@see Section}
 * @property string $value
 * @property string $type {@see Type}
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[UsePolicy(SettingPolicy::class)]
class Setting extends Model
{
    protected $guarded = [];
}
