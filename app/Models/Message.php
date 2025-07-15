<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *   schema="Message",
 *   required={"chat_id", "sender_id", "message"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     readOnly=true,
 *     description="Identifiant unique du message"
 *   ),
 *   @OA\Property(
 *     property="chat_id",
 *     type="integer",
 *     description="Identifiant du chat auquel appartient ce message"
 *   ),
 *   @OA\Property(
 *     property="sender_id",
 *     type="integer",
 *     description="Identifiant de l'utilisateur qui a envoyé le message"
 *   ),
 *   @OA\Property(
 *     property="message",
 *     type="string",
 *     description="Contenu du message"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de création du message"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     readOnly=true,
 *     description="Date de dernière modification du message"
 *   )
 * )
 */
class Message extends Model
{
    protected $fillable = ['chat_id', 'sender_id', 'message'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

 
}
