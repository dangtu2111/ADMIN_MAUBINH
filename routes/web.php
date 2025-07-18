<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HandResultController;
use App\Http\Controllers\DashboardStatsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Route hiển thị form đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');

// Route xử lý đăng nhập
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

// Route xử lý đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route cho admin quản lý tài khoản
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [AdminController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});



// Nhóm các route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    // Route mặc định
    Route::get('/', function () {
        return auth()->check() ? redirect('/dashboard') : redirect('/login');
    })->name('home');

    // Route cho dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Route cho analytics
    Route::get('/analytics', function () {
        return view('analytics.index');
    })->name('analytics.index');

    // Route cho danh sách hand
    Route::get('/listhand', [HandResultController::class, 'index'])->name('listhand.index');
    Route::delete('/hand-results/{id}', [HandResultController::class, 'destroy'])->name('hand-results.destroy');
    // Route cho chỉnh sửa HandResult
    Route::get('/hand-results/{id}/edit', [HandResultController::class, 'edit'])->name('hand-results.edit');
    Route::post('/hand-results/{id}', [HandResultController::class, 'update'])->name('hand-results.update');
    // Route hiển thị danh sách thiết bị
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::delete('/devices/delete/{id}', [DeviceController::class, 'destroyRevenue'])->name('revenues.destroy');
    Route::get('/line-chart-data', [DashboardStatsController::class, 'lineChartData'])->name('lineChartData');
    Route::get('/device-chi-win-rate', [DashboardStatsController::class, 'getDeviceChiWinRate']);
    Route::get('/devices/revenue', [DashboardStatsController::class, 'deviceResult'])->name('devicesRevenue');
    Route::get('/compare-money', [DeviceController::class, 'compareMoneyByTimeRanges'])->name('devices.compare-money');
    Route::get('/revenues-by-serial', [DeviceController::class, 'getRevenuesBySerial'])->name('devices.get-revenues-by-serial');
    Route::get('/hand-results/range', [DeviceController::class, 'getHandResultsInRange'])->name('hand-results.range');

});
?>