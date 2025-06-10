<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'title' => $this->data->title,
            'candidacy_id' => $this->data->candidacy_id ?? null,
            'read_at' => $this->read_at,
            'message' => $this->data->message
        ];
    }
}
