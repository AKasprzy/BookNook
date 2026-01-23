<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookEditionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'edition_title' => ['required', 'string', 'max:255'],
            'edition_publication_date' => ['nullable', 'date'],
            'format' => ['nullable', 'string', 'max:255'],
            'edition_language' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'length_minutes' => ['nullable', 'integer', 'min:1'],
            'cover_url' => ['nullable', 'url'],
            'publisher' => ['nullable', 'string', 'max:255'],
        ];
    }
}
