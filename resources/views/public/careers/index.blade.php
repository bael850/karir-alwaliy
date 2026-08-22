@extends('layouts.public')

@section('title', 'Lowongan Kerja')

@section('content')
    <div style="margin-bottom:2rem;">
        <h1>Lowongan Kerja</h1>
        <p style="color:var(--muted); margin:.4rem 0 0;">Gabung bersama tim Alwaliy Sejahtera.</p>
    </div>

    <form action="{{ route('careers.index') }}" method="GET" style="margin-bottom:1.75rem;">
        <input type="text" name="q" placeholder="Cari posisi..." value="{{ request('q') }}" style="margin-bottom:0;">
    </form>

    @forelse ($jobPostings as $job)
        <a href="{{ route('careers.show', $job) }}" class="job-card" style="display:block; color:inherit;">
            <h3 style="margin-bottom:.2rem;">{{ $job->title }}</h3>
            <div class="job-meta">{{ $job->department->name }} · {{ $job->location ?? 'Lokasi fleksibel' }}</div>
            <span class="badge-type">{{ ['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship'][$job->employment_type] ?? $job->employment_type }}</span>
        </a>
    @empty
        <div class="card" style="text-align:center; color:var(--muted);">
            Belum ada lowongan yang dibuka saat ini. Coba cek lagi nanti ya.
        </div>
    @endforelse

    <div style="margin-top:1.5rem;">{{ $jobPostings->links() }}</div>
@endsection