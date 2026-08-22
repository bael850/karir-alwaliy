{{--
    Butuh $application->load('answers.question') dari controller.
    Include di applications/show.blade.php: @include('partials.screening_answers')
--}}

@if ($application->answers->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h3>Jawaban Screening</h3>
    </div>
    <dl class="answer-list">
        @foreach ($application->answers as $answer)
            <dt>{{ $answer->question->question }}</dt>
            <dd>{{ $answer->answer ?: '-' }}</dd>
        @endforeach
    </dl>
</div>
@endif