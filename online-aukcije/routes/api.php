<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AukcijaAPIController;
use App\Http\Controllers\PonudaAPIController;
use App\Http\Controllers\ProizvodAPIController;
use App\Http\Controllers\KorisnikAPIController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\NotificationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('aukcije/pretraga-po-kategoriji', [AukcijaAPIController::class, 'pretragaPoKategoriji']);

Route::apiResource('aukcije', AukcijaAPIController::class)->only(['index', 'show'])->parameters([
    'aukcije' => 'aukcija']);
Route::apiResource('aukcije.proizvodi', ProizvodAPIController::class)->only(['index', 'show'])->parameters([
    'aukcije' => 'aukcija', 'proizvodi' => 'proizvod']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::get('/korisnik',[KorisnikAPIController::class, 'show']);
    Route::patch('/korisnik',[KorisnikAPIController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/korisnici/{korisnik}/aukcije', [AukcijaAPIController::class, 'korisnickeAukcije']);

    Route::post('aukcije/{aukcija}/ponudi', [PonudaAPIController::class, 'postaviPonudu']);

    Route::apiResource('aukcije', AukcijaAPIController::class)->except(['index', 'show'])->parameters([
        'aukcije' => 'aukcija']);
    Route::apiResource('aukcije.proizvodi', ProizvodAPIController::class)->except(['index', 'show'])->parameters([
        'aukcije' => 'aukcija', 'proizvodi' => 'proizvod']);

});



