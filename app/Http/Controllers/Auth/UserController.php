<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSelectRoleRequest;
use App\Http\Resources\ActiveRoleResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'message' => "User Info",
            'data' => new UserResource($request->user()),
        ], 200);
    }

    public function select_role(UserSelectRoleRequest $request)
    {
        $user = $request->user();
        if (!$role = $user->activateRole($request->role)) {
            throw new HttpResponseException(response()->json([
                'message' => 'No fue posible seleccionar este rol, por favor intentalo de nuevo'
            ], 400));
        }
        return response()->json([
            'message' => 'Rol seleccionado',
            'data' => new ActiveRoleResource($role)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
