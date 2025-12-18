<?php

use Illuminate\Support\Facades\Route;

Route::post('/upload', [\App\Http\Controllers\API\UploadFileController::class, 'upload']);
