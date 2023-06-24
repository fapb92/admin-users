<?php

namespace App\Observers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Avatar;

class UserObserver
{
    public function created(User $user)
    {
        $nameForAvatar = str()->upper($user->name);

        $date = now()->format('YmdHms');
        $config = config('laravolt.avatar');
        $avatar = new Avatar($config);
        $image = $avatar->create($nameForAvatar)->toSvg();
        $nameImage = "avatar-{$date}.svg";

        $user->avatar_name = $nameImage;
        $user->save();

        Storage::disk('avatar-local')->put("{$user->id}/{$nameImage}", $image);
    }
}
