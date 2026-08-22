{{--
    Butuh $jobPosting, $application (relasi interviews.interviewers, interviews.feedback.interviewer sudah di-load)
    dan $hrUsers (koleksi User) dari controller.
    Include di applications/show.blade.php: @include('interviews._panel')
--}}

<div class="card">
    <div class="card-header">
        <h3>Interview</h3>
    </div>

    @forelse ($application->interviews as $interview)
        <div class="interview-row">
            <div class="interview-meta">
                <strong>{{ $interview->scheduled_at->translatedFormat('d M Y, H:i') }}</strong>
                <span class="badge badge-{{ $interview->status === 'completed' ? 'green' : ($interview->status === 'cancelled' ? 'gray' : 'blue') }}">
                    {{ str($interview->status)->title() }}
                </span>
                @if ($interview->location_or_link)
                    <div class="text-muted">{{ $interview->location_or_link }}</div>
                @endif
                <div class="text-muted">
                    Interviewer: {{ $interview->interviewers->pluck('name')->join(', ') ?: '-' }}
                </div>
            </div>

            <details class="feedback-details">
                <summary>Feedback ({{ $interview->feedback->count() }})</summary>

                @foreach ($interview->feedback as $fb)
                    <div class="feedback-item">
                        <strong>{{ $fb->interviewer->name }}</strong>
                        @if ($fb->rating) — {{ $fb->rating }}/5 @endif
                        @if ($fb->comments)
                            <p>{{ $fb->comments }}</p>
                        @endif
                    </div>
                @endforeach

                <form action="{{ route('applications.interviews.feedback', [$jobPosting, $application, $interview]) }}" method="POST" class="inline-form">
                    @csrf
                    <select name="interviewer_id" required>
                        <option value="">Pilih interviewer...</option>
                        @foreach ($interview->interviewers as $iv)
                            <option value="{{ $iv->id }}">{{ $iv->name }}</option>
                        @endforeach
                    </select>
                    <select name="rating">
                        <option value="">Rating</option>
                        @for ($r = 1; $r <= 5; $r++)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endfor
                    </select>
                    <input type="text" name="comments" placeholder="Komentar singkat">
                    <button type="submit" class="btn btn-secondary btn-sm">Simpan Feedback</button>
                </form>
            </details>

            <form action="{{ route('applications.interviews.destroy', [$jobPosting, $application, $interview]) }}" method="POST"
                  onsubmit="return confirm('Hapus jadwal interview ini?');" class="inline-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Hapus Interview</button>
            </form>
        </div>
        <hr>
    @empty
        <p class="text-muted">Belum ada interview dijadwalkan.</p>
    @endforelse

    <details>
        <summary>+ Jadwalkan Interview Baru</summary>
        <form action="{{ route('applications.interviews.store', [$jobPosting, $application]) }}" method="POST" class="stacked-form">
            @csrf
            <div class="form-group">
                <label for="scheduled_at">Tanggal & Jam</label>
                <input type="datetime-local" id="scheduled_at" name="scheduled_at" required>
            </div>
            <div class="form-group">
                <label for="location_or_link">Lokasi / Link</label>
                <input type="text" id="location_or_link" name="location_or_link" placeholder="Kantor / link Zoom / Meet">
            </div>
            <div class="form-group">
                <label for="interviewers">Interviewer</label>
                <select id="interviewers" name="interviewers[]" multiple required>
                    @foreach ($hrUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <p class="hint">Ctrl/Cmd + klik buat pilih lebih dari satu.</p>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Jadwalkan</button>
        </form>
    </details>
</div>