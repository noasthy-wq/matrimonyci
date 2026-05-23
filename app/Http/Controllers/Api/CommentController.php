<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Ajouter un commentaire
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'content' => 'required|string|min:3|max:500',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'profile_id' => $validated['profile_id'],
            'content' => $validated['content'],
            'is_approved' => false, // Nécessite une modération
        ]);

        return response()->json([
            'message' => 'Comment submitted successfully and pending moderation',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Lister les commentaires d'un profil
     */
    public function index(Request $request, $profileId)
    {
        $comments = Comment::where('profile_id', $profileId)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $comments->items(),
            'pagination' => [
                'total' => $comments->total(),
                'current_page' => $comments->currentPage(),
                'per_page' => $comments->perPage(),
            ],
        ], 200);
    }

    /**
     * Supprimer un commentaire
     */
    public function destroy(Request $request, $commentId)
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
