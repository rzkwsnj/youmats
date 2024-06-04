<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Driver\CarRequest;
use App\Http\Resources\CarResource;
use App\Http\Resources\CarTypeResource;
use App\Models\Car;
use App\Models\CarType;
use App\Nova\Driver;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    public function index() {
        $driver = Auth::guard('driver-api')->user();
        $car = $driver->car;
        if(isset($car))
            return new CarResource($car);
        else
            return response()->json(['message' => 'No added car'], 400);
    }

    public function store(CarRequest $request) {
        $data = $request->validated();
        $driver_id = Auth::guard('driver-api')->id();

        if (count(Car::where('driver_id',  $driver_id)->get()) >= 1) {
            return response()->json(['message' => 'Car Already Added!'], 400);
        }

        $data['driver_id'] = $driver_id;

        $car = Car::create($data);

        if(isset($request->car_photo))
            foreach($request->car_photo as $image) {
                $car->addMedia($image)->toMediaCollection(CAR_PHOTO);
            }

        if(isset($request->car_license))
            foreach($request->car_license as $image) {
                $car->addMedia($image)->toMediaCollection(CAR_LICENSE);
            }

        $car->setTranslation('name', 'en', $data['name_en']);
        $car->setTranslation('name', 'ar', $data['name_ar']);

        $car->save();

        return response()->json([
            'message' => 'Car Added Successfully.',
            'car' => new CarResource($car)
        ], 200);
    }

    public function update(CarRequest $request) {
        $data = $request->validated();
        $driver = Auth::guard('driver-api')->user();
        $car = $driver->car;

        if(isset($request->car_photo))
            foreach($request->car_photo as $image) {
                $car->addMedia($image)->toMediaCollection(CAR_PHOTO);
            }

        if(isset($request->car_license))
            foreach($request->car_license as $image) {
                $car->addMedia($image)->toMediaCollection(CAR_LICENSE);
            }

        $car->setTranslation('name', 'en', $data['name_en']);
        $car->setTranslation('name', 'ar', $data['name_ar']);

        $car->update($data);

        return response()->json([
            'message' => 'Car Updated Successfully.',
            'car' => new CarResource($car)
        ], 200);
    }

    public function delete() {
        $driver = Auth::guard('driver-api')->user();
        if($driver->car) {
            $driver->car->delete();
            return response()->json(['message' => 'Car Deleted Successfully.'], 200);
        }
        return response()->json(['message' => 'This car doesn\'t exists!'], 400);
    }

    public function getCarTypes() {
        return CarTypeResource::collection(CarType::all());
    }

//    public function deleteImage($car_id, $collectionName, $collection_id) {
//        $driver_id = Auth::guard('driver-api')->id();
//        $car = Car::where([
//            'id' => $car_id,
//            'driver_id' => $driver_id
//        ])->first();
//
//        if($car) {
//            $car->clearMediaCollection($collectionName, $collection_id);
//            return response()->json(['message' => 'Image Deleted Successfully.'], 200);
//        }
//        return response()->json(['message' => 'This car doesn\'t exists!'], 400);
//    }
}
