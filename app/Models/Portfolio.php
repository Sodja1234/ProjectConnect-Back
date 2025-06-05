<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model
{
    // 
    protected $fillable = [
        'name',
        'description',
        'link',
        'user_id'
     
    ];
    public function user()
    {
        return $this->belongsTo(user::class);
    }
   
}
