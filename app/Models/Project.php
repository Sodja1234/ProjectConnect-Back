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
        'updated_by'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
