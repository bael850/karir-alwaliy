@extends('layouts.app')

@section('content')
    <a href="{{ route('pipeline-templates.index') }}" style="font-size:.85rem; color:var(--muted);">← Kembali ke Template Pipeline</a>

    <h2 style="margin:.9rem 0 1.75rem;">Edit Template: {{ $template->name }}</h2>

    <div class="card">
        <form action="{{ route('pipeline-templates.update', $template) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pipeline_templates._form', [
                'template' => $template,
                'stages' => old('stages', $template->stages->map(fn ($s) => ['name' => $s->name, 'is_final' => $s->is_final])->all()),
            ])
        </form>
    </div>
@endsection