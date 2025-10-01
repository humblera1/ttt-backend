<?php

namespace App\Models;

use App\Policies\SettingPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(SettingPolicy::class)]
class Setting extends Model
{
    protected $guarded = [];
}
