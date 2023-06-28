<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $activeRole = $user->getActiveRole();
        $roles = Role::where('priority', '>=', $activeRole->priority);
        if ($activeRole->priority !== 1) {
            $roles->where(function (Builder $query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhere('created_by', null);
            });
        }
        return response()->json([
            'message' => 'Roles',
            'data' => RoleResource::collection($roles->get())
        ], 200);
    }

    /**
     * Display the specified role.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return response()->json([
            'message' => 'Roles',
            'data' => new RoleResource($role)
        ], 200);
    }
}
