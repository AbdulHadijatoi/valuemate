<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        // Check if user is authenticated
        if (!$request->user()) {
            Log::warning('Unauthenticated user attempted to access protected route', [
                'route' => $request->route()->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return response()->json([
                'message' => "Unauthenticated user.",
                'status' => false,
            ], 401);
        }

        foreach ($permissions as $permission) {
            // Check if the permission exists
            if (!Permission::where('name', $permission)->exists()) {
                Log::error('Permission does not exist', [
                    'permission' => $permission,
                    'user_id' => $request->user()->id,
                    'route' => $request->route()->getName(),
                ]);
                
                return response()->json([
                    'message' => "Permission '{$permission}' does not exist.",
                    'status' => false,
                ], 422);
            }

            // Check if user has permission
            if (!$request->user()->hasPermissionTo($permission)) {
                Log::warning('User attempted to access route without permission', [
                    'user_id' => $request->user()->id,
                    'permission' => $permission,
                    'route' => $request->route()->getName(),
                    'ip' => $request->ip(),
                ]);
                
                return response()->json([
                    'message' => "You do not have the '{$permission}' permission.",
                    'status' => false,
                ], 422);
            }
        }

        return $next($request);
    }
}
