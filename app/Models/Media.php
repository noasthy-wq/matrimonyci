<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'path',
        'type',
        'file_size',
        'mime_type',
        'is_approved',
        'is_main',
        'rejection_reason',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_main' => 'boolean',
    ];

    /**
     * Relations
     */

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Scopes
     */

    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Méthodes
     */

    /**
     * Approuve le média
     */
    public function approve(): void
    {
        $this->update([
            'is_approved' => true,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Rejette le média
     */
    public function reject(string $reason): void
    {
        $this->update([
            'is_approved' => false,
            'rejection_reason' => $reason,
        ]);

        // Supprimer le fichier
        Storage::delete($this->path);
    }

    /**
     * Obtient l'URL du média
     */
    public function getUrl(): string
    {
        if (config('filesystems.default') === 's3') {
            return Storage::disk('s3')->url($this->path);
        }

        return asset('storage/' . $this->path);
    }

    /**
     * Vérifie si c'est une photo
     */
    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }

    /**
     * Vérifie si c'est une vidéo
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
