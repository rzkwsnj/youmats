<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::post('password/forget', 'AuthController@password_forgot');

Route::middleware('auth:driver-api')->group(function () {
    Route::post('password/reset', 'AuthController@password_reset');
    Route::post('profile/update', 'AuthController@profile_update');
    Route::post('profile/update-photos', 'AuthController@updatePhotos');

    Route::get('notifications/{player_id}', 'GeneralController@getAllNotifications');

    Route::group(['prefix' => 'car'], function () {
        Route::get('/', 'CarController@index');
        Route::post('/store', 'CarController@store');
        Route::put('/update', 'CarController@update');
        Route::delete('/delete', 'CarController@delete');
//        Route::delete('/delete-image/{car_id}/{collection_name}/{collection_id}', 'CarController@deleteImage');
    });

    Route::get('requests/count', 'TripController@requestsCount');
    Route::get('all-requests', 'TripController@getAllRequests');
    Route::get('pending-requests', 'TripController@getPendingRequests');
    Route::get('request/{id}', 'TripController@getRequestById');
    Route::post('request/{id}/response', 'TripController@requestResponse');
    Route::post('request/{id}/complete', 'TripController@requestComplete');
    Route::post('request/{id}/cancel', 'TripController@cancelRequest');
    Route::post('request/{id}/give-rate', 'TripController@giveRate');

    Route::group(['prefix' => 'chat'], function () {
        Route::get('view_messages/{user_id}', 'ChatController@viewMessage');
        Route::post('send_message', 'ChatController@sendMessage');
    });

});

Route::get('/car-types', 'CarController@getCarTypes');
Route::get('/countries', 'CountryController@index');

Route::get('/social-links', 'GeneralController@socialLinks');
