<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobPosting extends Model
{
    protected $fillable = [
        'department_id', 'pipeline_template_id', 'created_by',
        'title', 'slug', 'location', 'employment_type',
        'description', 'requirements', 'status',
        'opening_date', 'closing_date',
    ];

    protected $casts = [
        'opening_date' => 'date',
        'closing_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (JobPosting $job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title) . '-' . Str::random(6);
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function pipelineTemplate(): BelongsTo
    {
        return $this->belongsTo(PipelineTemplate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Stage AKTUAL yang dipakai lowongan ini (hasil copy dari template).
    public function stages(): HasMany
    {
        return $this->hasMany(JobPostingStage::class)->orderBy('order');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function screeningQuestions(): HasMany
    {
        return $this->hasMany(JobScreeningQuestion::class)->orderBy('order');
    }

    /**
     * Copy stage dari pipeline template ke job_posting_stages.
     * Dipanggil sekali pas lowongan dibuat.
     */
    public function copyStagesFromTemplate(PipelineTemplate $template): void
    {
        foreach ($template->stages as $stage) {
            $this->stages()->create([
                'name' => $stage->name,
                'order' => $stage->order,
                'is_final' => $stage->is_final,
            ]);
        }
    }
}