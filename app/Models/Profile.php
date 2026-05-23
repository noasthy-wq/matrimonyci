<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'age',
        'religion',
        'profession',
        'bio',
        'city',
        'country',
        'education',
        'marital_status',
        'height',
        'complexion',
        'looking_for',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function receivedLikes()
    {
        return Like::where('profile_id', $this->id);
    }

    public function receivedComments()
    {
        return Comment::where('profile_id', $this->id);
    }

    /**
     * Scopes
     */

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('is_banned', false);
        });
    }

    /**
     * Médias
     */

    public function approvedPhotos()
    {
        return $this->media()
            ->where('type', 'photo')
            ->where('is_approved', true)
            ->get();
    }

    public function approvedVideos()
    {
        return $this->media()
            ->where('type', 'video')
            ->where('is_approved', true)
            ->get();
    }

    public function mainPhoto()
    {
        return $this->media()
            ->where('type', 'photo')
            ->where('is_approved', true)
            ->where('is_main', true)
            ->first();
    }

    public function pendingMedia()
    {
        return $this->media()
            ->where('is_approved', false)
            ->get();
    }

    /**
     * Compteurs
     */

    public function likesCount(): int
    {
        return $this->receivedLikes()->count();
    }

    public function commentsCount(): int
    {
        return $this->receivedComments()->count();
    }

    public function mediaCount(): int
    {
        return $this->media()->count();
    }

    /**
     * Méthodes utilitaires
     */

    public function hasCompletedProfile(): bool
    {
        return !is_null($this->gender)
            && !is_null($this->age)
            && !is_null($this->city)
            && $this->approvedPhotos()->count() > 0;
    }
}
