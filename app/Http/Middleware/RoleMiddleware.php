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
    public function handle(Request $request, Closure $next, $role): Response
    {
        if(!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();
        
        if (!$user->hasRole($role)) {
             abort(403, 'User does not have the right roles.');
        }
        return $next($request);

        

    }
}
