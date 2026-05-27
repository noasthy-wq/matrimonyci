<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    /**
     * Relations
     */

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function termsAcceptances()
    {
        return $this->hasMany(TermsAcceptance::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    /**
     * Scopes
     */

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Méthodes utilitaires
     */

    /**
     * Vérifie si l'utilisateur est banni
     */
    public function isBanned(): bool
    {
        return $this->is_banned === true;
    }

    /**
     * Bannit l'utilisateur
     */
    public function ban(string $reason = null): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
        ]);

        if ($reason) {
            $this->violations()->create([
                'reason' => $reason,
                'type' => 'ban',
                'status' => 'active',
            ]);
        }
    }

    /**
     * Débannit l'utilisateur
     */
    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
        ]);
    }

    /**
     * Obtient l'abonnement actif
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();
    }

    /**
     * Vérifie le statut premium
     */
    public function isPremium(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && in_array($subscription->tier, ['premium_monthly', 'premium_annual']);
    }

    /**
     * Vérifie l'acceptation des conditions d'utilisation
     */
    public function hasAcceptedTerms(): bool
    {
        return $this->termsAcceptances()
            ->where('version', config('app.terms_version', '1.0'))
            ->exists();
    }

    /**
     * Compte les violations actives
     */
    public function activeViolationsCount(): int
    {
        return $this->violations()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Compte les avertissements
     */
    public function warningsCount(): int
    {
        return $this->violations()
            ->where('type', 'warning')
            ->where('status', 'active')
            ->count();
    }
}
