<?php

namespace App\Http\Controllers;

use App\Models\PipelineTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PipelineTemplateController extends Controller
{
    public function index(): View
    {
        $templates = PipelineTemplate::withCount('stages', 'jobPostings')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('pipeline_templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('pipeline_templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTemplate($request);

        DB::transaction(function () use ($validated, $request) {
            $template = PipelineTemplate::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default' => $request->boolean('is_default'),
            ]);

            $this->syncStages($template, $validated['stages']);
        });

        return redirect()
            ->route('pipeline-templates.index')
            ->with('status', 'Template pipeline berhasil dibuat.');
    }

    public function edit(PipelineTemplate $pipelineTemplate): View
    {
        $pipelineTemplate->load('stages');

        return view('pipeline_templates.edit', ['template' => $pipelineTemplate]);
    }

    public function update(Request $request, PipelineTemplate $pipelineTemplate): RedirectResponse
    {
        $validated = $this->validateTemplate($request);

        DB::transaction(function () use ($validated, $pipelineTemplate, $request) {
            $pipelineTemplate->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default' => $request->boolean('is_default'),
            ]);

            // Cara paling aman: hapus semua stage lama, buat ulang sesuai urutan form.
            // Job posting yang udah kepakai template ini nggak kepengaruh karena
            // stage aktualnya ada di job_posting_stages (hasil copy), bukan di sini.
            $pipelineTemplate->stages()->delete();
            $this->syncStages($pipelineTemplate, $validated['stages']);
        });

        return redirect()
            ->route('pipeline-templates.index')
            ->with('status', 'Template pipeline berhasil diupdate.');
    }

    public function destroy(PipelineTemplate $pipelineTemplate): RedirectResponse
    {
        if ($pipelineTemplate->jobPostings()->exists()) {
            return redirect()
                ->route('pipeline-templates.index')
                ->with('error', 'Template ini masih dipakai job posting, nggak bisa dihapus.');
        }

        $pipelineTemplate->delete();

        return redirect()
            ->route('pipeline-templates.index')
            ->with('status', 'Template pipeline berhasil dihapus.');
    }

    /**
     * @return array{name: string, description: ?string, stages: array<int, array{name: string, is_final: bool}>}
     */
    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'stages' => ['required', 'array', 'min:1'],
            'stages.*.name' => ['required', 'string', 'max:255'],
            'stages.*.is_final' => ['nullable', 'boolean'],
        ]);
    }

    private function syncStages(PipelineTemplate $template, array $stages): void
    {
        foreach (array_values($stages) as $index => $stage) {
            $template->stages()->create([
                'name' => $stage['name'],
                'order' => $index + 1,
                'is_final' => ! empty($stage['is_final']),
            ]);
        }
    }
}