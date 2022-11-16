<?php

use App\Http\Controllers\Api\GetBalancesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HandleTradingviewHookController;
use App\Http\Controllers\Api\GetCommanderProfitController;
use App\Http\Controllers\Api\GetCommanderRiskController;
use App\Http\Controllers\Api\ProfitController;

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
Route::get('/commanders/{id}/risk', GetCommanderRiskController::class);
Route::middleware('auth.tradingview')
    ->post('/balances', GetBalancesController::class);

Route::get('/profit', ProfitController::class);
