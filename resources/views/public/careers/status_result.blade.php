@extends('layouts.public')

@section('title', 'Status Lamaran')

@section('content')
    <h2 style="margin-bottom:.3rem;">Halo, {{ $applicant->name }}</h2>
    <p style="color:var(--muted); font-size:.9rem; margin:0 0 1.75rem;">Berikut status semua lamaranmu di Alwaliy Sejahtera.</p>

    @forelse ($applicant->applications as $app)
        <div class="card" style="margin-bottom:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h3 style="margin-bottom:.2rem;">{{ $app->jobPosting->title }}</h3>
                    <div style="color:var(--muted); font-size:.85rem;">Dilamar {{ $app->applied_at?->format('d M Y') }}</div>
                </div>
                <span class="badge-type">{{ $app->currentStage->name ?? 'Diproses' }}</span>
            </div>

            @php $nextInterview = $app->interviews->where('status', 'scheduled')->sortBy('scheduled_at')->first(); @endphp
            @if ($nextInterview)
                <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border); font-size:.88rem;">
                    <strong>Jadwal Interview:</strong> {{ $nextInterview->scheduled_at?->format('d M Y H:i') }}
                    @if ($nextInterview->location_or_link)
                        · {{ $nextInterview->location_or_link }}
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="card" style="text-align:center; color:var(--muted);">Belum ada lamaran tercatat untuk email ini.</div>
    @endforelse

    <div style="margin-top:1.5rem;">
        <a href="{{ route('careers.index') }}" class="btn btn-ghost">Lihat Lowongan Lain</a>
    </div>
@endsection