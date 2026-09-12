<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}/register', [EventController::class, 'register'])->name('events.register');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event:slug}/registrations', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('registrations.store');
Route::get('/registrations/{registration:registration_code}/success', [RegistrationController::class, 'success'])
    ->name('registrations.success');

Route::middleware('auth')->prefix('check-in')->name('check-in.')->group(function (): void {
    Route::get('/', [CheckInController::class, 'index'])->name('index');
    Route::post('/', [CheckInController::class, 'store'])->name('store');
    Route::get('/lookup', [CheckInController::class, 'lookup'])->name('lookup');
});

Route::middleware('auth')->prefix('admin/exports')->name('exports.')->group(function (): void {
    Route::get('/events/{event}/registrations', [ExportController::class, 'registrations'])->name('registrations');
    Route::get('/events/{event}/attendances', [ExportController::class, 'attendances'])->name('attendances');
    Route::get('/registrations/{registration:registration_code}/qr-code', [ExportController::class, 'registrationQrCode'])->name('registration-qr-code');
});
