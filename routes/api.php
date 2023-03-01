<?php

use App\Http\HttpKernel\Request;
use App\Http\HttpKernel\Route;

Route::post('/component/{type}', [\App\Http\Controllers\ComponentController::class, 'handle']);