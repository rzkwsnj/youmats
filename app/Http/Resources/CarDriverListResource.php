<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarDriverListResource extends JsonResource
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
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'phone2' => $this->phone2,
                'whatsapp' => $this->whatsapp,
                'driver_photo' => $this->getFirstMediaUrl(DRIVER_PHOTO),
            ],
            'model' => $this->model,
            'type' => new CarTypeResource($this->type),
            'license_no' => $this->license_no,
            'max_load' => $this->max_load,
            'price_per_kilo' => $this->price_per_kilo,
            'car_photo' => $this->getFirstMediaUrl(CAR_PHOTO)
        ];
    }
}
