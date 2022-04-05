<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HandleTradingviewHookController;
use App\Http\Controllers\Api\GetCommanderProfitController;

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

Route::middleware('auth.tradingview')->post('/tradingview', HandleTradingviewHookController::class);
Route::get('/commanders/{id}/profit', GetCommanderProfitController::class);
