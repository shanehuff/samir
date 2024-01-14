<?php

use App\Http\Controllers\Pages\ChampionController;
use App\Http\Controllers\Pages\DealsController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\DailyRoiController;
use App\Http\Controllers\Pages\MonthlyRoiController;
use App\Http\Controllers\Pages\ShowProfitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\IncomesController;


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

Route::get('/', DashboardController::class);
Route::get('/dashboard', DashboardController::class);
Route::get('/deals', DealsController::class);
Route::get('/daily-roi', DailyRoiController::class);
Route::get('/monthly-roi', MonthlyRoiController::class);
Route::get('/profits/{profitId}', ShowProfitController::class)->name('profits.show');
Route::get('/incomes', IncomesController::class);
Route::get('/champions/{id}', ChampionController::class)->name('champions.show');
