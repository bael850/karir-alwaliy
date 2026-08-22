<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Models\InterviewFeedback;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    /**
     * Jadwalin interview baru buat sebuah lamaran.
     */
    public function store(Request $request, JobPosting $jobPosting, Application $application): RedirectResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);

        $validated = $request->validate([
            'stage_id' => ['nullable', 'exists:job_posting_stages,id'],
            'scheduled_at' => ['required', 'date'],
            'location_or_link' => ['nullable', 'string', 'max:255'],
            'interviewers' => ['required', 'array', 'min:1'],
            'interviewers.*' => ['exists:users,id'],
        ]);

        $interview = $application->interviews()->create([
            'stage_id' => $validated['stage_id'] ?? $application->current_stage_id,
            'scheduled_at' => $validated['scheduled_at'],
            'location_or_link' => $validated['location_or_link'] ?? null,
            'status' => 'scheduled',
        ]);

        $interview->interviewers()->sync($validated['interviewers']);

        return redirect()
            ->route('applications.show', [$jobPosting, $application])
            ->with('status', 'Interview berhasil dijadwalkan.');
    }

    /**
     * Update status interview (misal ditandain selesai/dibatalkan) atau reschedule.
     */
    public function update(Request $request, JobPosting $jobPosting, Application $application, Interview $interview): RedirectResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);
        $this->authorizeInterviewBelongsToApplication($application, $interview);

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'location_or_link' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
            'interviewers' => ['required', 'array', 'min:1'],
            'interviewers.*' => ['exists:users,id'],
        ]);

        $interview->update([
            'scheduled_at' => $validated['scheduled_at'],
            'location_or_link' => $validated['location_or_link'] ?? null,
            'status' => $validated['status'],
        ]);

        $interview->interviewers()->sync($validated['interviewers']);

        return redirect()
            ->route('applications.show', [$jobPosting, $application])
            ->with('status', 'Jadwal interview berhasil diupdate.');
    }

    public function destroy(JobPosting $jobPosting, Application $application, Interview $interview): RedirectResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);
        $this->authorizeInterviewBelongsToApplication($application, $interview);

        $interview->delete();

        return redirect()
            ->route('applications.show', [$jobPosting, $application])
            ->with('status', 'Interview berhasil dihapus.');
    }

    /**
     * Interviewer ngisi feedback buat interview yang dia ikut.
     */
    public function storeFeedback(Request $request, JobPosting $jobPosting, Application $application, Interview $interview): RedirectResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);
        $this->authorizeInterviewBelongsToApplication($application, $interview);

        $validated = $request->validate([
            'interviewer_id' => ['required', 'exists:users,id'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comments' => ['nullable', 'string'],
        ]);

        InterviewFeedback::updateOrCreate(
            [
                'interview_id' => $interview->id,
                'interviewer_id' => $validated['interviewer_id'],
            ],
            [
                'rating' => $validated['rating'] ?? null,
                'comments' => $validated['comments'] ?? null,
            ]
        );

        return redirect()
            ->route('applications.show', [$jobPosting, $application])
            ->with('status', 'Feedback interview berhasil disimpan.');
    }

    private function authorizeApplicationBelongsToJob(JobPosting $jobPosting, Application $application): void
    {
        abort_unless($application->job_posting_id === $jobPosting->id, 404);
    }

    private function authorizeInterviewBelongsToApplication(Application $application, Interview $interview): void
    {
        abort_unless($interview->application_id === $application->id, 404);
    }
}