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
Route::POST('/changePassword', [AuthController::class, 'changePassword']);

Route::middleware('auth:api')->group(function (){
    Route::GET('/user', [AuthController::class, 'details']);
    Route::POST('/logout', [AuthController::class, 'logout']);
    Route::GET('/trips', [TripsController::class, 'index']);
    Route::POST('/addTrip', [TripsController::class, 'addTrip']);
    Route::GET('/trips/{trip}', [TripsController::class, 'detail']);
    Route::DELETE('/trips/{trip}/deleteTrip', [TripsController::class, 'destroy']);
    Route::POST('/trips/{trip}/reserve', [ReservationController::class, 'reserve']);
    Route::GET('/trips/{trip}/reservations', [ReservationController::class, 'reservations']);
    Route::POST('/trips/{trip}/deleteReservation', [ReservationController::class, 'destroy']);
    Route::POST('/trips/{trip}/editTrip', [TripsController::class, 'edit']);
    Route::POST('/trips/{trip}/editReservation', [ReservationController::class, 'editReservation']);
    Route::POST('/trips/{trip}/LeaderDeleteReservation', [ReservationController::class, 'deleteReservation']);
});