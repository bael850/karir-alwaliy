<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'name', 'email', 'phone', 'linkedin_url', 'portfolio_url',
        'password', 'email_verified_at', 'retention_until', 'last_synced_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'retention_until' => 'datetime',
        'last_synced_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Dokumen yang nempel langsung ke pelamar (bukan ke lamaran spesifik), misal CV umum.
    public function documents(): MorphMany
    {
        return $this->morphMany(ApplicantDocument::class, 'documentable');
    }

    public function isRegistered(): bool
    {
        return ! is_null($this->password);
    }
}