<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = $request->user();
        foreach ($permissions as $permission) {
            if (!$user->is_ableTo($permission)) {
                throw new HttpResponseException(response()->json([
                    'message' => "you don't have the necessary permissions"
                ], 401));
            }
        }
        return $next($request);
    }
}
