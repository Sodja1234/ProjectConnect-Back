<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AuthResource",
 *     type="object",
 *     title="Auth Resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="slug", type="string", example="john-doe-1"),
 *     @OA\Property(property="is_verified", type="boolean", example=true),
 *     @OA\Property(property="token", type="string", example="generated-auth-token")
 * )
 */
class AuthResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'slug' => $this->slug,
            'is_verified' => $this->email_verified_at !== null,
            'token' => $this->token,
            'role' => $this->role
        ];
    }
}
