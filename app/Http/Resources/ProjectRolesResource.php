<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectRolesResource extends JsonResource
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

            'description' => $this->description,

            'role' => new RoleResource($this->whenLoaded('role')),
            'skills' => SkillsResource::collection($this->whenLoaded('skills')),
            'candidacies_count' => $this->candidacies()->where('is_validated', true)->count(),
            'invitations_count' => $this->invitations()->where('status', 'pending')->count(),
        ];
    }
}
