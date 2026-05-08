<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lms\LmsDashboardController;
use App\Http\Controllers\Lms\LmsLeadController;
use App\Http\Controllers\Lms\LmsNoteController;
use App\Http\Controllers\Lms\LmsFollowUpController;
use App\Http\Controllers\Lms\LmsExportController;
use App\Http\Controllers\Lms\LmsNotificationController;

Route::middleware(['web', 'auth', 'lms.access'])->prefix('lms')->name('lms.')->group(function () {
    // Dashboard
    Route::get('/', [LmsDashboardController::class, 'index'])->name('dashboard');

    // Leads CRUD
    Route::get('/leads', [LmsLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/create', [LmsLeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [LmsLeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LmsLeadController::class, 'show'])->name('leads.show');
    Route::get('/leads/{lead}/edit', [LmsLeadController::class, 'edit'])->name('leads.edit');
    Route::put('/leads/{lead}', [LmsLeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [LmsLeadController::class, 'destroy'])->name('leads.destroy');

    // Lead status update
    Route::put('/leads/{lead}/status', [LmsLeadController::class, 'updateStatus'])->name('leads.status');

    // Lead notes
    Route::post('/leads/{lead}/notes', [LmsNoteController::class, 'store'])->name('leads.notes.store');

    // Lead follow-ups
    Route::post('/leads/{lead}/follow-ups', [LmsFollowUpController::class, 'store'])->name('leads.follow-ups.store');

    // Follow-up completion (not nested under leads)
    Route::put('/follow-ups/{followUp}/complete', [LmsFollowUpController::class, 'complete'])->name('follow-ups.complete');

    // Export
    Route::get('/export', [LmsExportController::class, 'export'])->name('export');

    // Notifications
    Route::get('/notifications', [LmsNotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{notification}/read', [LmsNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [LmsNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});
