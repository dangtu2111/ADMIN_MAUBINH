<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HandResultController;

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
    // Route hiển thị danh sách thiết bị
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
});
?>