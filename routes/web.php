<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\SubAdminController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', [AuthController::class, 'index']);
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/login', function () {
    return redirect()->route('google.login');
})->name('login');

Route::get('/access-denied', function () {
    return view('access-denied');
})->name('access.denied');

Route::post('login/email', [AuthController::class, 'loginWithEmail'])->name('login.email');

// Admin Routes (with authentication and role middleware)
Route::middleware(['auth', CheckRole::class . ':admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Messages
    Route::get('/admin/messages', [MessageController::class, 'showMessagesForm'])->name('admin.messages');
    Route::post('/admin/broadcast', [MessageController::class, 'broadcastToRecipients'])->name('admin.broadcastToRecipients');
    Route::post('/admin/review-message', [MessageController::class, 'reviewMessage'])->name('admin.reviewMessage');
    Route::post('/admin/send-messages', [MessageController::class, 'sendBulkMessages'])->name('admin.send-messages');
    Route::post('/admin/messages/cancel/{id}', [MessageController::class, 'cancelScheduledMessage'])->name('admin.cancelScheduledMessage');

    // Message Logs
    Route::get('/admin/message-logs', [MessageController::class, 'getMessageLogs'])->name('admin.messageLogs');

    // Message Templates CRUD
    Route::get('/admin/app-management/message-templates', [MessageTemplateController::class, 'index'])->name('message_templates.index');
    Route::get('/admin/app-management/message-templates/create', [MessageTemplateController::class, 'create'])->name('message_templates.create');
    Route::post('/admin/app-management/message-templates', [MessageTemplateController::class, 'store'])->name('message_templates.store');
    Route::get('/admin/app-management/message-templates/{id}/edit', [MessageTemplateController::class, 'edit'])->name('message_templates.edit');
    Route::put('/admin/app-management/message-templates/{id}', [MessageTemplateController::class, 'update'])->name('message_templates.update');
    Route::delete('/admin/app-management/message-templates/{id}', [MessageTemplateController::class, 'destroy'])->name('message_templates.destroy');

    // Analytics
    Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');

    // User Management
    Route::get('/admin/user-management', [AdminController::class, 'userManagement'])->name('admin.user-management');
    Route::post('/admin/user-management/add-user', [AdminController::class, 'addUser'])->name('admin.add-user');
    Route::patch('/admin/user-management/{user}', [AdminController::class, 'changeRole'])->name('admin.change-role');
    Route::patch('/admin/user-management/remove-access/{user}', [AdminController::class, 'removeAccess'])->name('admin.remove-access');

    // App Management
    Route::get('/admin/app-management', [AdminController::class, 'appManagement'])->name('admin.app-management');
    
    // Update Contact Number
    Route::post('/admin/update-contact-number', [AdminController::class, 'updateContactNumber'])->name('admin.update-contact-number');
});

// Sub-Admin Routes (with authentication and role middleware)
Route::middleware(['auth', CheckRole::class . ':subadmin'])->group(function () {
    // Dashboard
    Route::get('/subadmin/dashboard', [SubAdminController::class, 'dashboard'])->name('subadmin.dashboard');

    // Messages
    Route::get('/subadmin/messages', [MessageController::class, 'showMessagesForm'])->name('subadmin.messages');
    Route::post('/subadmin/broadcast', [MessageController::class, 'broadcastToRecipients'])->name('subadmin.broadcastToRecipients');
    Route::post('/subadmin/review-message', [MessageController::class, 'reviewMessage'])->name('subadmin.reviewMessage');
    Route::post('/subadmin/send-messages', [MessageController::class, 'sendBulkMessages'])->name('subadmin.send-messages');
    Route::post('/subadmin/messages/cancel/{id}', [MessageController::class, 'cancelScheduledMessage'])->name('subadmin.cancelScheduledMessage');

    // Analytics
    Route::get('/subadmin/analytics', [SubAdminController::class, 'analytics'])->name('subadmin.analytics');
});

// API Route for fetching dependent filters
Route::get('/api/filters/{type}/{campusId}', [FilterController::class, 'getFilters']);
Route::get('/api/filters/college/{collegeId}/programs', [FilterController::class, 'getProgramsByCollege']);
Route::get('/api/filters/types/{campusId}/{officeId}/{statusId?}', [FilterController::class, 'getTypesByOffice']);
Route::get('/api/contacts', [FilterController::class, 'getContacts']);
Route::get('/api/recipients/count', [MessageController::class, 'getRecipientCount']);
Route::get('/api/progress/{logId}', [MessageController::class, 'getProgress']);
Route::get('/api/analytics', [MessageController::class, 'getAnalyticsData'])->name('api.analytics');
Route::get('/api/filters/program/{programId}/majors', [FilterController::class, 'getMajorsByProgram']);