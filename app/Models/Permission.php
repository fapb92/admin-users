<?php

namespace App\Models;

use App\Casts\LowerTitle;
use App\Casts\SnakeLower;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $casts = [
        'key' => SnakeLower::class,
        'name' => LowerTitle::class,
    ];

    protected $fillable = [
        'key',
        'name',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
}
