<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user() || !Auth::user()->vendor) {
            return response()->json(['error' => 'Access denied. User is not a vendor.'], 403);
        }

        return $next($request);
    }
}
