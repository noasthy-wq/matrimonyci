<?php

namespace App\Http\Controllers\Api;

use App\Models\Like;
use App\Models\Profile;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Ajouter un like
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
        ]);

        $user = $request->user();
        $profileId = $validated['profile_id'];

        // Vérifier si l'utilisateur a déjà liké ce profil
        if (Like::hasLiked($user->id, $profileId)) {
            return response()->json(['message' => 'You have already liked this profile'], 409);
        }

        $like = Like::create([
            'user_id' => $user->id,
            'profile_id' => $profileId,
        ]);

        // Vérifier les likes mutuels
        $isMutual = Like::getMutualLikes($user->id, Profile::find($profileId)->user_id);

        return response()->json([
            'message' => 'Like added successfully',
            'like' => $like,
            'is_mutual' => $isMutual,
        ], 201);
    }

    /**
     * Retirer un like
     */
    public function destroy(Request $request, $profileId)
    {
        $like = Like::where('user_id', $request->user()->id)
            ->where('profile_id', $profileId)
            ->first();

        if (!$like) {
            return response()->json(['message' => 'Like not found'], 404);
        }

        $like->delete();

        return response()->json(['message' => 'Like removed successfully'], 200);
    }

    /**
     * Obtenir les likes d'un utilisateur
     */
    public function myLikes(Request $request)
    {
        $likes = Like::where('user_id', $request->user()->id)
            ->with('profile.user')
            ->paginate(20);

        return response()->json([
            'data' => $likes->items(),
            'pagination' => [
                'total' => $likes->total(),
                'current_page' => $likes->currentPage(),
                'per_page' => $likes->perPage(),
            ],
        ], 200);
    }

    /**
     * Obtenir les likes reçus
     */
    public function likedByMe(Request $request)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'User has no profile'], 404);
        }

        $likes = Like::where('profile_id', $profile->id)
            ->with('user')
            ->paginate(20);

        return response()->json([
            'data' => $likes->items(),
            'pagination' => [
                'total' => $likes->total(),
                'current_page' => $likes->currentPage(),
                'per_page' => $likes->perPage(),
            ],
        ], 200);
    }
}
