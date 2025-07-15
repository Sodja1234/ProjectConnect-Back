<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Domain",
 *   required={"name"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique du domaine"
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Nom du domaine"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de création du domaine"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de dernière mise à jour du domaine"
 *   )
 * )
 */

class Domain extends Model
{
    protected $fillable = ["name"];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
