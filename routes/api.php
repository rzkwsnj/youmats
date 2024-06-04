<?php

use App\Http\Controllers\Api\GenerateProductController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Statistics\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('loadData/{category}/product/{product?}', [TemplateController::class, 'loadData']);
Route::get('loadData/{category}/model/{model?}', [GenerateProductController::class, 'loadData']);

Route::post('setLog', [IndexController::class, 'setLog'])->name('log.set');
Route::post('setAction', [IndexController::class, 'setAction'])->name('log.action');
