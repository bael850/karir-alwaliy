@extends('layouts.app')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.75rem;">
        <h2>Template Pipeline</h2>
        <a href="{{ route('pipeline-templates.create') }}" class="btn">+ Template Baru</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jumlah Stage</th>
                    <th>Dipakai di Job Posting</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr>
                        <td>
                            <strong>{{ $template->name }}</strong>
                            @if ($template->description)
                                <div style="color:var(--muted); font-size:.82rem; margin-top:.2rem;">{{ $template->description }}</div>
                            @endif
                        </td>
                        <td>{{ $template->stages_count }}</td>
                        <td>{{ $template->job_postings_count }}</td>
                        <td>
                            @if ($template->is_default)
                                <span class="badge badge-published">Default</span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('pipeline-templates.edit', $template) }}" style="margin-right:1rem;">Edit</a>
                            <form action="{{ route('pipeline-templates.destroy', $template) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus template ini?');" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="color:var(--muted);">Belum ada template pipeline.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection