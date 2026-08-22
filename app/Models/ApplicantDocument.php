<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApplicantDocument extends Model
{
    protected $fillable = [
        'documentable_type', 'documentable_id',
        'type', 'original_filename', 'path', 'mime_type', 'size_bytes',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}