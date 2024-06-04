<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Driver\GiveRateRequest;
use App\Http\Requests\Api\User\MakeRequestRequest;
use App\Http\Requests\Api\User\PickDriverRequest;
use App\Http\Resources\CarDriverListResource;
use App\Http\Resources\CarTypeResource;
use App\Http\Resources\TripResource;
use App\Models\Car;
use App\Models\CarType;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller {
    public function makeRequest(MakeRequestRequest $request) {
        $data = $request->validated();
        $user_id = Auth::guard('api')->id();

        $distance = haversineGreatCircleDistance($data['pickup_latitude'], $data['pickup_longitude'], $data['destination_latitude'], $data['destination_longitude']);
        $cars = Car::where('active', 1)->where('type_id', $data['car_type_id'])->get();

        if(!count($cars)) {
            return response()->json(['message' => 'Not available cars now!'], 400);
        }

        foreach ($cars as $car) {
            $avgPrice[] = $car->price_per_kilo * $distance;
        }
        $trip = Trip::create([
            'user_id' => $user_id,
            'pickup_latitude' => $data['pickup_latitude'],
            'pickup_longitude' => $data['pickup_longitude'],
            'destination_latitude' => $data['destination_latitude'],
            'destination_longitude' => $data['destination_longitude'],
            'pickup_date' => $data['pickup_date'],
            'distance' => $distance,
            'driver_status' => '0',
            'status' => '0'
        ]);

        return (new TripResource($trip))->additional([
            'available_cars' => CarDriverListResource::collection($cars),
            'estimated_price' => round(array_sum($avgPrice) / count($avgPrice)),
            'cars_available' => count($cars)
        ]);
    }

    public function pickDriver(PickDriverRequest $request) {
        $data = $request->validated();
        $user_id = Auth::guard('api')->id();

        $trip = Trip::where([
            'id' => $data['trip_id'],
            'user_id' => $user_id
        ])->first();

        $trip->update([
            'driver_id' => $data['driver_id']
        ]);

        return new TripResource($trip);
    }

    public function giveRate(GiveRateRequest $request, $id) {
        $user_id = Auth::guard('api')->id();
        $data = $request->validated();
        $trip = Trip::where([
            'user_id' => $user_id,
            'id' => $id,
            'driver_status' => '1',
            'status' => '2'
        ])->first();
        if($trip) {
            $trip->update([
                'driver_rate' => $data['rate'],
                'driver_review' => $data['review']
            ]);
            return response()->json([
                'message' => 'Rate Submit Successfully.',
                'trip' => new TripResource($trip)
            ], 200);
        }
        return response()->json(['message' => 'Trip dosn\'t exist.'], 400);
    }

    public function tripsCount() {
        $user_id = Auth::guard('api')->id();
        return response()->json([
            'count' => Trip::where('user_id', $user_id)->orderBy('id', 'desc')->count()
        ], 200);
    }

    public function trips($type, $count = 3) {
        $user_id = Auth::guard('api')->id();
        $trips = null;
        if($type == 'recent') {
            $trips = Trip::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->take($count)->get();
        } elseif ($type == 'pending') {
            $trips = Trip::where('user_id', $user_id)
                ->where('driver_status', '0')
                ->where('status', '0')
                ->orderBy('id', 'desc')
                ->take($count)->get();
        } elseif ($type == 'past') {
            $trips = Trip::where('user_id', $user_id)
                ->where('status', '2')
                ->orderBy('id', 'desc')
                ->take($count)->get();
        } else {
            return response()->json(['message' => 'Choose Right Type (recent, pending, past)'], 400);
        }
        return TripResource::collection($trips);
    }

    public function tripDetails($trip_id) {
        $user_id = Auth::guard('api')->id();
        $trip = Trip::where([
            'user_id' => $user_id,
            'id' => $trip_id
        ])->first();

        if($trip) {
            return new TripResource($trip);
        }
        return response()->json(['message' => 'Trip dosn\'t exists.'], 400);
    }

    public function tripCancel($trip_id) {
        $user_id = Auth::guard('api')->id();
        $trip = Trip::where([
            'user_id' => $user_id,
            'id' => $trip_id,
        ])->where('status', '!=', '2')->first();

        if($trip) {
            $trip->update([
//                'driver_id' => null,
//                'driver_status' => '0',
                'status' => '3',
//                'started_at' => null,
//                'price' => null
            ]);
            return response()->json([
                'message' => 'Trip Cancelled Successfully.',
                'trip' => new TripResource($trip)
            ], 200);
        }

        return response()->json(['message' => 'Request doesn\'t exists.'], 400);
    }

    public function carTypes() {
        $types = CarType::all();
        return CarTypeResource::collection($types);
    }
}
