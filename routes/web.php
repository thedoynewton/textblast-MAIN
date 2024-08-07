<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SubAdminController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', [AuthController::class, 'index']);
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (with authentication middleware)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Messages
    Route::get('/admin/messages', [AdminController::class, 'messages'])->name('admin.messages');
    Route::post('/admin/broadcast', [MessageController::class, 'broadcastToRecipients'])->name('admin.broadcastToRecipients');
    Route::post('/admin/review-message', [MessageController::class, 'reviewMessage'])->name('admin.reviewMessage');
    Route::post('/admin/send-messages', [MessageController::class, 'sendBulkMessages'])->name('admin.send-messages');
    Route::post('/admin/broadcast-employees', [MessageController::class, 'broadcastToEmployees'])->name('admin.broadcastToEmployees');

    // Analytics
    Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');

    // User Management
    Route::get('/admin/user-management', [AdminController::class, 'userManagement'])->name('admin.user-management');
    Route::post('/admin/user-management/add-user', [AdminController::class, 'addUser'])->name('admin.add-user');
    Route::patch('/admin/user-management/{user}', [AdminController::class, 'changeRole'])->name('admin.change-role');
    Route::patch('/admin/user-management/remove-access/{user}', [AdminController::class, 'removeAccess'])->name('admin.remove-access');

    // App Management
    Route::get('/admin/app-management', [AdminController::class, 'appManagement'])->name('admin.app-management');
});

// Sub-Admin Routes (with authentication middleware)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/subadmin/dashboard', [SubAdminController::class, 'dashboard'])->name('subadmin.dashboard');

    // Messages
    Route::get('/subadmin/messages', [SubAdminController::class, 'messages'])->name('subadmin.messages');
    Route::post('/subadmin/messages/broadcast', [SubAdminController::class, 'broadcastMessages'])->name('subadmin.broadcast');

    // Analytics
    Route::get('/subadmin/analytics', [SubAdminController::class, 'analytics'])->name('subadmin.analytics');
});

// API Route for fetching dependent filters
Route::get('/api/filters/{type}/{campusId}', [FilterController::class, 'getFilters']);
Route::get('/api/filters/college/{collegeId}/programs', [FilterController::class, 'getProgramsByCollege']);
Route::get('/api/filters/office/{officeId}/types', [FilterController::class, 'getTypesByOffice']);


