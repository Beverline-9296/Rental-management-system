<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Only check verification for tenants
        if ($user && $user->isTenant() && !$user->is_verified) {
            // Allow access to verification routes
            if ($request->routeIs('account.verification.*') || $request->routeIs('logout')) {
                return $next($request);
            }
            
            // Redirect to verification page for all other routes
            return redirect()->route('account.verification.show')
                ->with('warning', 'Please verify your account to access this feature.');
        }
        
        return $next($request);
    }
}
