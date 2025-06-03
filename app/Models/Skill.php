<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name'];
     public function portfolio()
    {
        return $this->belongsToMany(portfolio::class);
    }
public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'portfolio_skill'
        );
    }
}
