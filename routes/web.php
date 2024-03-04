<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::post('/deletereservation', [CalendarController::class, 'deletereservation']);
Route::post('/getreservations', [CalendarController::class, 'getreservations']);
Route::post('/getreceptions', [CalendarController::class, 'getreceptions']);
Route::post('/addreservation', [CalendarController::class, 'addreservation']);
Route::post('/getcalendar', [CalendarController::class, 'getcalendar']);
