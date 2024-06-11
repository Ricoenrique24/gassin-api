<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('Authorization');

        if (!$apiKey || !User::where('apikey', $apiKey)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->user = User::where('apikey', $apiKey)->first();

        return $next($request);
    }
}
