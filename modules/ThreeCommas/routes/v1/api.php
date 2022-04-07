<?php

use Illuminate\Support\Facades\Route;
use Modules\ThreeCommas\Http\Controllers\V1\BotController;

Route::get('/bots/{id}', [BotController::class, 'show'])->name('bots.show');
Route::get('/bots', [BotController::class, 'index'])->name('bots.index');