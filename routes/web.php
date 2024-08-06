<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SubAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index']);

// Callback
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Logout user
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Redirect to Google
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');

        //<ADMIN ROUTES>
// Admin dashboard route
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth');

// Admin messages
Route::get('/admin/messages', [AdminController::class, 'messages'])->name('admin.messages');
Route::post('/admin/broadcast', [MessageController::class, 'broadcastToRecipients'])->name('admin.broadcastToRecipients');
// Route::post('/admin/send-messages', [AdminController::class, 'sendMessages'])->name('admin.send-messages')->middleware('auth');

// Admin analytics route
Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics')->middleware('auth');

// Admin user management routes
Route::get('/admin/user-management', [AdminController::class, 'userManagement'])->name('admin.user-management')->middleware('auth');
Route::post('/admin/user-management/add-user', [AdminController::class, 'addUser'])->name('admin.add-user')->middleware('auth');
Route::patch('/admin/user-management/{user}', [AdminController::class, 'changeRole'])->name('admin.change-role')->middleware('auth');
Route::patch('/admin/user-management/remove-access/{user}', [AdminController::class, 'removeAccess'])->name('admin.remove-access')->middleware('auth');

// Admin app management route
Route::get('/admin/app-management', [AdminController::class, 'appManagement'])->name('admin.app-management')->middleware('auth');
        //</ADMIN ROUTES>


        //<SUB-ADMIN ROUTES>
// Subadmin dashboard route
Route::get('/subadmin/dashboard', [SubAdminController::class, 'dashboard'])->name('subadmin.dashboard')->middleware('auth');

// Admin messages route
Route::get('/subadmin/messages', [SubAdminController::class, 'messages'])->name('subadmin.messages');
Route::post('/subadmin/messages/broadcast', [SubAdminController::class, 'broadcastMessages'])->name('subadmin.broadcast');

// Admin analytics route
Route::get('/subadmin/analytics', [SubAdminController::class, 'analytics'])->name('subadmin.analytics')->middleware('auth');
        //</SUB-ADMIN ROUTES>

//subadmin send messages route
Route::post('/admin/send-messages', [MessageController::class, 'sendBulkMessages'])->name('admin.send-messages');
Route::post('/admin/broadcast-employees', [MessageController::class, 'broadcastToEmployees'])->name('admin.broadcastToEmployees');

// API Route for fetching dependent filters
Route::get('/api/filters/{type}/{campusId}', [FilterController::class, 'getFilters']);