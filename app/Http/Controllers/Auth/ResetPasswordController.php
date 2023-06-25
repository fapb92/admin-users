<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    protected $configFront;
    public function __construct()
    {
        $this->configFront = config('services.user_admin');
    }
    public function sentEmail(ResetPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => trans($status),
            ], 400);
        }

        return response()->json([
            'message' => trans($status),
        ], 200);
    }

    public function reset($token)
    {
        $path = create_url($this->configFront['front'], $this->configFront['reset_password']);
        $query = http_build_query([
            'token' => $token,
        ]);
        return redirect("$path?{$query}");
    }
}
