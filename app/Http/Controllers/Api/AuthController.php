<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Enregistrement utilisateur
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms_accepted' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        // Enregistrer l'acceptation des conditions
        $user->termsAcceptances()->create([
            'version' => config('app.terms_version', '1.0'),
            'accepted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Créer une souscription gratuite par défaut
        $user->subscriptions()->create([
            'tier' => 'free',
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Connexion utilisateur
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->isBanned()) {
            return response()->json(['message' => 'Your account has been banned'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Authentification OAuth Google
     */
    public function googleCallback(Request $request)
    {
        $validated = $request->validate([
            'google_token' => 'required|string',
        ]);

        // À intégrer avec Google API pour vérifier le token
        // Pour ce template, on simule juste le flux
        
        return response()->json([
            'message' => 'Google authentication in progress',
            'requires_implementation' => true,
        ], 501);
    }

    /**
     * Authentification OAuth Facebook
     */
    public function facebookCallback(Request $request)
    {
        $validated = $request->validate([
            'facebook_token' => 'required|string',
        ]);

        // À intégrer avec Facebook API
        
        return response()->json([
            'message' => 'Facebook authentication in progress',
            'requires_implementation' => true,
        ], 501);
    }

    /**
     * Déconnexion utilisateur
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Obtenir l'utilisateur actuel
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }
}
