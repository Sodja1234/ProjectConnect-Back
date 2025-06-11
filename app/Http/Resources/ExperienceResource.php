<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'position'    => $this->position,
            'company'     => $this->company,
            'date_start'  => $this->date_start,
            'date_end'    => $this->date_end,
            'description' => $this->description,
            'user'        => new UserResource($this->whenLoaded('created_by')), // Optionnel si tu veux inclure l'utilisateur
             
          
        ];
        
    }
}
