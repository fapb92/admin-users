<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationEmailCode extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'code',
        'rovoked_at',
    ];

    protected $casts = [
        'rovoked_at' => 'datetime',
    ];

    /**
     * Returns user
     * @return \App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRevoked()
    {
        return $this->rovoked_at->isPast();
    }

    public function revoke()
    {
        return $this->delete();
    }

    public function verify_code($code_to_check)
    {
        return str()->is(sha1($this->code), $code_to_check);
    }

}
