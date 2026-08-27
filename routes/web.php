<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/photos/{filename}', function (string $filename) {
    $safeFilename = basename($filename);
    $path = resource_path('photos/'.$safeFilename);

    abort_unless(File::exists($path), 404);

    return response()->file($path, [
        'Content-Type' => File::mimeType($path),
    ]);
})->where('filename', '.*')->name('public.photos');

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/incident-reporting', [PublicPageController::class, 'incident'])->name('public.incidents');
Route::get('/document-requests', [PublicPageController::class, 'documents'])->name('public.documents');
Route::post('/document-requests', [PublicPageController::class, 'submitDocumentRequest'])->name('public.documents.submit');
Route::get('/event-registration', [PublicPageController::class, 'events'])->name('public.events');
Route::post('/event-registration', [PublicPageController::class, 'submitEventRegistration'])->name('public.events.submit');

Route::get('/admin/login', [AdminAuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::post('/admin/residents', [ResidentController::class, 'store'])->name('admin.residents.store');
Route::post('/admin/residents/assign-household', [ResidentController::class, 'assignHousehold'])->name('admin.residents.assign-household');
Route::post('/admin/residents/update', [ResidentController::class, 'update'])->name('admin.residents.update');
Route::post('/admin/residents/verify', [ResidentController::class, 'verify'])->name('admin.residents.verify');
Route::post('/admin/document-requests/approve', [ResidentController::class, 'approveDocumentRequest'])->name('admin.documents.approve');
Route::post('/admin/events', [EventController::class, 'store'])->name('admin.events.store');

Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
