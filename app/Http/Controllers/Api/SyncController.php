<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncApplicantRequest;
use App\Http\Requests\Api\SyncApplicationRequest;
use App\Http\Resources\ApplicationSyncResource;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SyncController extends Controller
{
    /**
     * PUSH: marketplace kirim/update data pelamar.
     * Idempotent by external_id — dipanggil ulang aman, nggak dobel.
     */
    public function storeApplicant(SyncApplicantRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $applicant = Applicant::updateOrCreate(
            ['external_id' => $data['external_id']],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'portfolio_url' => $data['portfolio_url'] ?? null,
                'last_synced_at' => now(),
            ]
        );

        return response()->json([
            'data' => [
                'external_id' => $applicant->external_id,
                'id' => $applicant->id,
                'created' => $applicant->wasRecentlyCreated,
            ],
        ], $applicant->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * PUSH: marketplace kirim/update lamaran (Applicant harus sudah di-sync duluan).
     * Idempotent by external_id.
     */
    public function storeApplication(SyncApplicationRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $applicant = Applicant::where('external_id', $data['applicant_external_id'])->firstOrFail();

        $jobPosting = isset($data['job_posting_id'])
            ? JobPosting::findOrFail($data['job_posting_id'])
            : JobPosting::where('slug', $data['job_posting_slug'])->firstOrFail();

        // Stage awal = stage pertama (order paling kecil) punya job posting ini.
        $firstStage = $jobPosting->stages()->orderBy('order')->first();

        if (! $firstStage) {
            return response()->json([
                'message' => 'Job posting ini belum punya pipeline stage — nggak bisa terima lamaran baru.',
            ], 422);
        }

        $existing = Application::where('external_id', $data['external_id'])->first();

        $application = Application::updateOrCreate(
            ['external_id' => $data['external_id']],
            [
                'applicant_id' => $applicant->id,
                'job_posting_id' => $jobPosting->id,
                'source' => $data['source'] ?? 'marketplace',
                'applied_at' => $data['applied_at'] ?? now(),
                'last_synced_at' => now(),
            ]
        );

        // Stage cuma di-set kalau ini record baru. Kalau update, current_stage_id
        // dibiarkan apa adanya — jangan ditimpa balik ke stage pertama tiap kali marketplace sync ulang,
        // karena progress stage-nya dijalanin dari sisi ATS, bukan marketplace.
        if (! $existing) {
            $application->update(['current_stage_id' => $firstStage->id]);
        }

        return response()->json([
            'data' => [
                'external_id' => $application->external_id,
                'id' => $application->id,
                'current_stage' => $application->currentStage->name,
                'created' => $application->wasRecentlyCreated,
            ],
        ], $application->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * PULL: marketplace tarik daftar lamaran yang statusnya berubah sejak timestamp tertentu.
     * GET /api/sync/applications?since=2026-08-20T00:00:00Z&per_page=50
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'since' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $query = Application::with(['applicant', 'jobPosting', 'currentStage'])
            ->whereNotNull('external_id')
            ->orderBy('updated_at');

        if ($request->filled('since')) {
            $query->where('updated_at', '>=', $request->date('since'));
        }

        $applications = $query->paginate($request->integer('per_page', 50));

        return ApplicationSyncResource::collection($applications);
    }

    /**
     * PULL: detail satu lamaran by external_id (ID yang dikasih marketplace pas sync awal).
     */
    public function show(string $externalId): ApplicationSyncResource
    {
        $application = Application::with(['applicant', 'jobPosting', 'currentStage'])
            ->where('external_id', $externalId)
            ->firstOrFail();

        return new ApplicationSyncResource($application);
    }
}