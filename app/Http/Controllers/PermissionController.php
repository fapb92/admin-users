<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json([
            'message' => 'Permisos',
            'data' => PermissionResource::collection($permissions)
        ], 200);
    }
}
