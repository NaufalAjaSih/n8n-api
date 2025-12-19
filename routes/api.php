<?php

use Illuminate\Support\Facades\Route;

Route::post('/upload', [\App\Http\Controllers\API\UploadFileController::class, 'upload']);
Route::post('/work-orders', [\App\Http\Controllers\API\WorkOrderController::class, 'store']);
