<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Invitation",
 *     type="object",
 *     title="Invitation",
 *     description="Invitation model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="invited@example.com"),
 *     @OA\Property(property="project_role_id", type="integer", example=1),
 *     @OA\Property(property="token", type="string", example="invitation-token"),
 *     @OA\Property(property="status", type="string", enum={"pending", "accepted", "declined"}, example="pending"),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'project_role_id',
        'token',
         'status',
        'created_by'

    ];

    public function projectRole():BelongsTo
    {
        return $this->belongsTo(ProjectRole::class);
    }


}
