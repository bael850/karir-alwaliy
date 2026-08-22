<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SyncApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi sesungguhnya sudah dihandle middleware auth:sanctum di route.
        return true;
    }

    public function rules(): array
    {
        return [
            'external_id' => ['required', 'string', 'max:191'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}