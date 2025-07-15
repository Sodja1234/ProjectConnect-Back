<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Notification",
 *   required={"id", "data", "created_at"},
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     description="Identifiant unique de la notification"
 *   ),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     description="Données associées à la notification"
 *   ),
 *   @OA\Property(
 *     property="read_at",
 *     type="string",
 *     nullable=true,
 *     description="Date de lecture de la notification ou null si non lue"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     description="Date de création de la notification (format relatif, ex : 'il y a 2 minutes')"
 *   )
 * )
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
