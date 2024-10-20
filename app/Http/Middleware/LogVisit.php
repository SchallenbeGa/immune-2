<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use Stevebauman\Location\Facades\Location;

class LogVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $location = Location::get($request->ip());
        $country = $location ? $location->countryName : 'Unknown';

        Visit::create([
            'ip_address'  => $request->ip(),
            'url'         => $request->fullUrl(),
            'http_method' => $request->method(),
            'user_agent'  => $request->header('User-Agent'),
            'referer'     => $request->header('referer'),
            'country'     => $country,
        ]);

        return $next($request);
    }
}
