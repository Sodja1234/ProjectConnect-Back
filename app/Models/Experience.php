<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    //
    protected $fillable = [

        'created_by',
        'position',
        'company',
        'date_start',
        'date_end',
        'description',
        'user_id'

    ];
     public function user() {
        return $this->belongsTo(User::class);
     }
}
