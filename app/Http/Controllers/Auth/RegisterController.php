<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'Lo sentimos no pudo ser creado el usuario'
            ], 400);
        }

        event(new Registered($user));

        return response()->json([
            'message' => 'Usuario creado con exito, por favor confirma tu correo'
        ], 200);
    }
}
