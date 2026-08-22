@php
    $currentType = old('answer_type', $question->answer_type ?? 'text');
    $optionsText = old('options', isset($question) ? implode("\n", $question->options ?? []) : '');
@endphp

<label for="question">Pertanyaan <span class="required">*</span></label>
<textarea id="question" name="question" rows="2" required>{{ old('question', $question->question ?? '') }}</textarea>
@error('question')<span class="field-hint" style="color:var(--danger);">{{ $message }}</span>@enderror

<label for="answer_type">Tipe Jawaban <span class="required">*</span></label>
<select id="answer_type" name="answer_type" onchange="toggleOptionsField(this.value)" required>
    @foreach (['text' => 'Teks Singkat', 'textarea' => 'Teks Panjang', 'number' => 'Angka', 'yes_no' => 'Ya/Tidak', 'select' => 'Pilihan Ganda'] as $value => $label)
        <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
    @endforeach
</select>

<div id="options-field" style="{{ $currentType === 'select' ? '' : 'display:none' }}">
    <label for="options">Daftar Opsi</label>
    <span class="field-hint" style="margin-top:0;">Satu opsi per baris.</span>
    <textarea id="options" name="options" rows="4" placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3">{{ $optionsText }}</textarea>
</div>

<label style="display:flex; align-items:center; gap:.5rem; font-weight:600; margin-bottom:1.35rem;">
    <input type="checkbox" name="is_required" value="1" style="width:auto; margin:0;"
        @checked(old('is_required', $question->is_required ?? true))>
    Wajib dijawab
</label>

<div class="form-actions">
    <button type="submit">Simpan</button>
    <a href="{{ route('job-postings.screening-questions.index', $jobPosting) }}" class="btn-ghost">Batal</a>
</div>

<script>
    function toggleOptionsField(type) {
        document.getElementById('options-field').style.display = (type === 'select') ? '' : 'none';
    }
</script>