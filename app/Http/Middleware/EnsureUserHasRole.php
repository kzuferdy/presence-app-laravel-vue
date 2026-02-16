<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
            // Or redirect if it's a web request, but for API 403 json is better.
            // For Inertia handling, we might want to return a redirect or an inertia error.
            // But since this is likely for API routes, JSON is safe. With Inertia, 
            // the authorized routes are usually checked client side for visibility, 
            // and server side for security. If a user forces a visit, Inertia handles 403.
        }

        return $next($request);
    }
}
