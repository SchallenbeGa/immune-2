<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Symbol extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
    ];

    public function getRouteKeyName(): string
    {
        return 'name';
    }

    /**
     * Check if user favored the article.
     */
    public function favoritedByUser(User $user): bool
    {
        return $this->favoritedUsers()
            ->whereKey($user->getKey())
            ->exists();
    }
    /**
     * Get users that favorited the article.
     */
    public function favoritedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'symbol_favorite');
    }

    public function data()
    {
        return $this->hasMany(Ohlvc::class, 'symbol_id');
    }
    public function data_s()
    {
        return $this->hasMany(Signal::class, 'symbol_id');
    }
    public function data_t()
    {
        return $this->hasMany(Trade::class, 'symbol_id');
    }
    public function toggleUserFavorite(User $user): bool
    {
        $isFavorited = false;

        if ($this->favoritedByUser($user)) {
            $user->favorites()->detach($this);
        } else {
            $user->favorites()->syncWithoutDetaching($this);
            $isFavorited = true;
        }

        return $isFavorited;
    }
}
