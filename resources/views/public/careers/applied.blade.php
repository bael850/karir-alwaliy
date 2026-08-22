@extends('layouts.public')

@section('title', 'Lamaran Terkirim')

@section('content')
    <div class="card" style="text-align:center; padding:3rem 2rem;">
        <h2 style="margin-bottom:.75rem;">Lamaran Kamu Berhasil Dikirim 🎉</h2>
        <p style="color:var(--muted); font-size:.93rem; max-width:440px; margin:0 auto 1.5rem;">
            Terima kasih sudah melamar posisi <strong>{{ $jobPosting->title }}</strong> di Alwaliy Sejahtera.
            Tim rekrutmen kami akan meninjau lamaranmu dan mengabari via email.
        </p>
        <div style="display:flex; gap:.75rem; justify-content:center;">
            <a href="{{ route('careers.status.form') }}" class="btn">Cek Status Lamaran</a>
            <a href="{{ route('careers.index') }}" class="btn btn-ghost">Lihat Lowongan Lain</a>
        </div>
    </div>
@endsection