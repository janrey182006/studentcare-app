<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

/* ---------- Public pages ---------- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/track', [TrackController::class, 'index'])->middleware('throttle:30,1')->name('track');

/* ---------- Logged in, but OTP not yet entered ---------- */
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->middleware('throttle:6,1')->name('otp.verify');
    Route::post('/verify-otp/resend', [OtpController::class, 'resend'])->middleware('throttle:3,1')->name('otp.resend');
});

/* ---------- Private pages: must be logged in AND OTP-verified ---------- */
Route::middleware(['auth', 'otp'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Breeze profile pages
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD for service requests (policy decides who may do what)
    Route::resource('requests', ServiceRequestController::class)
        ->parameters(['requests' => 'serviceRequest']);
    Route::post('requests/{serviceRequest}/comments', [ServiceRequestController::class, 'comment'])->name('requests.comment');
    Route::post('requests/{serviceRequest}/cancel', [ServiceRequestController::class, 'cancel'])->name('requests.cancel');
    Route::post('requests/{serviceRequest}/rate', [ServiceRequestController::class, 'rate'])->name('requests.rate');
    Route::get('requests/{serviceRequest}/attachment', [ServiceRequestController::class, 'attachment'])->name('requests.attachment');

    // Staff + admin only
    Route::middleware('role:staff,admin')->group(function () {
        Route::patch('requests/{serviceRequest}/status', [ServiceRequestController::class, 'updateStatus'])->name('requests.status');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Admin only
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show', 'create']);
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
    });
});

require __DIR__ . '/auth.php';
