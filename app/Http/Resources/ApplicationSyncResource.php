<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationSyncResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'external_id' => $this->external_id,
            'applicant' => [
                'external_id' => $this->applicant->external_id,
                'name' => $this->applicant->name,
                'email' => $this->applicant->email,
            ],
            'job_posting' => [
                'id' => $this->jobPosting->id,
                'slug' => $this->jobPosting->slug,
                'title' => $this->jobPosting->title,
            ],
            'current_stage' => [
                'name' => $this->currentStage->name,
                'is_final' => $this->currentStage->is_final,
            ],
            'source' => $this->source,
            'applied_at' => optional($this->applied_at)->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}