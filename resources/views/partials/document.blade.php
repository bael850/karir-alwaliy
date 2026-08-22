{{--
    Butuh $jobPosting, $application (relasi 'documents' sudah di-load).
    Include di applications/show.blade.php: @include('partials.documents')
    Jalankan sekali di server: php artisan storage:link
--}}

<div class="card">
    <div class="card-header">
        <h3>Dokumen</h3>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table table-compact">
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Nama File</th>
                <th>Ukuran</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($application->documents as $doc)
                <tr>
                    <td>{{ str($doc->type)->title() }}</td>
                    <td>{{ $doc->original_filename }}</td>
                    <td>{{ $doc->size_bytes ? number_format($doc->size_bytes / 1024, 0) . ' KB' : '-' }}</td>
                    <td class="actions">
                        <a href="{{ route('applications.documents.download', [$jobPosting, $application, $doc]) }}">Unduh</a>
                        <form action="{{ route('applications.documents.destroy', [$jobPosting, $application, $doc]) }}" method="POST"
                              onsubmit="return confirm('Hapus dokumen ini?');" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">Belum ada dokumen diupload.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <form action="{{ route('applications.documents.store', [$jobPosting, $application]) }}" method="POST" enctype="multipart/form-data" class="inline-form">
        @csrf
        <select name="type" required>
            <option value="cv">CV</option>
            <option value="portfolio">Portfolio</option>
            <option value="transcript">Transkrip</option>
            <option value="certificate">Sertifikat</option>
            <option value="other">Lainnya</option>
        </select>
        <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        <button type="submit" class="btn btn-secondary btn-sm">Upload</button>
    </form>
    <p class="hint">Maks 5MB. Format: PDF, Word, JPG, PNG.</p>
</div>