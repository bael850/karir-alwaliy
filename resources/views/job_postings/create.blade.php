@extends('layouts.app')

@section('content')
    <h2>Buat Lowongan Baru</h2>
    <p style="color:var(--muted); font-size:.9rem; margin:0 0 1.75rem;">Isi detail lowongan dan pilih alur rekrutmen yang dipakai.</p>

    <div class="card">
        <form action="{{ route('job-postings.store') }}" method="POST">
            @csrf

            <h3 class="section-title">Informasi Dasar</h3>

            <label for="title">Judul Lowongan<span class="required">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="mis. Backend Engineer" required>

            <div class="form-row">
                <div>
                    <label for="department_id">Departemen<span class="required">*</span></label>
                    <select id="department_id" name="department_id" required>
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="pipeline_template_id">Pipeline Rekrutmen<span class="required">*</span></label>
                    <select id="pipeline_template_id" name="pipeline_template_id" required>
                        <option value="">-- Pilih Pipeline --</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}" @selected(old('pipeline_template_id') == $template->id)>{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-hint">Stage-nya bakal disalin ke lowongan ini dan bisa diubah terpisah nanti.</span>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="location">Lokasi</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="mis. Jakarta / Remote">
                </div>
                <div>
                    <label for="employment_type">Tipe Pekerjaan<span class="required">*</span></label>
                    <select id="employment_type" name="employment_type" required>
                        @foreach (['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('employment_type', 'full_time') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="section-title">Deskripsi Pekerjaan</h3>

            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4" placeholder="Ringkasan tanggung jawab dan lingkup kerja...">{{ old('description') }}</textarea>

            <label for="requirements">Requirements</label>
            <textarea id="requirements" name="requirements" rows="4" placeholder="Kualifikasi, pengalaman, skill yang dibutuhkan...">{{ old('requirements') }}</textarea>

            <h3 class="section-title">Jadwal &amp; Status</h3>

            <div class="form-row">
                <div>
                    <label for="status">Status<span class="required">*</span></label>
                    <select id="status" name="status" required>
                        @foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'draft') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="field-hint">Draft belum tampil ke pelamar; Published langsung terbuka.</span>
                </div>
                <div></div>
            </div>

            <div class="form-row">
                <div>
                    <label for="opening_date">Tanggal Buka</label>
                    <input type="date" id="opening_date" name="opening_date" value="{{ old('opening_date') }}">
                </div>
                <div>
                    <label for="closing_date">Tanggal Tutup</label>
                    <input type="date" id="closing_date" name="closing_date" value="{{ old('closing_date') }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit">Simpan Lowongan</button>
                <a href="{{ route('job-postings.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection