<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *   schema="Portfolio",
 *   required={"name", "user_id"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique du portfolio"
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Nom du portfolio"
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     nullable=true,
 *     description="Description du portfolio"
 *   ),
 *   @OA\Property(
 *     property="link",
 *     type="string",
 *     format="url",
 *     nullable=true,
 *     description="Lien vers le portfolio"
 *   ),
 *   @OA\Property(
 *     property="user_id",
 *     type="integer",
 *     description="Identifiant de l'utilisateur propriétaire du portfolio"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de création du portfolio"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de dernière mise à jour du portfolio"
 *   )
 * )
 */

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
        return $this->belongsTo(User::class);
    } public function skills(){
        return $this->belongsToMany(Skill::class);
    }
   
}
