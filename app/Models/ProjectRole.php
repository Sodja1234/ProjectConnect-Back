<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRole extends Model
{
     protected $table = 'project_role';

    protected $fillable = [
        'project_id',
        'role_id',
        'description',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_role_skill')->withTimestamps();
    }
    public function candidacies()
    {
        return $this->hasMany(Candidacy::class);
    }

    public function applicants()
    {
        return $this->belongsToMany(User::class, 'candidacies')
            ->withPivot('is_validated')->withTimestamps();

    }
}
