<?php

use App\Http\Controllers\Public\CareerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('careers.index'));

Route::prefix('karir')->name('careers.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('index');
    Route::get('/status', [CareerController::class, 'statusForm'])->name('status.form');
    Route::post('/status', [CareerController::class, 'sendStatusLink'])
        ->middleware('throttle:5,1')
        ->name('status.send');
    Route::get('/status/{applicant}', [CareerController::class, 'showStatus'])->name('status.show');
    Route::get('/{slug}', [CareerController::class, 'show'])->name('show');
    Route::post('/{slug}/lamar', [CareerController::class, 'apply'])
        ->middleware('throttle:10,1')
        ->name('apply');
    Route::get('/{slug}/terkirim', [CareerController::class, 'applied'])->name('applied');
});