<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::post('password/forget', 'AuthController@password_forgot');

Route::middleware('auth:api')->group(function () {
    Route::post('password/reset', 'AuthController@password_reset');
    Route::post('profile/update', 'AuthController@profile_update');

    Route::get('notifications/{player_id}', 'GeneralController@getAllNotifications');

    Route::post('make-request', 'TripController@makeRequest');
    Route::post('pick-driver', 'TripController@pickDriver');
    Route::post('request/{id}/give-rate', 'TripController@giveRate');

    Route::group(['prefix' => 'chat'], function () {
        Route::get('view_messages/{driver_id}', 'ChatController@viewMessage');
        Route::post('send_message', 'ChatController@sendMessage');
    });

    Route::get('trips/count', 'TripController@tripsCount');
    Route::get('trips_details/{trip_id}', 'TripController@tripDetails');
    Route::post('trips/{trip_id}/cancel', 'TripController@tripCancel');
    Route::get('trips/{type}/{count?}', 'TripController@trips');
});

Route::get('/car_types', 'TripController@carTypes');
Route::get('/get-driver/{id}', 'DriverController@getDriverById');

Route::get('/social-links', 'GeneralController@socialLinks');
