<?php

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Models\VerificationEmailCode;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(VerificationEmailCode $vcode, $hash)
    {
        if ($vcode->isRevoked() || !$vcode->verify_code($hash)) {
            return response()->json([
                'message' => 'Lo sentimos, no podemos verificar el correo, por favor vuelve a intentarlo o solicita un nuevo correo de verificación'
            ], 400);
        }

        $user = $vcode->user;

        $user->markEmailAsVerified();

        $vcode->delete();

        event(new EmailVerified($user));

        return response()->json([
            'message' => 'Se ha verificado tu correo electrónico',
            'user' => $user
        ], 200);
    }
}
