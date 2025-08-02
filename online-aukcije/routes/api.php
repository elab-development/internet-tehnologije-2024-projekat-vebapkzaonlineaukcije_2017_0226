<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AukcijaAPIController;
use App\Http\Controllers\PonudaAPIController;


Route::apiResource('aukcije', AukcijaAPIController::class);

Route::post('aukcije/{aukcija}/ponudi', [PonudaAPIController::class, 'postaviPonudu']);



