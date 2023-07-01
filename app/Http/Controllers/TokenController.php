<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Http\Resources\UserPermissionsResource;
use App\Models\User;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
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

        $token_decrypted = $this->decryptRefreshToken($token);

        if (array_key_exists('error', $token_decrypted)) {
            return response()->json([
                'message' => $token_decrypted['message']
            ], 200, $token_decrypted['status']);
        }

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

        $user = User::where('id', $token_decrypted['user_id'])->first();

        $role = $user->getActiveRole();

        $responseBody = Arr::except($contentResponse, ['refresh_token']);

        $responseBody['role'] = !!$role ? new RoleResource($role) : null;
        $responseBody['permissions'] = !!$role ?  UserPermissionsResource::collection($role->permissions) : null;

        return response()->json($responseBody, 200)->withCookie(cookie($this->refresh_token_cookie_name, $contentResponse['refresh_token'], $this->refresh_token_exp_time));
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

    protected function decryptRefreshToken($token)
    {
        try {
            $enc_key = base64_decode(substr(config('app.key'), 7));
            $decrypt_ref_token = Crypto::decryptWithPassword($token, $enc_key);
            return json_decode($decrypt_ref_token, true);
        } catch (WrongKeyOrModifiedCiphertextException $ex) {
            return ['message' => "invalid token", 'status' => 403];
        }
    }
}
