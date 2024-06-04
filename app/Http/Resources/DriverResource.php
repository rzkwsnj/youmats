<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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
            'country' => new CountryResource($this->country),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'whatsapp' => $this->whatsapp,
            'rate' => $this->rate(),
            'reviews' => $this->reviews(),
            'driver_photo' => $this->getImages(DRIVER_PHOTO),
            'driver_id' => $this->getImages(DRIVER_ID),
            'driver_license' => $this->getImages(DRIVER_LICENSE),
            'car' => new CarResource($this->car),
            'active' => $this->active,
        ];
    }

    public function getImages($path) {
        $images = [];
        foreach ($this->getMedia($path) as $image) {
            $images[] = $image->getFullUrl();
        }
        return $images;
    }
}
