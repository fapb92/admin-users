<?php

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Models\VerificationEmailCode;
use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;

class VerifyEmailController extends Controller
{
    protected $tokenRepository;

    public function __construct()
    {
        $this->tokenRepository = app(TokenRepository::class);
    }

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
        ], 200);
    }

    public function resend_email(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Tu email ya ha sido verificado'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        $tokenId = $user->token()->id;

        $this->tokenRepository->revokeAccessToken($tokenId);

        return response()->json([
            'message' => 'Se ha enviado un nuevo correo de verificación, por favor revisa tu correo'
        ], 200);
    }
}
