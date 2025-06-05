<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $fillable = ['name'];
     public function portfolio()
    {
        return $this->belongsToMany(portfolio::class);
    }
    public function users(): BelongsToMany{
        return $this->belongsToMany(user::class);
     }

}
