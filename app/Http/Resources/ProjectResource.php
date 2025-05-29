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

            // Dates formatées avec "il y a..." ou "dans..."
            'date_start' => Carbon::parse($this->date_start)->diffForHumans(),
            'date_end' => Carbon::parse($this->date_end)->diffForHumans(),

            'budget' => $this->budget,
            'location' => $this->location,

            // Si tu veux toujours garder "visibility" tel quel :
            'visibility' => $this->visibility,

            // Récupération manuelle du user via l'ID (non recommandé pour beaucoup de projets)
            'created_by' => $this->created_by ? new UserResource(User::find($this->created_by)) : null,
            'updated_by' => $this->updated_by ? new UserResource(User::find($this->updated_by)) : null,

            // Dates de création / mise à jour formatées
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),

            // Relations chargées (assure-toi qu'elles soient "with()" dans le contrôleur)
            'domains' => DomainResource::collection($this->whenLoaded('domains')),
            'project_roles_skills' => ProjectRolesResource::collection($this->whenLoaded('projectRoles')),
        ];
    }
}
