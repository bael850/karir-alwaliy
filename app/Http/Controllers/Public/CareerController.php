<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\AtsApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * Versi karir-alwaliy (app publik). Nggak ada Eloquent/DB di sini —
 * semua data lowongan/lamaran/status diambil lewat AtsApiClient,
 * yang manggil API di repo ats-alwaliy.
 */
class CareerController extends Controller
{
    public function __construct(private AtsApiClient $ats)
    {
    }

    public function index(Request $request): View
    {
        $json = $this->ats->jobPostings([
            'q' => $request->query('q'),
            'page' => $request->query('page'),
        ])->json();

        $jobPostings = new LengthAwarePaginator(
            collect($json['data'] ?? [])->map(fn ($item) => (object) $this->toObject($item)),
            $json['total'] ?? 0,
            $json['per_page'] ?? 9,
            $json['current_page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('public.careers.index', compact('jobPostings'));
    }

    public function show(string $slug): View
    {
        $response = $this->ats->jobPosting($slug);

        abort_if($response->status() === 404, 404);
        $response->throw();

        $jobPosting = (object) $this->toObject($response->json('data'));

        return view('public.careers.show', compact('jobPosting'));
    }

    public function apply(Request $request, string $slug): RedirectResponse
    {
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

        unset($validated['cv'], $validated['attachments']);

        $response = $this->ats->apply($slug, $validated, [
            'cv' => $request->file('cv'),
            'attachments' => $request->file('attachments', []),
        ]);

        if ($response->status() === 422) {
            return back()
                ->withErrors($response->json('errors') ?? ['email' => $response->json('message')])
                ->withInput();
        }

        $response->throw();

        return redirect()
            ->route('careers.applied', $slug)
            ->with('status', $response->json('message') ?? 'Lamaran kamu berhasil dikirim!');
    }

    public function applied(string $slug): View
    {
        $jobPosting = (object) $this->toObject($this->ats->jobPosting($slug)->json('data'));

        return view('public.careers.applied', compact('jobPosting'));
    }

    public function statusForm(): View
    {
        return view('public.careers.status');
    }

    public function sendStatusLink(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);

        $response = $this->ats->sendStatusLink($validated['email']);

        return back()->with('status', $response->json('message'));
    }

    public function showStatus(Request $request, string $applicant): View
    {
        $response = $this->ats->status($applicant, $request->query());

        abort_if($response->status() === 403, 403);
        $response->throw();

        $applicant = (object) $this->toObject($response->json('data'));

        return view('public.careers.status_result', compact('applicant'));
    }

    /**
     * Ubah array hasil decode JSON jadi stdClass secara rekursif,
     * biar view yang lama (pakai akses ->properti) tetap jalan.
     */
    private function toObject(mixed $data): mixed
    {
        if (is_array($data)) {
            $isList = array_is_list($data);

            $mapped = array_map(fn ($v) => $this->toObject($v), $data);

            return $isList ? $mapped : (object) $mapped;
        }

        return $data;
    }
}