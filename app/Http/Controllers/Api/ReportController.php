<?php

namespace App\Http\Controllers\Api;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Signaler un utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'reason' => 'required|in:fraud,harassment,inappropriate-content,spam,fake-profile',
            'description' => 'nullable|string|max:1000',
        ]);

        // Vérifier que l'utilisateur ne se signale pas lui-même
        if ($validated['reported_user_id'] === $request->user()->id) {
            return response()->json(['message' => 'You cannot report yourself'], 400);
        }

        // Vérifier qu'il n'y a pas déjà un signalement en attente
        $existingReport = Report::where('user_id', $request->user()->id)
            ->where('reported_user_id', $validated['reported_user_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            return response()->json(['message' => 'You have already reported this user'], 409);
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'reported_user_id' => $validated['reported_user_id'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'User reported successfully',
            'report' => $report,
        ], 201);
    }

    /**
     * Lister mes signalements
     */
    public function myReports(Request $request)
    {
        $reports = Report::where('user_id', $request->user()->id)
            ->with(['reportedUser', 'reportedUser.profile'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $reports->items(),
            'pagination' => [
                'total' => $reports->total(),
                'current_page' => $reports->currentPage(),
                'per_page' => $reports->perPage(),
            ],
        ], 200);
    }
}
