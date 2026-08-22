@extends('layouts.public')

@section('title', 'Cek Status Lamaran')

@section('content')
    <div class="card" style="max-width:440px; margin:0 auto;">
        <h2 style="margin-bottom:.5rem;">Cek Status Lamaran</h2>
        <p style="color:var(--muted); font-size:.9rem; margin:0 0 1.5rem;">
            Masukkan email yang kamu pakai waktu melamar. Kami akan kirim link buat lihat status lamaranmu.
        </p>

        <form action="{{ route('careers.status.send') }}" method="POST">
            @csrf

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

            <button type="submit" style="width:100%; justify-content:center;">Kirim Link Cek Status</button>
        </form>
    </div>
@endsection