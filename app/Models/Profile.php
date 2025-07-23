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
        'availability',
        'profile_photo',
        'about'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
