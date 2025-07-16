<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Experience",
 *   required={"created_by", "position", "company", "date_start", "user_id"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique de l'expérience"
 *   ),
 *   @OA\Property(
 *     property="created_by",
 *     type="integer",
 *     description="Identifiant de l'utilisateur ayant créé l'expérience"
 *   ),
 *   @OA\Property(
 *     property="position",
 *     type="string",
 *     description="Poste occupé"
 *   ),
 *   @OA\Property(
 *     property="company",
 *     type="string",
 *     description="Nom de l'entreprise"
 *   ),
 *   @OA\Property(
 *     property="date_start",
 *     type="string",
 *     format="date",
 *     description="Date de début de l'expérience"
 *   ),
 *   @OA\Property(
 *     property="date_end",
 *     type="string",
 *     format="date",
 *     nullable=true,
 *     description="Date de fin de l'expérience (peut être null si en cours)"
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     nullable=true,
 *     description="Description de l'expérience"
 *   ),
 *   @OA\Property(
 *     property="user_id",
 *     type="integer",
 *     description="Identifiant de l'utilisateur associé à cette expérience"
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
