@extends('layouts.app')

@section('content')
    <a href="{{ route('job-postings.screening-questions.index', $jobPosting) }}" style="font-size:.85rem; color:var(--muted);">← Kembali ke Pertanyaan Screening</a>

    <h2 style="margin:.9rem 0 1.75rem;">Edit Pertanyaan Screening</h2>

    <div class="card">
        <form action="{{ route('job-postings.screening-questions.update', [$jobPosting, $question]) }}" method="POST">
            @csrf
            @method('PUT')
            @include('screening_questions._form', ['jobPosting' => $jobPosting, 'question' => $question])
        </form>
    </div>
@endsection