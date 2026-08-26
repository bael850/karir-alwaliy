<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

/**
 * Client buat semua panggilan ke API careers di ats-alwaliy.
 * App ini (karir-alwaliy) nggak lagi akses DB langsung — semua data
 * lowongan/lamaran/status lewat sini.
 */
class AtsApiClient
{
    protected function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.ats_api.base_url'), '/'))
            ->withHeaders(['X-Career-Api-Key' => config('services.ats_api.key')])
            ->acceptJson()
            ->timeout(10)
            ->connectTimeout(5);
    }

    /**
     * NOTE: sengaja TIDAK pakai ->throw() di sini. Controller (show/index)
     * perlu bisa cek status 404 secara manual sebelum mutusin abort/throw.
     * Kalau ->throw() dipasang di sini, baris pengecekan 404 di controller
     * jadi dead code karena exception-nya udah kelempar duluan.
     */
    public function jobPostings(array $query = []): Response
    {
        return $this->client()->get('/api/careers/job-postings', $query);
    }

    public function jobPosting(string $slug): Response
    {
        return $this->client()->get("/api/careers/job-postings/{$slug}");
    }

    /**
     * @param  array<string, UploadedFile|UploadedFile[]|null>  $files  contoh: ['cv' => $file, 'attachments' => [$file1, $file2]]
     */
    public function apply(string $slug, array $data, array $files = []): Response
    {
        $request = $this->client();

        if (! empty($files['cv'])) {
            $request = $request->attach('cv', fopen($files['cv']->getRealPath(), 'r'), $files['cv']->getClientOriginalName());
        }

        foreach ($files['attachments'] ?? [] as $attachment) {
            $request = $request->attach('attachments[]', fopen($attachment->getRealPath(), 'r'), $attachment->getClientOriginalName());
        }

        return $request->post("/api/careers/job-postings/{$slug}/apply", $data);
    }

    public function sendStatusLink(string $email): Response
    {
        return $this->client()->post('/api/careers/status', ['email' => $email])->throw();
    }

    public function status(string $applicantId, array $signedQuery): Response
    {
        return $this->client()->get("/api/careers/status/{$applicantId}", $signedQuery);
    }
}