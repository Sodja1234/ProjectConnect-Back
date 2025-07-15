<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *   schema="Skill",
 *   required={"name"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique de la compétence"
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Nom de la compétence"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de création de la compétence"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de dernière mise à jour de la compétence"
 *   )
 * )
 */

class Skill extends Model
{
    protected $fillable = ['name'];
     public function portfolio()
    {
        return $this->belongsToMany(portfolio::class);
    }
    public function users(): BelongsToMany{
        return $this->belongsToMany(user::class)->withPivot('experience_percentage');
     }

}


