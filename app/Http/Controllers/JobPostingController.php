<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\JobPosting;
use App\Models\PipelineTemplate;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPosting::with('department')->withCount('applications')->latest();

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobPostings = $query->paginate(10)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('job_postings.index', compact('jobPostings', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $templates = PipelineTemplate::orderBy('name')->get();
        return view('job_postings.create', compact('departments', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'pipeline_template_id' => ['required', 'exists:pipeline_templates,id'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,closed,archived'],
            'opening_date' => ['nullable', 'date'],
            'closing_date' => ['nullable', 'date', 'after_or_equal:opening_date'],
        ]);

        $data['created_by'] = auth()->id();

        $jobPosting = JobPosting::create($data);

        $template = PipelineTemplate::findOrFail($data['pipeline_template_id']);
        $jobPosting->copyStagesFromTemplate($template);

        return redirect()->route('job-postings.index')
            ->with('status', 'Lowongan "' . $jobPosting->title . '" berhasil dibuat.');
    }

    public function show(JobPosting $jobPosting)
    {
        $jobPosting->load([
            'department',
            'stages' => fn ($query) => $query->withCount('applications'),
            'applications.applicant',
            'applications.currentStage',
        ]);

        return view('job_postings.show', compact('jobPosting'));
    }

    public function edit(JobPosting $jobPosting)
    {
        $departments = Department::orderBy('name')->get();
        return view('job_postings.edit', compact('jobPosting', 'departments'));
    }

    public function update(Request $request, JobPosting $jobPosting)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,closed,archived'],
            'opening_date' => ['nullable', 'date'],
            'closing_date' => ['nullable', 'date', 'after_or_equal:opening_date'],
        ]);

        $jobPosting->update($data);

        return redirect()->route('job-postings.index')
            ->with('status', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(JobPosting $jobPosting)
    {
        $jobPosting->delete();
        return redirect()->route('job-postings.index')
            ->with('status', 'Lowongan berhasil dihapus.');
    }
}