<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'location',
        'job_title',
        'portfolio_url',
        'is_availability',
        'profile_photo',
        'about'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected function casts(): array
    {
        return [
            'is_availlability' => 'boolean',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
