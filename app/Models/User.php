<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\Hashed;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => Hashed::class,
    ];

    protected $appends = [
        'avatar_url'
    ];

    /**
     * Save all currente permission
     * @var array
     */
    protected $currentPermissions = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withPivot('active')->withTimestamps();
    }

    public function verification_email_code()
    {
        return $this->hasOne(VerificationEmailCode::class);
    }

    public function getActiveRole()
    {
        return $this->roles()->wherePivot('active', true)->first();
    }

    public function getCurretPermissions()
    {
        if (!!$this->currentPermissions) {
            return $this->currentPermissions;
        }


        if (!$role = $this->getActiveRole()) {
            return null;
        }
        return $this->currentPermissions = $role->permission_list();
    }

    public function is_ableTo($permission)
    {
        return in_array($permission, $this->getCurretPermissions());
    }

    public function activateRole($role)
    {
        $activeRole = $this->getActiveRole();
        $this->roles()->updateExistingPivot($activeRole->id, ['active' => false]);
        if (!!$newRole = $this->hasRole($role)) {
            $this->roles()->updateExistingPivot($newRole->id, ['active' => true]);
        }
        $this->currentPermissions = [];
    }

    public function hasRole($role, $key = 'key')
    {
        if (is_string($role)) {
            return $this->roles()->where($key, $role)->first();
        }
        if ($role instanceof Role) {
            return $this->roles()->where($key, $role->{$key})->first();
        }
        throw new HttpResponseException(response()->json([
            'message' => 'Invalid type role'
        ], 500));
    }

    public function addRole($role, $attributes = [], $key = 'key')
    {
        if (!!$this->hasRole($role, $key)) {
            return false;
        }
        if (is_string($role)) {
            $this->roles()->attach(Role::where($key, $role)->first(), $attributes);
            return true;
        }
        if ($role instanceof Role) {
            $this->roles()->attach($role, $attributes);
            return true;
        }
    }

    public function removeRole($role, $attributes = [], $key = 'key')
    {
        if (!$roleToRemove = $this->hasRole($role, $key)) {
            return false;
        }

        $this->roles()->detach($roleToRemove, $attributes);
        return true;
    }

    public function newSuperAdmin()
    {
        if (!$this->roles->all()) {
            return $this->addRole("super_admin", ['active' => true]);
        }
        return false;
    }

    public function newAdmin()
    {
        if (!$this->roles->all()) {
            return $this->addRole('admin', ['active' => true]);
        }
        return false;
    }

    public function newUser()
    {
        if (!$this->roles->all()) {
            return $this->addRole('user', ['active' => true]);
        }
        return false;
    }

    public function get_verification_email_code()
    {
        if ($this->hasVerifiedEmail()) {
            return null;
        }

        $code =  $this->verification_email_code()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'code' => str()->random(40),
            'rovoked_at' => now()->addHours(1),
        ]);

        if ($code->isRevoked()) {
            $code =  $this->verification_email_code()->updateOrCreate([
                'user_id' => $this->id,
            ], [
                'code' => str()->random(40),
                'rovoked_at' => now()->addHours(1),
            ]);
        }

        return $code;
    }

    public function getAvatarUrlAttribute()
    {
        return Storage::disk('avatar-local')->url("{$this->id}/{$this->avatar_name}");
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function rolesCreated()
    {
        return Role::where('user_id', $this->id)->get();
    }
}
