<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Liste tous les profils actifs
     */
    public function index(Request $request)
    {
        $query = Profile::with('user', 'media')
            ->active()
            ->verified();

        // Filtres optionnels
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->has('age_min')) {
            $query->where('age', '>=', $request->age_min);
        }
        if ($request->has('age_max')) {
            $query->where('age', '<=', $request->age_max);
        }
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $profiles = $query->paginate(20);

        return response()->json([
            'data' => $profiles->items(),
            'pagination' => [
                'total' => $profiles->total(),
                'current_page' => $profiles->currentPage(),
                'per_page' => $profiles->perPage(),
            ],
        ], 200);
    }

    /**
     * Affiche un profil spécifique
     */
    public function show($id)
    {
        $profile = Profile::with(['user', 'media', 'likes', 'comments'])->find($id);

        if (!$profile || !$profile->user) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json([
            'profile' => $profile,
            'stats' => [
                'likes_count' => $profile->likesCount(),
                'comments_count' => $profile->commentsCount(),
                'photos_count' => $profile->media()->photos()->count(),
            ],
        ], 200);
    }

    /**
     * Crée ou met à jour le profil de l'utilisateur
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'gender' => 'required|in:homme,femme,autre',
            'age' => 'required|integer|min:18|max:100',
            'religion' => 'nullable|string',
            'profession' => 'nullable|string',
            'bio' => 'nullable|string|max:500',
            'city' => 'required|string',
            'education' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'height' => 'nullable|integer',
            'complexion' => 'nullable|string',
            'looking_for' => 'nullable|string|max:500',
        ]);

        $profile = $user->profile()->updateOrCreate([], $validated);

        return response()->json([
            'message' => 'Profile created/updated successfully',
            'profile' => $profile,
        ], $user->profile() ? 200 : 201);
    }

    /**
     * Met à jour le profil
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'gender' => 'nullable|in:homme,femme,autre',
            'age' => 'nullable|integer|min:18|max:100',
            'religion' => 'nullable|string',
            'profession' => 'nullable|string',
            'bio' => 'nullable|string|max:500',
            'city' => 'nullable|string',
            'education' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'height' => 'nullable|integer',
            'complexion' => 'nullable|string',
            'looking_for' => 'nullable|string|max:500',
        ]);

        $profile->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ], 200);
    }

    /**
     * Supprime le profil
     */
    public function destroy(Request $request, $id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $profile->delete();

        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }
}
