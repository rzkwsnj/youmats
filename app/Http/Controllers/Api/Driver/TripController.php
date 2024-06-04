<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Driver\GiveRateRequest;
use App\Http\Requests\Api\Driver\RequestResponseRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function requestsCount() {
        $driver_id = Auth::guard('driver-api')->id();
        return response()->json([
            'count' => Trip::where('driver_id', $driver_id)->count()
        ], 200);
    }

    public function getAllRequests() {
        $driver_id = Auth::guard('driver-api')->id();
        $trips = Trip::where('driver_id', $driver_id)->get();
        return TripResource::collection($trips);
    }

    public function getPendingRequests() {
        $driver_id = Auth::guard('driver-api')->id();
        $trips = Trip::where('driver_id', $driver_id)
            ->where('driver_status', '0')->get();
        return TripResource::collection($trips);
    }

    public function getRequestById($id) {
        $driver_id = Auth::guard('driver-api')->id();
        $trip = Trip::where([
            'driver_id' => $driver_id,
            'id' => $id
        ])->first();

        if($trip) {
            return new TripResource($trip);
        }
        return response()->json(['message' => 'Request dosn\'t exists.'], 400);
    }

    public function requestResponse(RequestResponseRequest $request, $id) {
        $driver = Auth::guard('driver-api')->user();
        $data = $request->validated();
        $trip = Trip::where([
            'driver_id' => $driver->id,
            'id' => $id
        ])->first();
        if($trip) {
            if($trip->driver_status == '0') {
                if($data['response'] == '1') {
                    $trip->update([
                        'driver_status' => '1',
                        'status' => '1',
                        'started_at' => now(),
                        'price' => $trip->distance * $driver->car->price_per_kilo
                    ]);
                    return response()->json([
                        'message' => 'Request Accepted Successfully.',
                        'trip' => new TripResource($trip)
                    ], 200);
                } elseif($data['response'] == '2') {
                    $trip->update([
                        'driver_id' => null
                    ]);
                    return response()->json([
                        'message' => 'Request Rejected.',
                        'trip' => new TripResource($trip)
                    ], 200);
                }
            }
            return response()->json(['message' => 'Request Already Updated.'], 400);
        }
        return response()->json(['message' => 'Request dosn\'t exists.'], 400);
    }

    public function requestComplete($id) {
        $driver = Auth::guard('driver-api')->user();
        $trip = Trip::where([
            'driver_id' => $driver->id,
            'id' => $id,
            'driver_status' => '1',
            'status' => '1'
        ])->first();

        if($trip) {
            $trip->update([
                'status' => '2'
            ]);
            return response()->json([
                'message' => 'Trip Completed Successfully.',
                'trip' => new TripResource($trip)
            ], 200);
        }

        return response()->json(['message' => 'Request dosn\'t exists.'], 400);
    }

    public function cancelRequest($id) {
        $driver = Auth::guard('driver-api')->user();
        $trip = Trip::where([
            'driver_id' => $driver->id,
            'id' => $id,
            'driver_status' => '1',
            'status' => '1'
        ])->first();

        if($trip) {
            $trip->update([
                'driver_id' => null,
                'driver_status' => '0',
                'status' => '0',
                'started_at' => null,
                'price' => null
            ]);
            return response()->json([
                'message' => 'Trip Cancelled Successfully.',
                'trip' => new TripResource($trip)
            ], 200);
        }

        return response()->json(['message' => 'Request dosn\'t exists.'], 400);
    }

    public function giveRate(GiveRateRequest $request, $id) {
        $driver_id = Auth::guard('driver-api')->id();
        $data = $request->validated();
        $trip = Trip::where([
            'driver_id' => $driver_id,
            'id' => $id,
            'driver_status' => '1',
            'status' => '2'
        ])->first();
        if($trip) {
            $trip->update([
                'user_rate' => $data['rate'],
                'user_review' => $data['review']
            ]);
            return response()->json([
                'message' => 'Rate Submit Successfully.',
                'trip' => new TripResource($trip)
            ], 200);
        }
        return response()->json(['message' => 'Trip dosn\'t exist.'], 400);
    }
}
