<?php

use App\Http\Controllers\ApplicantDocumentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\JobScreeningQuestionController;
use App\Http\Controllers\PipelineTemplateController;
use App\Http\Controllers\Public\CareerController;

Route::get('/', function () {
    return redirect(auth()->check() ? route('dashboard') : route('login'));
});

Route::prefix('karir')->name('careers.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('index');
    Route::get('/status', [CareerController::class, 'statusForm'])->name('status.form');
    Route::post('/status', [CareerController::class, 'sendStatusLink'])
        ->middleware('throttle:5,1')
        ->name('status.send');
    Route::get('/status/{applicant}', [CareerController::class, 'showStatus'])->name('status.show');
    Route::get('/{jobPosting:slug}', [CareerController::class, 'show'])->name('show');
    Route::post('/{jobPosting:slug}/lamar', [CareerController::class, 'apply'])
        ->middleware('throttle:10,1')
        ->name('apply');
    Route::get('/{jobPosting:slug}/terkirim', [CareerController::class, 'applied'])->name('applied');
});

Route::middleware('throttle:5,1')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('job-postings.index');
    })->name('dashboard');

    Route::resource('job-postings', JobPostingController::class);

    Route::get('/job-postings/{jobPosting}/applications/{application}', [ApplicationController::class, 'show'])
        ->name('applications.show');
    Route::post('/job-postings/{jobPosting}/applications/{application}/move-stage', [ApplicationController::class, 'moveStage'])
        ->name('applications.move-stage');
    Route::post('/job-postings/{jobPosting}/applications/{application}/notes', [ApplicationController::class, 'addNote'])
        ->name('applications.notes.store');

    // --- 1. Pipeline Template CRUD ---
    Route::resource('pipeline-templates', PipelineTemplateController::class)
        ->except(['show']);

    // --- 2. Screening Questions (nested di bawah job posting) ---
    Route::prefix('job-postings/{jobPosting}/screening-questions')
        ->name('job-postings.screening-questions.')
        ->group(function () {
            Route::get('/', [JobScreeningQuestionController::class, 'index'])->name('index');
            Route::get('/create', [JobScreeningQuestionController::class, 'create'])->name('create');
            Route::post('/', [JobScreeningQuestionController::class, 'store'])->name('store');
            Route::get('/{screeningQuestion}/edit', [JobScreeningQuestionController::class, 'edit'])->name('edit');
            Route::put('/{screeningQuestion}', [JobScreeningQuestionController::class, 'update'])->name('update');
            Route::delete('/{screeningQuestion}', [JobScreeningQuestionController::class, 'destroy'])->name('destroy');
        });

    // --- 3. Interview scheduling (nested di bawah job-posting/application, ikut pola applications.show) ---
    Route::prefix('job-postings/{jobPosting}/applications/{application}/interviews')
        ->name('applications.interviews.')
        ->group(function () {
            Route::post('/', [InterviewController::class, 'store'])->name('store');
            Route::put('/{interview}', [InterviewController::class, 'update'])->name('update');
            Route::delete('/{interview}', [InterviewController::class, 'destroy'])->name('destroy');
            Route::post('/{interview}/feedback', [InterviewController::class, 'storeFeedback'])->name('feedback');
        });

    // --- 4. Upload dokumen pelamar (nested di bawah job-posting/application) ---
    Route::prefix('job-postings/{jobPosting}/applications/{application}/documents')
        ->name('applications.documents.')
        ->group(function () {
            Route::post('/', [ApplicantDocumentController::class, 'store'])->name('store');
            Route::get('/{document}/download', [ApplicantDocumentController::class, 'download'])->name('download');
            Route::delete('/{document}', [ApplicantDocumentController::class, 'destroy'])->name('destroy');
        });
});