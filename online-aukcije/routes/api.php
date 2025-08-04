<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AukcijaAPIController;
use App\Http\Controllers\PonudaAPIController;
use App\Http\Controllers\ProizvodAPIController;

Route::get('aukcije/pretraga-po-kategoriji', [AukcijaAPIController::class, 'pretragaPoKategoriji']);

Route::post('aukcije/{aukcija}/ponudi', [PonudaAPIController::class, 'postaviPonudu']);

Route::apiResource('aukcije.proizvodi', ProizvodAPIController::class)->parameters([
    'aukcije' => 'aukcija']);

Route::apiResource('aukcije', AukcijaAPIController::class)->parameters([
    'aukcije' => 'aukcija']);



