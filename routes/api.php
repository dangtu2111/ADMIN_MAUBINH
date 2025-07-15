<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/
use App\Http\Controllers\DashboardStatsController;


Route::get('/dashboard-stats', [DashboardStatsController::class, 'getStats']);
Route::get('/chart-data', [DashboardStatsController::class, 'getChartData']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/game-sessions', [GameSessionController::class, 'store'])->name('game-sessions.store');
});

Route::post('/login', [AuthController::class, 'issueToken']);
?>