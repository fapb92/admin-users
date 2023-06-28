<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserStoreRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserStoreRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'created_by' => $request->user()->id,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'Lo sentimos no pudo ser creado el usuario'
            ], 400);
        }

        $user->addRole($request->role);

        // event(new Registered($user));

        return response()->json([
            'message' => 'Usuario creado con exito, correo de confirmación enviado a nuevo usuario'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}