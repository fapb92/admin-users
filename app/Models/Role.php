<?php

namespace App\Models;

use App\Casts\LowerTitle;
use App\Casts\SnakeUpper;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'id',
        'key',
        'user_id',
        'name',
    ];

    protected $casts = [
        'key' => SnakeUpper::class,
        'name' => LowerTitle::class
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }
}
