@extends('layouts.app')

@section('content')
    <a href="{{ route('job-postings.show', $jobPosting) }}" style="font-size:.85rem; color:var(--muted);">← Kembali ke {{ $jobPosting->title }}</a>

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin:.9rem 0 1.75rem;">
        <h2>Pertanyaan Screening</h2>
        <a href="{{ route('job-postings.screening-questions.create', $jobPosting) }}" class="btn">+ Tambah Pertanyaan</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pertanyaan</th>
                    <th>Tipe Jawaban</th>
                    <th>Wajib</th>
                    <th>Jawaban Masuk</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $q)
                    <tr>
                        <td>{{ $q->order }}</td>
                        <td>{{ $q->question }}</td>
                        <td>{{ str($q->answer_type)->replace('_', ' ')->title() }}</td>
                        <td>{{ $q->is_required ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $q->answers_count }}</td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('job-postings.screening-questions.edit', [$jobPosting, $q]) }}" style="margin-right:1rem;">Edit</a>
                            <form action="{{ route('job-postings.screening-questions.destroy', [$jobPosting, $q]) }}"
                                  method="POST" onsubmit="return confirm('Yakin hapus pertanyaan ini?');" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="color:var(--muted);">Belum ada pertanyaan screening buat lowongan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection