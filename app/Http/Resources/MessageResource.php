<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'sender_type' => $this->sender_type,
            'receiver_id' => $this->receiver_id,
            'receiver_type' => $this->receiver_type,
            'message' => $this->message->message,
            'created_at' => $this->created_at
        ];
    }
}
