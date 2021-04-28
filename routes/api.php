<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TripsController;
use App\Http\Controllers\ReservationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function (){
    Route::get('/user', [AuthController::class, 'details']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/trips', [TripsController::class, 'index']);
    Route::post('/addTrip', [TripsController::class, 'addTrip']);
    Route::Get('/trips/{trip}', [TripsController::class, 'detail']);
    Route::DELETE('/trips/{trip}/deleteTrip', [TripsController::class, 'destroy']);
    Route::POST('/trips/{trip}/reserve', [ReservationController::class, 'reserve']);
    Route::POST('/trips/{trip}/deleteReservation', [ReservationController::class, 'destroy']);
});
