<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'status',
        'provider',
        'transaction_id',
        'reference',
        'phone_number',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
    ];

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scopes
     */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Méthodes
     */

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }
}
