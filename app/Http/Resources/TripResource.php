<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'driver' => new DriverResource($this->driver),
            'pickup_latitude' => $this->pickup_latitude,
            'pickup_longitude' => $this->pickup_longitude,
            'destination_latitude' => $this->destination_latitude,
            'destination_longitude' => $this->destination_longitude,
            'distance' => round($this->distance, 2),
            'price' => round($this->price, 2),
            'driver_status' => $this->driver_status,
            'status' => $this->status,
//            'driver_status' => $this->driver_status_value($this->driver_status),
//            'status' => $this->status_value($this->status),
            'pickup_date' => $this->pickup_date,
            'started_at' => $this->started_at,
            'user_rate' => $this->user_rate,
            'user_review' => $this->user_review,
            'driver_rate' => $this->driver_rate,
            'driver_review' => $this->driver_review
        ];
    }

    private function driver_status_value($status) {
        $values = [
            '0' => 'Pending',
            '1' => 'Accepted'
        ];
        return $values[$status];
    }
    private function status_value($status) {
        $values = [
            '0' => 'Pending',
            '1' => 'In progress',
            '2' => 'Completed',
            '3' => 'Canceled'
        ];
        return $values[$status];
    }
}
