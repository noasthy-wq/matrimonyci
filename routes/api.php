<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Routes publiques (pas d'authentification requise)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google-callback', [AuthController::class, 'googleCallback']);
    Route::post('/facebook-callback', [AuthController::class, 'facebookCallback']);
});

// Routes publiques - Profils
Route::prefix('profiles')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::get('/{id}', [ProfileController::class, 'show']);
});

// Routes protégées (authentification requise)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Profils
    Route::prefix('profiles')->group(function () {
        Route::post('/', [ProfileController::class, 'store']);
        Route::put('/{id}', [ProfileController::class, 'update']);
        Route::delete('/{id}', [ProfileController::class, 'destroy']);
    });

    // Likes
    Route::prefix('likes')->group(function () {
        Route::post('/', [LikeController::class, 'store']);
        Route::delete('/{profileId}', [LikeController::class, 'destroy']);
        Route::get('/my-likes', [LikeController::class, 'myLikes']);
        Route::get('/liked-by-me', [LikeController::class, 'likedByMe']);
    });

    // Comments
    Route::prefix('comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']);
        Route::get('/{profileId}', [CommentController::class, 'index']);
        Route::delete('/{commentId}', [CommentController::class, 'destroy']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::post('/', [ReportController::class, 'store']);
        Route::get('/my-reports', [ReportController::class, 'myReports']);
    });
});
