<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class TokenController extends Controller
{
    protected $password_client_id;

    protected $refresh_token_exp_time;

    protected $tokenRepository;

    protected $refreshTokenRepository;

    protected $refresh_token_cookie_name;

    public function __construct(protected ClientRepository $clientRepository)
    {
        $this->password_client_id = config('auth.passport.password_grant_client_id');
        $this->refresh_token_exp_time = config('auth.passport.expiration_time.refresh_token');
        $this->refreshTokenRepository = app(RefreshTokenRepository::class);
        $this->tokenRepository = app(TokenRepository::class);
        $this->refresh_token_cookie_name = 'refresh_token';
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
        $response = app()->handle(Request::create('/oauth/token', 'POST', $body));

        $contentResponse = json_decode($response->getContent(), true);
        if ($response->getStatusCode() >= 400) {
            throw new HttpResponseException(response()->json($contentResponse, $response->getStatusCode())->withoutCookie($this->refresh_token_cookie_name));
        }

        return response()->json(Arr::except($contentResponse, ['refresh_token']), 200)->withCookie(cookie($this->refresh_token_cookie_name, $contentResponse['refresh_token'], $this->refresh_token_exp_time));
    }

    public function logout(Request $request)
    {
        $tokenId = $request->user()->token()->id;

        $this->tokenRepository->revokeAccessToken($tokenId);

        $this->refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);


        return response()->json([
            'message' => "Se cerró sesión exitosamente",
        ])->withoutCookie($this->refresh_token_cookie_name);
    }
}
