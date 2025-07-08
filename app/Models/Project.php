<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [

        'title',
        'description',
        'date_start',
        'date_end',
        'budget',
        'location',
        'visibility',
        'created_by',
        'updated_by',
        'status_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
   public function roles()
{
    return $this->belongsToMany(Role::class, 'project_role')
                ->withPivot('description');

}

    public function domains(){
        return $this->belongsToMany(Domain::class);
    }
    public function projectRoles()
{
    return $this->hasMany(ProjectRole::class);
}
public function status()
{
    return $this->belongsTo(Status::class);
}

}
