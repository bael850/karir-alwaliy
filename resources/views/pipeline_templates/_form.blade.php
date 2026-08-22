@php
    // $template: null saat create, instance PipelineTemplate saat edit
    // $stages: array asosiatif [['name' => ..., 'is_final' => bool], ...]
@endphp

<div class="section-title">Informasi Dasar</div>

<label for="name">Nama Template <span class="required">*</span></label>
<input type="text" id="name" name="name" value="{{ old('name', $template->name ?? '') }}" required>
@error('name')<span class="field-hint" style="color:var(--danger);">{{ $message }}</span>@enderror

<label for="description">Deskripsi</label>
<textarea id="description" name="description" rows="2">{{ old('description', $template->description ?? '') }}</textarea>

<label style="display:flex; align-items:center; gap:.5rem; font-weight:600; margin-bottom:1.35rem;">
    <input type="checkbox" name="is_default" value="1" style="width:auto; margin:0;"
        @checked(old('is_default', $template->is_default ?? false))>
    Jadikan template default
</label>
<span class="field-hint" style="margin-top:-1.1rem;">Template default otomatis kepilih saat bikin job posting baru.</span>

<div class="section-title">Stage Pipeline <span class="required">*</span></div>
<span class="field-hint" style="margin-top:0; margin-bottom:1rem;">Urutan sesuai yang ditampilkan di bawah. Minimal 1 stage.</span>

<div id="stage-rows">
    @foreach ($stages as $i => $stage)
        <div class="stage-row" style="display:flex; align-items:center; gap:.6rem; margin-bottom:.75rem;">
            <span style="color:var(--muted); font-size:.85rem; width:1.5rem;">{{ $i + 1 }}.</span>
            <input type="text" name="stages[{{ $i }}][name]" value="{{ $stage['name'] }}" placeholder="Nama stage" required style="margin-bottom:0; flex:1;">
            <label style="display:flex; align-items:center; gap:.35rem; font-weight:500; font-size:.85rem; margin:0; white-space:nowrap;">
                <input type="checkbox" name="stages[{{ $i }}][is_final]" value="1" style="width:auto; margin:0;" @checked(!empty($stage['is_final']))>
                Final
            </label>
            <button type="button" class="btn-danger" onclick="removeStageRow(this)">Hapus</button>
        </div>
    @endforeach
</div>

<button type="button" class="btn-ghost" onclick="addStageRow()" style="margin-bottom:1.5rem;">+ Tambah Stage</button>
@error('stages')<div class="field-hint" style="color:var(--danger); margin-top:-1rem;">{{ $message }}</div>@enderror

<div class="form-actions">
    <button type="submit">Simpan</button>
    <a href="{{ route('pipeline-templates.index') }}" class="btn-ghost">Batal</a>
</div>

<template id="stage-row-template">
    <div class="stage-row" style="display:flex; align-items:center; gap:.6rem; margin-bottom:.75rem;">
        <span class="stage-order" style="color:var(--muted); font-size:.85rem; width:1.5rem;">•</span>
        <input type="text" name="stages[__INDEX__][name]" placeholder="Nama stage" required style="margin-bottom:0; flex:1;">
        <label style="display:flex; align-items:center; gap:.35rem; font-weight:500; font-size:.85rem; margin:0; white-space:nowrap;">
            <input type="checkbox" name="stages[__INDEX__][is_final]" value="1" style="width:auto; margin:0;">
            Final
        </label>
        <button type="button" class="btn-danger" onclick="removeStageRow(this)">Hapus</button>
    </div>
</template>

<script>
    function renumberStages() {
        document.querySelectorAll('#stage-rows .stage-row').forEach((row, idx) => {
            row.querySelector('span').textContent = (idx + 1) + '.';
        });
    }

    function addStageRow() {
        const container = document.getElementById('stage-rows');
        const template = document.getElementById('stage-row-template').innerHTML;
        const index = container.querySelectorAll('.stage-row').length;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.replaceAll('__INDEX__', index);
        container.appendChild(wrapper.firstElementChild);
        renumberStages();
    }

    function removeStageRow(button) {
        const rows = document.querySelectorAll('#stage-rows .stage-row');
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 stage.');
            return;
        }
        button.closest('.stage-row').remove();
        renumberStages();
    }
</script>