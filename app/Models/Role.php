<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Role",
 *   required={"name"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique du rôle"
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Nom du rôle"
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     nullable=true,
 *     description="Description du rôle"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de création du rôle"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de dernière mise à jour du rôle"
 *   )
 * )
 */

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function projects()
{
    return $this->belongsToMany(Project::class, 'project_role')
                ->withPivot('description');
                
}
}