<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AukcijaAPIController;
use App\Http\Controllers\PonudaAPIController;
use App\Http\Controllers\ProizvodAPIController;
use App\Http\Controllers\API\AuthController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('aukcije/pretraga-po-kategoriji', [AukcijaAPIController::class, 'pretragaPoKategoriji']);

Route::apiResource('aukcije', AukcijaAPIController::class)->only(['index', 'show'])->parameters([
    'aukcije' => 'aukcija']);

Route::apiResource('aukcije.proizvodi', ProizvodAPIController::class)->only(['index', 'show'])->parameters([
    'aukcije' => 'aukcija', 'proizvodi' => 'proizvod']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('aukcije/{aukcija}/ponudi', [PonudaAPIController::class, 'postaviPonudu']);

    Route::apiResource('aukcije', AukcijaAPIController::class)->except(['index', 'show'])->parameters([
        'aukcije' => 'aukcija']);

    Route::apiResource('aukcije.proizvodi', ProizvodAPIController::class)->except(['index', 'show'])->parameters([
        'aukcije' => 'aukcija', 'proizvodi' => 'proizvod']);

});



