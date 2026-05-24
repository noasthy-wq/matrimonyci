<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyTermsAccepted
{
    /**
     * Vérifie que l'utilisateur a accepté les conditions d'utilisation
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->hasAcceptedTerms()) {
            return response()->json([
                'message' => 'You must accept the terms of service',
            ], 403);
        }

        return $next($request);
    }
}
