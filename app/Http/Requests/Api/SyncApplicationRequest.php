<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SyncApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_id' => ['required', 'string', 'max:191'],
            'applicant_external_id' => ['required', 'string', 'max:191', 'exists:applicants,external_id'],

            // Boleh kirim salah satu: id internal ATS atau slug lowongan.
            'job_posting_id' => ['required_without:job_posting_slug', 'integer', 'exists:job_postings,id'],
            'job_posting_slug' => ['required_without:job_posting_id', 'string', 'exists:job_postings,slug'],

            'source' => ['nullable', 'string', 'max:100'],
            'applied_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_external_id.exists' => 'Applicant dengan external_id ini belum pernah di-sync ke ATS. Sync applicant-nya dulu sebelum sync application.',
        ];
    }
}