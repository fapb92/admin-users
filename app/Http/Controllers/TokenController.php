<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;

class TokenController extends Controller
{
    protected $password_client_id;

    protected $refresh_token_exp_time;

    public function __construct(protected ClientRepository $clientRepository)
    {
        $this->password_client_id = config('auth.passport.password_grant_client_id');
        $this->refresh_token_exp_time = config('auth.passport.expiration_time.refresh_token');
    }

    public function refresh(Request $request)
    {
        $token = $request->cookie('refresh_token');
        $client = $this->clientRepository->find($this->password_client_id);

        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            "client_id" => $client->id,
            "client_secret" => $client->secret,
            'scope' => '',
        ];
        $response = json_decode(app()->handle(Request::create('/oauth/token', 'POST', $body))->content());

        // dd($response);

        return response()->json([
            'status' => 1,
            'message' => 'refresh token',
            'token_type' => $response->token_type,
            'expires_in' => $response->expires_in,
            'access_token' => $response->access_token,
        ], 200)->withCookie(cookie('refresh_token', $response->refresh_token, $this->refresh_token_exp_time));
    }
}
