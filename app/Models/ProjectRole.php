<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="ProjectRole",
 *   required={"project_id", "role_id"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique du lien projet-rôle"
 *   ),
 *   @OA\Property(
 *     property="project_id",
 *     type="integer",
 *     description="Identifiant du projet associé"
 *   ),
 *   @OA\Property(
 *     property="role_id",
 *     type="integer",
 *     description="Identifiant du rôle associé"
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     nullable=true,
 *     description="Description spécifique du rôle dans le projet"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de création de l'enregistrement"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de dernière mise à jour de l'enregistrement"
 *   )
 * )
 */

class ProjectRole extends Model
{
     protected $table = 'project_role';

    protected $fillable = [
        'project_id',
        'role_id',
        'description',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_role_skill')->withTimestamps();
    }
    public function candidacies()
    {
        return $this->hasMany(Candidacy::class);
    }

    public function applicants()
    {
        return $this->belongsToMany(User::class, 'candidacies')
            ->withPivot('is_validated')->withTimestamps();

    }
    public function invitations(){
        return $this->hasMany(Invitation::class);
    }
}
