<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
    
     *
     * @return array<string, mixed>
     */
   public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'group_id' => $this->group_id,
            'message' => $this->message,
            'created_at' => $this->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ],
        ];
    }

}
