<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'email'           => $this->email,
            'project_role' => [
                'id' => $this->projectRole->id,

                'role' => [
                    'id' => $this->projectRole->role->id,
                    'name' => $this->projectRole->role->name
                ]
            ],

            'created_at'      => $this->created_at,

            'status'          => $this->status,

        ];
    }
}
