<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'applicant_id', 'job_posting_id', 'current_stage_id',
        'source', 'applied_at', 'last_synced_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(JobPostingStage::class, 'current_stage_id');
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(ApplicationStageHistory::class)->latest('moved_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class)->latest();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ApplicationAnswer::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(ApplicantDocument::class, 'documentable');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Pindahin lamaran ke stage baru + catat ke history sekaligus.
     */
    public function moveToStage(JobPostingStage $newStage, User $movedBy, ?string $note = null): void
    {
        $fromStageId = $this->current_stage_id;

        $this->update(['current_stage_id' => $newStage->id]);

        $this->stageHistory()->create([
            'from_stage_id' => $fromStageId,
            'to_stage_id' => $newStage->id,
            'moved_by' => $movedBy->id,
            'note' => $note,
            'moved_at' => now(),
        ]);

        app(NotificationService::class)->send($this, 'application_stage_changed', [
            'applicant_name' => $this->applicant->name,
            'job_title' => $this->jobPosting->title,
            'stage_name' => $newStage->name,
        ]);
    }
}