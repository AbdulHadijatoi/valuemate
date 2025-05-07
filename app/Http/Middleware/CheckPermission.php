<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        foreach ($permissions as $permission) {
            // Check if the permission exists
            if (!Permission::where('name', $permission)->exists()) {
                return response()->json([
                    'message' => "Permission '{$permission}' does not exist.",
                    'status' => false,
                ], 422);
            }

            // Check if user has permission
            if (!$request->user()) {
                return response()->json([
                    'message' => "Unauthenticated user.",
                    'status' => false,
                ], 422);
            }
            
            if (!$request->user()->hasPermissionTo($permission)) {
                return response()->json([
                    'message' => "You do not have the '{$permission}' permission.",
                    'status' => false,
                ], 422);
            }
        }

        return $next($request);
    }
}
