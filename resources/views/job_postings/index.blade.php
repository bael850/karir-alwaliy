@extends('layouts.app')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem;">
        <div>
            <h2>Daftar Lowongan</h2>
            <p style="color:var(--muted); font-size:.9rem; margin:.4rem 0 0;">Kelola seluruh lowongan yang sedang dan pernah dibuka.</p>
        </div>
        <a href="{{ route('job-postings.create') }}" class="btn">+ Buat Lowongan</a>
    </div>

    <form action="{{ route('job-postings.index') }}" method="GET" class="filter-bar">
        <div class="field" style="flex:2;">
            <label for="q">Cari Judul</label>
            <input type="text" id="q" name="q" placeholder="mis. Backend Engineer" value="{{ request('q') }}">
        </div>
        <div class="field">
            <label for="department_id">Departemen</label>
            <select id="department_id" name="department_id">
                <option value="">Semua Departemen</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">Semua Status</option>
                @foreach (['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed', 'archived' => 'Archived'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') == $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit">Terapkan</button>
            @if (request()->hasAny(['q', 'department_id', 'status']))
                <a href="{{ route('job-postings.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    <div class="card" style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th style="padding-left:1.5rem;">Judul</th>
                    <th>Departemen</th>
                    <th>Pelamar</th>
                    <th>Status</th>
                    <th style="padding-right:1.5rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobPostings as $job)
                    @php
                        $badgeClass = [
                            'draft' => 'badge-draft',
                            'published' => 'badge-published',
                            'closed' => 'badge-closed',
                            'archived' => 'badge-archived',
                        ][$job->status] ?? 'badge-draft';
                    @endphp
                    <tr>
                        <td style="padding-left:1.5rem; font-weight:600;">{{ $job->title }}</td>
                        <td style="color:var(--muted);">{{ $job->department->name }}</td>
                        <td style="color:var(--muted);">{{ $job->applications_count }}</td>
                        <td><span class="badge {{ $badgeClass }}">{{ ucfirst($job->status) }}</span></td>
                        <td style="padding-right:1.5rem;">
                            <a href="{{ route('job-postings.show', $job) }}">Lihat</a>
                            &nbsp;·&nbsp;
                            <a href="{{ route('job-postings.edit', $job) }}">Edit</a>
                            &nbsp;·&nbsp;
                            <form action="{{ route('job-postings.destroy', $job) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus lowongan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:3rem 1rem; color:var(--muted);">
                            @if (request()->hasAny(['q', 'department_id', 'status']))
                                Nggak ada lowongan yang cocok dengan filter ini. <a href="{{ route('job-postings.index') }}">Reset filter</a>.
                            @else
                                Belum ada lowongan. Klik <strong>+ Buat Lowongan</strong> untuk mulai.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem;">{{ $jobPostings->links() }}</div>
@endsection