<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/photos/{filename}', function (string $filename) {
    $safeFilename = basename($filename);

    // 1. Check if it's an uploaded event photo
    $uploadPath = public_path('uploads/events/'.$safeFilename);
    if (File::exists($uploadPath)) {
        return response()->file($uploadPath, [
            'Content-Type' => File::mimeType($uploadPath),
        ]);
    }

    // 2. Check fallback/static photos in resources/photos
    $resourcePath = resource_path('photos/'.$safeFilename);
    if (File::exists($resourcePath)) {
        return response()->file($resourcePath, [
            'Content-Type' => File::mimeType($resourcePath),
        ]);
    }

    abort(404);
})->where('filename', '.*')->name('public.photos');

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/incident-reporting', [PublicPageController::class, 'incident'])->name('public.incidents');
Route::post('/incident-reporting', [PublicPageController::class, 'submitIncidentReport'])->name('public.incidents.submit');
Route::get('/document-requests', [PublicPageController::class, 'documents'])->name('public.documents');
Route::post('/document-requests', [PublicPageController::class, 'submitDocumentRequest'])->name('public.documents.submit');
Route::get('/event-registration', [PublicPageController::class, 'events'])->name('public.events');

// --- Pointed to EventRegistrationController ---
Route::post('/event-registration', [EventRegistrationController::class, 'store'])->name('public.events.submit');

Route::get('/admin/login', [AdminAuthController::class, 'loginForm'])->name('admin.login');
Route::get('/admin/register', [AdminAuthController::class, 'registerForm'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::post('/admin/residents', [ResidentController::class, 'store'])->name('admin.residents.store');
Route::post('/admin/residents/assign-household', [ResidentController::class, 'assignHousehold'])->name('admin.residents.assign-household');
Route::post('/admin/residents/update', [ResidentController::class, 'update'])->name('admin.residents.update');
Route::post('/admin/residents/verify', [ResidentController::class, 'verify'])->name('admin.residents.verify');
Route::post('/admin/document-requests/approve', [ResidentController::class, 'approveDocumentRequest'])->name('admin.documents.approve');
Route::post('/admin/events', [EventController::class, 'store'])->name('admin.events.store');
Route::get('/admin/events/{event_id}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
Route::put('/admin/events/{event_id}', [EventController::class, 'update'])->name('admin.events.update');
Route::delete('/admin/events/{event_id}', [EventController::class, 'destroy'])->name('admin.events.destroy');

// --- Incident Review & Status Update Routes ---
Route::get('/admin/incidents/{incident_id}/review', [AdminController::class, 'reviewIncident'])->name('admin.incidents.review');
Route::patch('/admin/incidents/{incident_id}/status', [AdminController::class, 'updateIncidentStatus'])->name('admin.incidents.update-status');

Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

Route::post('/admin/document-requests/status', [ResidentController::class, 'updateDocumentStatus'])->name('admin.documents.update-status');
Route::get('/admin/documents/{request_id}/print', [\App\Http\Controllers\ResidentController::class, 'printDocument'])
    ->name('admin.documents.print');

Route::middleware(['admin.inactivity'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/incidents/{incident_id}/review', [AdminController::class, 'reviewIncident'])->name('admin.incidents.review');
    Route::patch('/admin/incidents/{incident_id}/status', [AdminController::class, 'updateIncidentStatus'])->name('admin.incidents.update-status');
    Route::post('/admin/documents/update-status', [ResidentController::class, 'updateDocumentStatus'])->name('admin.documents.update-status');
    Route::get('/admin/documents/{request_id}/print', [ResidentController::class, 'printDocument'])->name('admin.documents.print');
});

Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::match(['get', 'post'], '/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');