@extends('layouts.public')

@section('title', $jobPosting->title)

@section('content')
    <a href="{{ route('careers.index') }}" style="font-size:.85rem; color:var(--muted);">← Semua lowongan</a>

    <div style="margin:.9rem 0 1.75rem;">
        <h1>{{ $jobPosting->title }}</h1>
        <p style="color:var(--muted); margin:.3rem 0 0;">
            {{ $jobPosting->department->name }} · {{ $jobPosting->location ?? 'Lokasi fleksibel' }}
        </p>
        <span class="badge-type">{{ ['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship'][$jobPosting->employment_type] ?? $jobPosting->employment_type }}</span>
    </div>

    @if ($jobPosting->description)
        <div class="card" style="margin-bottom:1.25rem;">
            <h3 style="margin-bottom:.75rem;">Deskripsi Pekerjaan</h3>
            <div style="white-space:pre-line; color:var(--ink); font-size:.93rem; line-height:1.6;">{{ $jobPosting->description }}</div>
        </div>
    @endif

    @if ($jobPosting->requirements)
        <div class="card" style="margin-bottom:1.75rem;">
            <h3 style="margin-bottom:.75rem;">Requirements</h3>
            <div style="white-space:pre-line; color:var(--ink); font-size:.93rem; line-height:1.6;">{{ $jobPosting->requirements }}</div>
        </div>
    @endif

    <div class="card">
        <h2 style="margin-bottom:1.5rem;">Lamar Posisi Ini</h2>

        <form action="{{ route('careers.apply', $jobPosting->slug) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h3 class="section-title">Data Diri</h3>

            <label for="name">Nama Lengkap<span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>

            <label for="email">Email<span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            <span class="field-hint">Pakai email aktif, semua update lamaran dikirim ke sini.</span>

            <label for="phone">No. HP / WhatsApp</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}">

            <label for="linkedin_url">LinkedIn</label>
            <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/...">

            <label for="portfolio_url">Portfolio (opsional)</label>
            <input type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="https://...">

            <h3 class="section-title">Dokumen</h3>

            <label for="cv">CV<span class="required">*</span></label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
            <span class="field-hint">Format PDF/DOC/DOCX, maksimal 5MB.</span>

            <label for="attachments">Lampiran Lain (opsional)</label>
            <input type="file" id="attachments" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
            <span class="field-hint">Portfolio, sertifikat, transkrip, dll. Bisa pilih lebih dari satu file.</span>

            @if ($jobPosting->screeningQuestions->isNotEmpty())
                <h3 class="section-title">Pertanyaan Tambahan</h3>

                @foreach ($jobPosting->screeningQuestions as $question)
                    <label for="q_{{ $question->id }}">
                        {{ $question->question }}
                        @if ($question->is_required)<span class="required">*</span>@endif
                    </label>

                    @if ($question->answer_type === 'textarea')
                        <textarea id="q_{{ $question->id }}" name="answers[{{ $question->id }}]" rows="3" @required($question->is_required)>{{ old("answers.{$question->id}") }}</textarea>
                    @elseif ($question->answer_type === 'number')
                        <input type="number" id="q_{{ $question->id }}" name="answers[{{ $question->id }}]" value="{{ old("answers.{$question->id}") }}" @required($question->is_required)>
                    @elseif ($question->answer_type === 'yes_no')
                        <select id="q_{{ $question->id }}" name="answers[{{ $question->id }}]" @required($question->is_required)>
                            <option value="">-- Pilih --</option>
                            <option value="Ya" @selected(old("answers.{$question->id}") == 'Ya')>Ya</option>
                            <option value="Tidak" @selected(old("answers.{$question->id}") == 'Tidak')>Tidak</option>
                        </select>
                    @elseif ($question->answer_type === 'select')
                        <select id="q_{{ $question->id }}" name="answers[{{ $question->id }}]" @required($question->is_required)>
                            <option value="">-- Pilih --</option>
                            @foreach ($question->options ?? [] as $option)
                                <option value="{{ $option }}" @selected(old("answers.{$question->id}") == $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" id="q_{{ $question->id }}" name="answers[{{ $question->id }}]" value="{{ old("answers.{$question->id}") }}" @required($question->is_required)>
                    @endif
                @endforeach
            @endif

            <h3 class="section-title">Akun (Opsional)</h3>

            <label style="display:flex; align-items:center; gap:.5rem; font-weight:500; margin-bottom:1rem;">
                <input type="checkbox" id="create_account" name="create_account" value="1" style="width:auto; margin:0;" onchange="document.getElementById('password-fields').style.display = this.checked ? 'block' : 'none'">
                Buat akun biar gampang pantau status &amp; lamar posisi lain nanti
            </label>

            <div id="password-fields" style="display:none;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8">

                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" minlength="8">
                <span class="field-hint">Kalau nggak dicentang, kamu tetap bisa cek status lamaran kapan aja lewat email (tanpa perlu password).</span>
            </div>

            <button type="submit" style="margin-top:.5rem;">Kirim Lamaran</button>
        </form>
    </div>
@endsection