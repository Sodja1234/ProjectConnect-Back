<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        Carbon::setLocale('fr');
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,


            'date_start' => $this->date_start,
            'date_end' =>$this->date_end ,

            'budget' => $this->budget,
            'location' => $this->location,
            'status' => $this->status,


            // Si tu veux toujours garder "visibility" tel quel :
            'visibility' => $this->visibility,

            // Récupération manuelle du user qui a créé le projet
            'created_by' => $this->created_by ? new UserResource(User::find($this->created_by)) : null,
            'updated_by' => $this->updated_by ? new UserResource(User::find($this->updated_by)) : null,

            // Dates de création
            'created_at' => $this->created_at,
            'updated_at' =>$this->updated_at,

            // Relations chargées (assure-toi qu'elles soient "with()" dans le contrôleur)
            'domains' => DomainResource::collection($this->whenLoaded('domains')),
            'project_roles_skills' => ProjectRolesResource::collection($this->whenLoaded('projectRoles')),
            'total_candidacies_count' => $this->projectRoles->sum(function ($roleSkill) {
                return $roleSkill->candidacies()->where('is_validated', true)->count();
            }),
            'total_pending_invitations' => $this->projectRoles->sum(function ($roleSkill) {
                     return $roleSkill->invitations()->where('status', 'pending')->count();
                }),

        ];
    }
}
