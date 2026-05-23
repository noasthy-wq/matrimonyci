<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'profile_id',
    ];

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Méthodes statiques
     */

    /**
     * Vérifie si un utilisateur a liké un profil
     */
    public static function hasLiked(int $userId, int $profileId): bool
    {
        return self::where('user_id', $userId)
            ->where('profile_id', $profileId)
            ->exists();
    }

    /**
     * Détecte les likes mutuels
     */
    public static function getMutualLikes(int $userId, int $otherUserId)
    {
        return self::where(function ($query) use ($userId, $otherUserId) {
            $query->where('user_id', $userId)
                ->where('profile_id', $otherUserId);
        })->orWhere(function ($query) use ($userId, $otherUserId) {
            $query->where('user_id', $otherUserId)
                ->where('profile_id', $userId);
        })->count() === 2;
    }
}
