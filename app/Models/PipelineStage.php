<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineStage extends Model
{
    protected $fillable = ['pipeline_template_id', 'name', 'order', 'is_final'];

    protected $casts = [
        'is_final' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(PipelineTemplate::class, 'pipeline_template_id');
    }
}