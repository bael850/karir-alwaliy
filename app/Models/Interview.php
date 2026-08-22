<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interview extends Model
{
    protected $fillable = [
        'application_id', 'stage_id', 'scheduled_at', 'location_or_link', 'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (Interview $interview) {
            app(NotificationService::class)->send($interview->application, 'interview_scheduled', [
                'applicant_name' => $interview->application->applicant->name,
                'job_title' => $interview->application->jobPosting->title,
                'stage_name' => $interview->stage->name,
                'scheduled_at' => optional($interview->scheduled_at)->format('d M Y H:i'),
                'location_or_link' => $interview->location_or_link ?? '-',
            ]);
        });
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(JobPostingStage::class, 'stage_id');
    }

    public function interviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'interview_interviewers');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(InterviewFeedback::class);
    }
}