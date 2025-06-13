<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slug'
    ];

    /**
     *
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }


    public function candidacies()
    {
        return $this->hasMany(Candidacy::class);
    }

    public function appliedProjectRoles()
    {
        return $this->belongsToMany(ProjectRole::class, 'candidacies')
            ->withPivot('is_validated')->withTimestamps();

    }
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'followers_id');
    }
}
