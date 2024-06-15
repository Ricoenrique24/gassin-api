<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $authorizationHeader = $request->header('Authorization');
        Log::info('Authorization Header: ' . $authorizationHeader);

        if (!$authorizationHeader) {
            return response()->json(['message' => 'API key not provided'], 401);
        }

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $apiKey = substr($authorizationHeader, 7);
        } else {
            return response()->json(['message' => 'Invalid Authorization header'], 401);
        }

        Log::info('Extracted API Key: ' . $apiKey);

        $user = User::where('apikey', $apiKey)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid API key'], 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
