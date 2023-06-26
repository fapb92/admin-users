<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;

class LoginController extends Controller
{
    protected $password_client_id;

    protected $personal_access_client_id;

    protected $refresh_token_exp_time;

    public function __construct(protected ClientRepository $clientRepository)
    {
        $this->password_client_id = config('auth.passport.password_grant_client_id');
        $this->personal_access_client_id = config('auth.passport.personal_access_client_id');
        $this->refresh_token_exp_time = config('auth.passport.expiration_time.refresh_token');
    }

    public function login(LoginRequest $request)
    {
        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            $client = $this->clientRepository->find($this->personal_access_client_id);
            $body = [
                "grant_type" => 'personal_access',
                "client_id" => $client->id,
                "client_secret" => $client->secret,
                'user_id' => $user->id,
                "scope" => [],
            ];
            $response = $this->getToken($body);

            return response()->json([
                'status' => 2,
                'message' => 'Correo electrónico no verificado, por favor verifica tu correo y vuelve a intentarlo',
                'token_type' => $response->token_type,
                'expires_in' => $response->expires_in,
                'access_token' => $response->access_token,
            ], 200)->withCookie('refresh_token');
        }

        $client = $this->clientRepository->find($this->password_client_id);;
        $body = [
            "grant_type" => 'password',
            "client_id" => $client->id,
            "client_secret" => $client->secret,
            'username' => $request->email,
            'password' => $request->password,
            "scope" => [],
        ];
        $response = $this->getToken($body);

        return response()->json([
            'status' => 1,
            'message' => 'Inicio de sesión correcto',
            'token_type' => $response->token_type,
            'expires_in' => $response->expires_in,
            'access_token' => $response->access_token,
        ], 200)->withCookie(cookie('refresh_token', $response->refresh_token, $this->refresh_token_exp_time));
    }

    protected function getToken($body)
    {
        return json_decode(app()->handle(Request::create('/oauth/token', 'POST', $body))->content());
    }
}
