<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'original_language' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'original_publication_date' => ['nullable', 'date'],
            'series' => ['nullable', 'string', 'max:255'],
        ];
    }
}
