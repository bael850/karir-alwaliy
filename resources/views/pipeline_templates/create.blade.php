@extends('layouts.app')

@section('content')
    <a href="{{ route('pipeline-templates.index') }}" style="font-size:.85rem; color:var(--muted);">← Kembali ke Template Pipeline</a>

    <h2 style="margin:.9rem 0 1.75rem;">Template Pipeline Baru</h2>

    <div class="card">
        <form action="{{ route('pipeline-templates.store') }}" method="POST">
            @csrf
            @include('pipeline_templates._form', ['template' => null, 'stages' => old('stages', [['name' => '', 'is_final' => false]])])
        </form>
    </div>
@endsection