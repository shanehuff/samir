<?php

use App\Http\Controllers\Pages\DealsController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\DailyRoiController;
use App\Http\Controllers\Pages\MonthlyRoiController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/dashboard', DashboardController::class);
Route::get('/deals', DealsController::class);
Route::get('/daily-roi', DailyRoiController::class);
Route::get('/monthly-roi', MonthlyRoiController::class);
