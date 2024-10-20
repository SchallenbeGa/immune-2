<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Vérifie si l'utilisateur est authentifié et a le bon rôle
        if (Auth::check() && Auth::user()->role == $role) {
            return $next($request);
        }

        // Si l'utilisateur n'a pas le bon rôle, on le redirige
        return redirect()->route('home')->with('error', 'Accès non autorisé.');
    }
}
