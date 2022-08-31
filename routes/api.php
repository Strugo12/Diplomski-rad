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
Route::POST('/register', [AuthController::class, 'register']);
Route::POST('/login', [AuthController::class, 'login']);
Route::POST('/forgotPassword', [AuthController::class, 'forgotPassword']);
Route::PUT('/changePassword', [AuthController::class, 'changePassword']);

Route::middleware('auth:api')->group(function (){
    Route::GET('/user', [AuthController::class, 'details']);
    Route::POST('/logout', [AuthController::class, 'logout']);

    Route::GET('/trips', [TripsController::class, 'getAll']);
    Route::GET('/trips/{id}', [TripsController::class, 'getById']);
    Route::POST('/trips', [TripsController::class, 'post']);
    Route::PUT('/trips/{id}', [TripsController::class, 'put']);
    Route::DELETE('/trips/{id}', [TripsController::class, 'delete']);

    Route::GET('/trips/{tripId}/reservations', [ReservationController::class, 'getAllByTrip']);
    Route::POST('/trips/{tripId}/reservations', [ReservationController::class, 'post']);
    Route::PUT('/trips/{tripId}/reservations', [ReservationController::class, 'put']);
    Route::DELETE('/trips/{tripId}/reservations', [ReservationController::class, 'delete']);
    Route::DELETE('/trips/{trip}/reservations/leader', [ReservationController::class, 'deleteByLeader']);
});
