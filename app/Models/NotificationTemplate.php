<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    protected $fillable = ['key', 'subject', 'body'];

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}