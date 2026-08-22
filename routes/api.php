<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API buat integrasi Marketplace <-> ATS
|--------------------------------------------------------------------------
| Semua route di sini otomatis dapet prefix /api dan stateless (nggak
| kena CSRF protection), karena didaftarkan lewat key `api:` di
| bootstrap/app.php -> withRouting().
|
| Auth pakai Sanctum personal access token. Marketplace kirim header:
|   Authorization: Bearer <token>
*/

Route::middleware('auth:sanctum')->prefix('sync')->group(function () {
    // PUSH dari marketplace
    Route::post('/applicants', [SyncController::class, 'storeApplicant']);
    Route::post('/applications', [SyncController::class, 'storeApplication']);

    // PULL oleh marketplace
    Route::get('/applications', [SyncController::class, 'index']);
    Route::get('/applications/{externalId}', [SyncController::class, 'show']);
});