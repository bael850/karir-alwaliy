@extends('layouts.app')

@section('content')
    <a href="{{ route('job-postings.show', $jobPosting) }}" style="font-size:.85rem; color:var(--muted);">← Kembali ke {{ $jobPosting->title }}</a>

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin:.9rem 0 1.75rem;">
        <div>
            <h2>{{ $application->applicant->name }}</h2>
            <p style="color:var(--muted); font-size:.9rem; margin:.3rem 0 0;">
                {{ $application->applicant->email }}
                @if ($application->applicant->phone) · {{ $application->applicant->phone }} @endif
            </p>
            <p style="font-size:.85rem; margin:.4rem 0 0;">
                @if ($application->applicant->linkedin_url)
                    <a href="{{ $application->applicant->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a>
                @endif
                @if ($application->applicant->linkedin_url && $application->applicant->portfolio_url) &nbsp;·&nbsp; @endif
                @if ($application->applicant->portfolio_url)
                    <a href="{{ $application->applicant->portfolio_url }}" target="_blank" rel="noopener">Portfolio</a>
                @endif
            </p>
        </div>
        <span class="badge badge-published">{{ $application->currentStage->name ?? '-' }}</span>
    </div>

    @if ($application->answers->isNotEmpty())
        <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:1.25rem;">Jawaban Screening</h3>
            @foreach ($application->answers as $answer)
                <div style="padding:.75rem 0; border-bottom:1px solid var(--border);">
                    <div style="font-size:.85rem; color:var(--muted);">{{ $answer->question->question }}</div>
                    <div style="font-size:.9rem; margin-top:.2rem;">{{ $answer->answer ?: '-' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.25rem;">Pindah Stage</h3>
        <form action="{{ route('applications.move-stage', [$jobPosting, $application]) }}" method="POST">
            @csrf
            <div class="form-row">
                <div>
                    <label for="stage_id">Stage Tujuan</label>
                    <select id="stage_id" name="stage_id" required>
                        @foreach ($jobPosting->stages as $stage)
                            <option value="{{ $stage->id }}" @selected($application->current_stage_id == $stage->id)>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="note">Catatan (opsional)</label>
                    <input type="text" id="note" name="note" placeholder="mis. lolos screening awal">
                </div>
            </div>
            <button type="submit">Pindahkan</button>
        </form>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.25rem;">Riwayat Stage</h3>
        @forelse ($application->stageHistory as $history)
            <div style="padding:.75rem 0; border-bottom:1px solid var(--border);">
                <div style="font-size:.9rem;">
                    <strong>{{ $history->fromStage->name ?? 'Awal' }}</strong>
                    →
                    <strong>{{ $history->toStage->name ?? '-' }}</strong>
                </div>
                <div style="color:var(--muted); font-size:.82rem; margin-top:.25rem;">
                    {{ $history->movedBy->name ?? 'Sistem' }} · {{ $history->moved_at?->format('d M Y H:i') }}
                    @if ($history->note) — {{ $history->note }} @endif
                </div>
            </div>
        @empty
            <p style="color:var(--muted); font-size:.9rem;">Belum ada riwayat perpindahan stage.</p>
        @endforelse
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.25rem;">Catatan</h3>

        @forelse ($application->notes as $note)
            <div style="padding:.75rem 0; border-bottom:1px solid var(--border);">
                <div style="font-size:.9rem;">{{ $note->note }}</div>
                <div style="color:var(--muted); font-size:.82rem; margin-top:.25rem;">
                    {{ $note->user->name ?? 'Sistem' }} · {{ $note->created_at?->format('d M Y H:i') }}
                </div>
            </div>
        @empty
            <p style="color:var(--muted); font-size:.9rem;">Belum ada catatan.</p>
        @endforelse

        <form action="{{ route('applications.notes.store', [$jobPosting, $application]) }}" method="POST" style="margin-top:1.25rem;">
            @csrf
            <label for="note-new">Tambah Catatan</label>
            <textarea id="note-new" name="note" rows="3" required></textarea>
            <button type="submit">Simpan Catatan</button>
        </form>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.25rem;">Interview</h3>

        @forelse ($application->interviews as $interview)
            <div style="padding:.75rem 0; border-bottom:1px solid var(--border);">
                <div style="font-size:.9rem;">
                    <strong>{{ $interview->stage->name ?? '-' }}</strong>
                    — <span class="badge badge-{{ $interview->status === 'completed' ? 'published' : ($interview->status === 'cancelled' ? 'archived' : 'draft') }}">{{ ucfirst($interview->status) }}</span>
                </div>
                <div style="color:var(--muted); font-size:.82rem; margin-top:.25rem;">
                    {{ $interview->scheduled_at?->format('d M Y H:i') ?? 'Belum dijadwalkan' }}
                    @if ($interview->location_or_link) · {{ $interview->location_or_link }} @endif
                </div>
                @if ($interview->interviewers->isNotEmpty())
                    <div style="color:var(--muted); font-size:.82rem; margin-top:.15rem;">
                        Pewawancara: {{ $interview->interviewers->pluck('name')->join(', ') }}
                    </div>
                @endif

                <details style="margin-top:.6rem;">
                    <summary style="font-size:.82rem; color:var(--muted); cursor:pointer;">
                        Feedback ({{ $interview->feedback->count() }})
                    </summary>

                    @foreach ($interview->feedback as $fb)
                        <div style="padding:.5rem 0; font-size:.85rem;">
                            <strong>{{ $fb->interviewer->name }}</strong>
                            @if ($fb->rating) — {{ $fb->rating }}/5 @endif
                            @if ($fb->comments)
                                <div style="color:var(--muted); margin-top:.15rem;">{{ $fb->comments }}</div>
                            @endif
                        </div>
                    @endforeach

                    <form action="{{ route('applications.interviews.feedback', [$jobPosting, $application, $interview]) }}" method="POST" style="margin-top:.6rem;">
                        @csrf
                        <div class="form-row">
                            <div>
                                <label for="interviewer_id-{{ $interview->id }}">Interviewer</label>
                                <select id="interviewer_id-{{ $interview->id }}" name="interviewer_id" required>
                                    <option value="">Pilih...</option>
                                    @foreach ($interview->interviewers as $iv)
                                        <option value="{{ $iv->id }}">{{ $iv->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="rating-{{ $interview->id }}">Rating (1-5)</label>
                                <select id="rating-{{ $interview->id }}" name="rating">
                                    <option value="">-</option>
                                    @for ($r = 1; $r <= 5; $r++)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <label for="comments-{{ $interview->id }}">Komentar</label>
                        <input type="text" id="comments-{{ $interview->id }}" name="comments" placeholder="Komentar singkat">
                        <button type="submit" style="margin-top:.5rem;">Simpan Feedback</button>
                    </form>
                </details>

                <form action="{{ route('applications.interviews.destroy', [$jobPosting, $application, $interview]) }}" method="POST"
                      onsubmit="return confirm('Hapus jadwal interview ini?');" style="margin-top:.5rem;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Hapus Interview</button>
                </form>
            </div>
        @empty
            <p style="color:var(--muted); font-size:.9rem;">Belum ada interview terjadwal.</p>
        @endforelse

        <details style="margin-top:1rem;">
            <summary style="font-size:.9rem; cursor:pointer;">+ Jadwalkan Interview Baru</summary>
            <form action="{{ route('applications.interviews.store', [$jobPosting, $application]) }}" method="POST" style="margin-top:.9rem;">
                @csrf
                <div class="form-row">
                    <div>
                        <label for="scheduled_at">Tanggal & Jam</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" required>
                    </div>
                    <div>
                        <label for="location_or_link">Lokasi / Link</label>
                        <input type="text" id="location_or_link" name="location_or_link" placeholder="Kantor / link Zoom / Meet">
                    </div>
                </div>
                <label for="interviewers">Interviewer</label>
                <select id="interviewers" name="interviewers[]" multiple required style="min-height:6rem;">
                    @foreach ($hrUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <p style="color:var(--muted); font-size:.8rem; margin:.3rem 0 .8rem;">Ctrl/Cmd + klik buat pilih lebih dari satu.</p>
                <button type="submit">Jadwalkan</button>
            </form>
        </details>
    </div>

    <div class="card">
        <h3 style="margin-bottom:1.25rem;">Dokumen</h3>

        @forelse ($application->documents as $doc)
            <div style="padding:.6rem 0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div style="font-size:.9rem;">{{ $doc->original_filename }}</div>
                    <div style="color:var(--muted); font-size:.8rem; margin-top:.1rem;">
                        {{ ucfirst($doc->type) }}
                        @if ($doc->size_bytes) · {{ number_format($doc->size_bytes / 1024, 0) }} KB @endif
                    </div>
                </div>
                <div style="font-size:.85rem; display:flex; gap:.75rem;">
                    <a href="{{ route('applications.documents.download', [$jobPosting, $application, $doc]) }}">Unduh</a>
                    <form action="{{ route('applications.documents.destroy', [$jobPosting, $application, $doc]) }}" method="POST"
                          onsubmit="return confirm('Hapus dokumen ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p style="color:var(--muted); font-size:.9rem;">Belum ada dokumen diupload.</p>
        @endforelse

        <form action="{{ route('applications.documents.store', [$jobPosting, $application]) }}" method="POST" enctype="multipart/form-data" style="margin-top:1.25rem;">
            @csrf
            <div class="form-row">
                <div>
                    <label for="doc-type">Jenis Dokumen</label>
                    <select id="doc-type" name="type" required>
                        <option value="cv">CV</option>
                        <option value="portfolio">Portfolio</option>
                        <option value="transcript">Transkrip</option>
                        <option value="certificate">Sertifikat</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="doc-file">File</label>
                    <input type="file" id="doc-file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>
            </div>
            <p style="color:var(--muted); font-size:.8rem; margin:.3rem 0 .8rem;">Maks 5MB. Format: PDF, Word, JPG, PNG.</p>
            <button type="submit">Upload</button>
        </form>
    </div>
@endsection