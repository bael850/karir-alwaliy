@extends('layouts.app')

@section('content')
    @php
        $badgeClass = [
            'draft' => 'badge-draft', 'published' => 'badge-published',
            'closed' => 'badge-closed', 'archived' => 'badge-archived',
        ][$jobPosting->status] ?? 'badge-draft';

        $finalStageCount = $jobPosting->applications
            ->filter(fn ($app) => $app->currentStage?->is_final)
            ->count();
    @endphp

    <a href="{{ route('job-postings.index') }}" style="font-size:.85rem; color:var(--muted);">← Kembali ke daftar</a>

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin:.9rem 0 1.5rem;">
        <div>
            <h2>{{ $jobPosting->title }}</h2>
            <p style="color:var(--muted); font-size:.9rem; margin:0;">
                {{ $jobPosting->department->name }} · {{ $jobPosting->location ?? 'Lokasi belum diisi' }}
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:.75rem;">
            <span class="badge {{ $badgeClass }}">{{ ucfirst($jobPosting->status) }}</span>
            <a href="{{ route('job-postings.screening-questions.index', $jobPosting) }}" class="btn btn-ghost">Pertanyaan Screening</a>
            <a href="{{ route('job-postings.edit', $jobPosting) }}" class="btn btn-ghost">Edit</a>
        </div>
    </div>

    <div class="stat-row">
        <div class="stat-card">
            <div class="num">{{ $jobPosting->applications->count() }}</div>
            <div class="lbl">Total Pelamar</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $finalStageCount }}</div>
            <div class="lbl">Di Stage Final</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $jobPosting->opening_date?->format('d M Y') ?? '-' }}</div>
            <div class="lbl">Dibuka Sejak</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.25rem;">Alur Rekrutmen</h3>

        <div class="stepper">
            @foreach ($jobPosting->stages as $stage)
                <div class="step">
                    <div class="step-dot {{ $stage->is_final ? 'is-final' : '' }}">
                        @if ($stage->is_final) ✓ @else {{ $loop->iteration }} @endif
                    </div>
                    <div class="step-label">{{ $stage->name }}</div>
                    <div class="step-count">{{ $stage->applications_count }} pelamar</div>
                    @if (!$loop->last)<div class="step-line"></div>@endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom:1.25rem;">Pelamar ({{ $jobPosting->applications->count() }})</h3>
        <table>
            <thead>
                <tr><th>Nama</th><th>Email</th><th>Stage Saat Ini</th></tr>
            </thead>
            <tbody>
                @forelse ($jobPosting->applications as $app)
                    <tr>
                        <td style="font-weight:600;">
                            <a href="{{ route('applications.show', [$jobPosting, $app]) }}">{{ $app->applicant->name }}</a>
                        </td>
                        <td style="color:var(--muted);">{{ $app->applicant->email }}</td>
                        <td>
                            <form action="{{ route('applications.move-stage', [$jobPosting, $app]) }}" method="POST">
                                @csrf
                                <select name="stage_id" onchange="this.form.submit()" style="margin:0; width:auto; padding:.35rem .6rem; font-size:.85rem;">
                                    @foreach ($jobPosting->stages as $stage)
                                        <option value="{{ $stage->id }}" @selected($app->current_stage_id == $stage->id)>{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; padding:2.5rem 1rem; color:var(--muted);">
                            Belum ada pelamar untuk lowongan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .stepper { display: flex; align-items: flex-start; overflow-x: auto; padding-bottom: .25rem; }
        .step { display: flex; flex-direction: column; align-items: center; position: relative; min-width: 96px; }
        .step-dot {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--green-100); color: var(--green-700);
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; font-weight: 700; border: 2px solid var(--green-500);
            transition: transform .15s ease;
        }
        .step-dot.is-final { background: var(--green-700); color: white; border-color: var(--green-700); }
        .step:hover .step-dot { transform: scale(1.08); }
        .step-label { font-size: .78rem; color: var(--muted); margin-top: .5rem; text-align: center; padding: 0 .4rem; }
        .step-count { font-size: .7rem; color: var(--green-700); font-weight: 600; margin-top: .15rem; }
        .step-line {
            position: absolute; top: 17px; left: calc(50% + 24px); width: calc(100% - 24px);
            height: 2px; background: var(--border);
        }
    </style>
@endsection