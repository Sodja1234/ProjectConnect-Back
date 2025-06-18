<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'project_role_id',
        'token'

    ];

    public function projectRole():BelongsTo
    {
        return $this->belongsTo(ProjectRole::class);
    }


}
