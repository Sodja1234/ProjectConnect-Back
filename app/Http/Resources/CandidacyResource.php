<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidacyResource extends JsonResource
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
            'is_validated' => $this->is_validated,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'project_role' => [
                'id' => $this->projectRole->id,
                'description' => $this->projectRole->description,
                'role' => [
                    'id' => $this->projectRole->role->id,
                    'name' => $this->projectRole->role->name
                ]
            ]
        ];
    }
}
