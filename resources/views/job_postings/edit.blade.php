@extends('layouts.app')

@section('content')
    <h2>Edit Lowongan</h2>
    <p style="color:var(--muted); font-size:.9rem; margin:0 0 1.75rem;">{{ $jobPosting->title }}</p>

    <div class="card">
        <form action="{{ route('job-postings.update', $jobPosting) }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="section-title">Informasi Dasar</h3>

            <label for="title">Judul Lowongan<span class="required">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $jobPosting->title) }}" required>

            <div class="form-row">
                <div>
                    <label for="department_id">Departemen<span class="required">*</span></label>
                    <select id="department_id" name="department_id" required>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" @selected(old('department_id', $jobPosting->department_id) == $dept->id)>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="employment_type">Tipe Pekerjaan<span class="required">*</span></label>
                    <select id="employment_type" name="employment_type" required>
                        @foreach (['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('employment_type', $jobPosting->employment_type) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label for="location">Lokasi</label>
            <input type="text" id="location" name="location" value="{{ old('location', $jobPosting->location) }}">

            <h3 class="section-title">Deskripsi Pekerjaan</h3>

            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4">{{ old('description', $jobPosting->description) }}</textarea>

            <label for="requirements">Requirements</label>
            <textarea id="requirements" name="requirements" rows="4">{{ old('requirements', $jobPosting->requirements) }}</textarea>

            <h3 class="section-title">Jadwal &amp; Status</h3>

            <div class="form-row">
                <div>
                    <label for="status">Status<span class="required">*</span></label>
                    <select id="status" name="status" required>
                        @foreach (['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed', 'archived' => 'Archived'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $jobPosting->status) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div></div>
            </div>

            <div class="form-row">
                <div>
                    <label for="opening_date">Tanggal Buka</label>
                    <input type="date" id="opening_date" name="opening_date" value="{{ old('opening_date', $jobPosting->opening_date?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label for="closing_date">Tanggal Tutup</label>
                    <input type="date" id="closing_date" name="closing_date" value="{{ old('closing_date', $jobPosting->closing_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit">Update Lowongan</button>
                <a href="{{ route('job-postings.show', $jobPosting) }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection