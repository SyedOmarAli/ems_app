<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {

        \Log::info('RoleMiddleware: auth()->check() = ' . (auth()->check() ? 'true' : 'false'));
        if(!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();
        $roles = $user->getRoleNames();
        $hasRole = $user->hasRole($role);
        \Log::info('User roles:', ['roles' => $roles, 'required' => $role, 'hasRole' => $hasRole]);
        if (!$hasRole) {
            \Log::warning('User does not have the required role.', ['user_id' => $user->id, 'required' => $role]);
            abort(403, 'User does not have the right roles.');
        }
        return $next($request);



    }
}
