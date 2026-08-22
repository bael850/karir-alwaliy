<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\JobScreeningQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobScreeningQuestionController extends Controller
{
    public function index(JobPosting $jobPosting): View
    {
        $questions = $jobPosting->screeningQuestions()->withCount('answers')->get();

        return view('screening_questions.index', [
            'jobPosting' => $jobPosting,
            'questions' => $questions,
        ]);
    }

    public function create(JobPosting $jobPosting): View
    {
        return view('screening_questions.create', compact('jobPosting'));
    }

    public function store(Request $request, JobPosting $jobPosting): RedirectResponse
    {
        $validated = $this->validateQuestion($request);

        $jobPosting->screeningQuestions()->create([
            'question' => $validated['question'],
            'answer_type' => $validated['answer_type'],
            'options' => $validated['answer_type'] === 'select' ? $validated['options_parsed'] : null,
            'is_required' => $request->boolean('is_required'),
            'order' => ($jobPosting->screeningQuestions()->max('order') ?? 0) + 1,
        ]);

        return redirect()
            ->route('job-postings.screening-questions.index', $jobPosting)
            ->with('status', 'Pertanyaan screening berhasil ditambahkan.');
    }

    public function edit(JobPosting $jobPosting, JobScreeningQuestion $screeningQuestion): View
    {
        $this->authorizeQuestionBelongsToJob($jobPosting, $screeningQuestion);

        return view('screening_questions.edit', [
            'jobPosting' => $jobPosting,
            'question' => $screeningQuestion,
        ]);
    }

    public function update(Request $request, JobPosting $jobPosting, JobScreeningQuestion $screeningQuestion): RedirectResponse
    {
        $this->authorizeQuestionBelongsToJob($jobPosting, $screeningQuestion);

        $validated = $this->validateQuestion($request);

        $screeningQuestion->update([
            'question' => $validated['question'],
            'answer_type' => $validated['answer_type'],
            'options' => $validated['answer_type'] === 'select' ? $validated['options_parsed'] : null,
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()
            ->route('job-postings.screening-questions.index', $jobPosting)
            ->with('status', 'Pertanyaan screening berhasil diupdate.');
    }

    public function destroy(JobPosting $jobPosting, JobScreeningQuestion $screeningQuestion): RedirectResponse
    {
        $this->authorizeQuestionBelongsToJob($jobPosting, $screeningQuestion);

        if ($screeningQuestion->answers()->exists()) {
            return redirect()
                ->route('job-postings.screening-questions.index', $jobPosting)
                ->with('error', 'Pertanyaan ini udah punya jawaban dari pelamar, nggak bisa dihapus.');
        }

        $screeningQuestion->delete();

        return redirect()
            ->route('job-postings.screening-questions.index', $jobPosting)
            ->with('status', 'Pertanyaan screening berhasil dihapus.');
    }

    private function validateQuestion(Request $request): array
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'answer_type' => ['required', 'in:text,textarea,number,yes_no,select'],
            'options' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        // Opsi buat tipe "select" dikirim dari textarea, satu baris = satu opsi.
        $validated['options_parsed'] = null;

        if ($validated['answer_type'] === 'select') {
            $lines = collect(explode("\n", (string) $request->input('options')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();

            $validated['options_parsed'] = $lines;
        }

        return $validated;
    }

    private function authorizeQuestionBelongsToJob(JobPosting $jobPosting, JobScreeningQuestion $screeningQuestion): void
    {
        abort_unless($screeningQuestion->job_posting_id === $jobPosting->id, 404);
    }
}