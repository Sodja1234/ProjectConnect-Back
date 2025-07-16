<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     required={"name", "email"},
 *     @OA\Property(property="id", type="integer", description="ID of the user", example=1),
 *     @OA\Property(property="name", type="string", description="Name of the user", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", description="Email of the user", example="john.doe@example.com"),
 *     @OA\Property(property="phone", type="string", description="Phone number of the user", example="+1234567890"),
 *     @OA\Property(property="location", type="string", description="Location of the user", example="New York, USA"),
 *     @OA\Property(property="job_title", type="string", description="Job title of the user", example="Software Engineer"),
 *     @OA\Property(property="portfolio_url", type="string", format="url", description="Portfolio URL of the user", example="https://johndoe.com"),
 *     @OA\Property(property="availability", type="string", description="Availability of the user", example="Full-time"),
 *     @OA\Property(property="profile_photo", type="string", description="Profile photo URL of the user", example="https://example.com/photo.jpg"),
 *     @OA\Property(property="slug", type="string", description="Slug of the user", example="john-doe-1")
 * )
 *
 * @property \Illuminate\Database\Eloquent\Collection $following
 * @property \Illuminate\Database\Eloquent\Collection $followers
 * @property \Illuminate\Database\Eloquent\Collection $interests
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'job_title',
        'role',
        'portfolio_url',
        'availability',
        'profile_photo',
        'slug',
        'about',
        'skill',
        'email_otp',
        'email_otp_expires_at',
        'state',
    ];

    /**
     *
     * The attributes that should be hidden for serialization.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
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
        return $this->belongsToMany(Skill::class)
            ->withPivot('experience_percentage');
    }

    public function users(): BelongsToMany
    {
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

    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }
}
