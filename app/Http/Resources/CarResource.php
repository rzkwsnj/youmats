<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
//            'driver' => new DriverResource($this->driver),
            'driver' => $this->driver->name,
            'type' => new CarTypeResource($this->type),
            'name' => $this->name,
            'model' => $this->model,
            'license_no' => $this->license_no,
            'max_load' => $this->max_load,
            'price_per_kilo' => $this->price_per_kilo,
            'car_photo' => $this->getImages(CAR_PHOTO),
            'car_license' => $this->getImages(CAR_LICENSE)
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
