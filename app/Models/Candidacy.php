<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Candidacy",
 *     type="object",
 *     title="Candidacy",
 *     description="Candidacy model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="project_role_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="is_validated", type="boolean", example=false),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="project_role", ref="#/components/schemas/ProjectRole")
 * )
 */
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
