<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function show(JobPosting $jobPosting, Application $application)
    {
        // Pastikan lamaran ini memang milik job posting di URL,
        // biar orang nggak bisa akses /job-postings/1/applications/5
        // padahal application #5 sebenarnya punya job posting lain.
        abort_unless($application->job_posting_id === $jobPosting->id, 404);

        $application->load([
            'applicant',
            'currentStage',
            'stageHistory.fromStage',
            'stageHistory.toStage',
            'stageHistory.movedBy',
            'notes.user',
            'interviews.stage',
            'interviews.interviewers',
            'interviews.feedback.interviewer',
            'answers.question',
            'documents',
        ]);
        $jobPosting->load('stages');

        $hrUsers = User::orderBy('name')->get();

        return view('applications.show', compact('jobPosting', 'application', 'hrUsers'));
    }

    public function moveStage(Request $request, JobPosting $jobPosting, Application $application)
    {
        abort_unless($application->job_posting_id === $jobPosting->id, 404);

        $data = $request->validate([
            'stage_id' => ['required', 'integer'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        // findOrFail lewat relasi stages() milik job posting ini —
        // otomatis nolak kalau stage_id kirimannya punya job posting lain.
        $newStage = $jobPosting->stages()->findOrFail($data['stage_id']);

        $application->moveToStage($newStage, $request->user(), $data['note'] ?? null);

        return back()->with(
            'status',
            'Pelamar ' . $application->applicant->name . ' dipindah ke stage "' . $newStage->name . '".'
        );
    }

    public function addNote(Request $request, JobPosting $jobPosting, Application $application)
    {
        abort_unless($application->job_posting_id === $jobPosting->id, 404);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $application->notes()->create([
            'user_id' => $request->user()->id,
            'note' => $data['note'],
        ]);

        return back()->with('status', 'Catatan berhasil ditambahkan.');
    }
}