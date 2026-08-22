<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\JobPosting;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CareerController extends Controller
{
    private const DISK = 'public';
    private const DIR = 'applicant-documents';

    public function index(Request $request): View
    {
        $query = JobPosting::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('closing_date')->orWhere('closing_date', '>=', now()->toDateString());
            })
            ->with('department')
            ->latest('opening_date');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $jobPostings = $query->paginate(9)->withQueryString();

        return view('public.careers.index', compact('jobPostings'));
    }

    public function show(JobPosting $jobPosting): View
    {
        abort_unless($jobPosting->status === 'published', 404);

        $jobPosting->load(['department', 'screeningQuestions']);

        return view('public.careers.show', compact('jobPosting'));
    }

    public function apply(Request $request, JobPosting $jobPosting): RedirectResponse
    {
        abort_unless($jobPosting->status === 'published', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'cv' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
            'attachments.*' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'create_account' => ['nullable', 'boolean'],
            'password' => ['nullable', 'required_if:create_account,1', 'string', 'min:8', 'confirmed'],
            'answers' => ['nullable', 'array'],
        ]);

        // Cari applicant existing by email (biar orang yang udah pernah lamar posisi lain
        // nggak perlu isi ulang data dari nol), atau bikin baru kalau belum pernah.
        $applicant = Applicant::withTrashed()->where('email', $validated['email'])->first()
            ?? new Applicant(['email' => $validated['email']]);

        if ($applicant->trashed()) {
            $applicant->restore();
        }

        $applicant->name = $validated['name'];
        $applicant->phone = $validated['phone'] ?? $applicant->phone;
        $applicant->linkedin_url = $validated['linkedin_url'] ?? $applicant->linkedin_url;
        $applicant->portfolio_url = $validated['portfolio_url'] ?? $applicant->portfolio_url;

        if ($request->boolean('create_account') && empty($applicant->password)) {
            $applicant->password = Hash::make($validated['password']);
        }

        $applicant->save();

        if ($applicant->applications()->where('job_posting_id', $jobPosting->id)->exists()) {
            return back()
                ->withErrors(['email' => 'Email ini sudah pernah dipakai buat melamar posisi ini sebelumnya.'])
                ->withInput();
        }

        $firstStage = $jobPosting->stages()->orderBy('order')->first();

        $application = $applicant->applications()->create([
            'job_posting_id' => $jobPosting->id,
            'current_stage_id' => $firstStage?->id,
            'source' => 'website',
            'applied_at' => now(),
        ]);

        foreach ($jobPosting->screeningQuestions as $question) {
            $answer = $request->input("answers.{$question->id}");

            if ($answer !== null && $answer !== '') {
                $application->answers()->create([
                    'job_screening_question_id' => $question->id,
                    'answer' => is_array($answer) ? implode(', ', $answer) : $answer,
                ]);
            }
        }

        $cv = $request->file('cv');
        $application->documents()->create([
            'type' => 'cv',
            'original_filename' => $cv->getClientOriginalName(),
            'path' => $cv->store(self::DIR, self::DISK),
            'mime_type' => $cv->getClientMimeType(),
            'size_bytes' => $cv->getSize(),
        ]);

        foreach ($request->file('attachments', []) as $file) {
            $application->documents()->create([
                'type' => 'other',
                'original_filename' => $file->getClientOriginalName(),
                'path' => $file->store(self::DIR, self::DISK),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        app(NotificationService::class)->send($application, 'application_received', [
            'applicant_name' => $applicant->name,
            'job_title' => $jobPosting->title,
        ]);

        return redirect()
            ->route('careers.applied', $jobPosting)
            ->with('status', 'Lamaran kamu berhasil dikirim!');
    }

    public function applied(JobPosting $jobPosting): View
    {
        return view('public.careers.applied', compact('jobPosting'));
    }

    public function statusForm(): View
    {
        return view('public.careers.status');
    }

    /**
     * Kirim magic link ke email (kalau terdaftar) buat liat status lamaran,
     * tanpa perlu login/password.
     */
    public function sendStatusLink(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);

        $applicant = Applicant::where('email', $validated['email'])->first();

        if ($applicant) {
            $url = URL::temporarySignedRoute(
                'careers.status.show',
                now()->addMinutes(30),
                ['applicant' => $applicant->id]
            );

            Mail::html(
                '<p>Halo ' . e($applicant->name) . ',</p>'
                . '<p>Klik link berikut buat liat status lamaran kamu (berlaku 30 menit):</p>'
                . '<p><a href="' . $url . '">' . $url . '</a></p>',
                fn ($message) => $message->to($applicant->email)->subject('Link Cek Status Lamaran - Alwaliy Sejahtera')
            );
        }

        // Pesan sama persis baik email terdaftar atau nggak,
        // biar orang luar nggak bisa nebak email siapa aja yang ada di database.
        return back()->with('status', 'Kalau email tersebut terdaftar, link cek status sudah kami kirim. Cek inbox/folder spam kamu.');
    }

    public function showStatus(Request $request, Applicant $applicant): View
    {
        abort_unless($request->hasValidSignature(), 403);

        $applicant->load(['applications.jobPosting', 'applications.currentStage', 'applications.interviews']);

        return view('public.careers.status_result', compact('applicant'));
    }
}