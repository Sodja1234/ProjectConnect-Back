<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidacy extends Model
{
    protected $fillable = [
        'project_role_id',
        'user_id',
        'is_validated',
        'status'
    ];

    public function projectRole()
    {
        return $this->belongsTo(ProjectRole::class, 'project_role_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
