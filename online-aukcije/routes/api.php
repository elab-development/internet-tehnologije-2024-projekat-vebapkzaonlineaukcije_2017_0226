<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AukcijaAPIController;


Route::apiResource('aukcije', AukcijaAPIController::class);



