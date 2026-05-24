<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Vérifie le statut d'abonnement pour les fonctionnalités premium
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $feature = null)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription',
            ], 403);
        }

        if ($feature && !$subscription->hasFeature($feature)) {
            return response()->json([
                'message' => 'This feature requires a premium subscription',
            ], 403);
        }

        return $next($request);
    }
}
