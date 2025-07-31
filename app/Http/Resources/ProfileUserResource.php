<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->profile->id ?? null,
            'email' => $this->email,
            'name' => $this->name,
            'job_title' => $this->profile->job_title ?? null,
            'phone' => $this->profile->phone ?? null,
            'about' => $this->profile->about ?? null,
            'location' => $this->profile->location ?? null,
            'is_availability' => $this->profile->is_availability ?? null,
            'profile_photo' => $this->profile->profile_photo ?? null,
            'created_at' => $this->profile->created_at ?? null,
            'updated_at' => $this->profile->updated_at ?? null,
        ];
    }
}
