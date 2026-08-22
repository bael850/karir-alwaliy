<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicantDocument;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantDocumentController extends Controller
{
    private const DISK = 'public';
    private const DIR = 'applicant-documents';

    public function store(Request $request, JobPosting $jobPosting, Application $application): RedirectResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);

        $validated = $request->validate([
            'type' => ['required', 'in:cv,portfolio,transcript,certificate,other'],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $file = $request->file('file');
        $path = $file->store(self::DIR, self::DISK);

        $application->documents()->create([
            'type' => $validated['type'],
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return redirect()
            ->route('applications.show', [$jobPosting, $application])
            ->with('status', 'Dokumen berhasil diupload.');
    }

    public function download(JobPosting $jobPosting, Application $application, ApplicantDocument $document): StreamedResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);
        $this->authorizeDocumentBelongsToApplication($application, $document);

        abort_unless(Storage::disk(self::DISK)->exists($document->path), 404);

        return Storage::disk(self::DISK)->download($document->path, $document->original_filename);
    }

    public function destroy(JobPosting $jobPosting, Application $application, ApplicantDocument $document): RedirectResponse
    {
        $this->authorizeApplicationBelongsToJob($jobPosting, $application);
        $this->authorizeDocumentBelongsToApplication($application, $document);

        Storage::disk(self::DISK)->delete($document->path);
        $document->delete();

        return redirect()
            ->route('applications.show', [$jobPosting, $application])
            ->with('status', 'Dokumen berhasil dihapus.');
    }

    private function authorizeApplicationBelongsToJob(JobPosting $jobPosting, Application $application): void
    {
        abort_unless($application->job_posting_id === $jobPosting->id, 404);
    }

    private function authorizeDocumentBelongsToApplication(Application $application, ApplicantDocument $document): void
    {
        abort_unless(
            $document->documentable_type === Application::class && $document->documentable_id === $application->id,
            404
        );
    }
}