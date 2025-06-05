<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
=======
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
>>>>>>> 2253bd2 (ajout de la relation belongtoMany dans le model User)

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
    ];

    /**
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
     public function skills(): BelongsToMany{
         return $this->belongsToMany(Skill::class);
     }
     public function users(): BelongsToMany{
        return $this->belongsToMany(user::class);
     }
     public function skills(): BelongsToMany{
         return $this->belongsToMany(Skill::class);
     }
     public function users(): BelongsToMany{
        return $this->belongsToMany(user::class);
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
}
