<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'profile' => $this->getFirstMediaUrl(USER_PROFILE),
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'address' => $this->address,
            'address2' => $this->address2,
            'rate' => $this->rate(),
            'reviews' => $this->reviews(),
        ];
    }
}
