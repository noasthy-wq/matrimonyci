<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tier',
        'status',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Méthodes
     */

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function hasFeature(string $feature): bool
    {
        $tierFeatures = config('matrimony.subscriptions.tiers.' . $this->tier . '.features', []);
        return $tierFeatures[$feature] ?? false;
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
