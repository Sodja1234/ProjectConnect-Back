<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidacy extends Model
{
    protected $fillable = [
        'project_role_id',
        'user_id',
        'is_validated'
    ];

    public function projectRole()
    {
        return $this->belongsTo(ProjectRole::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
