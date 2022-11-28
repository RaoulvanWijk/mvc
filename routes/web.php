<?php
// Route namespace
use App\Http\Route;

// dont forget to add the controller namespace that  you want to use
use App\Http\Controllers\DemoController;

Route::get('/', [DemoController::class, 'index'], 'demo');
