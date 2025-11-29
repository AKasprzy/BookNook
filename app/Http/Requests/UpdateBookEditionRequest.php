<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookEditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => ['sometimes', 'exists:books,id'],
            'edition_title' => ['sometimes', 'string', 'max:255'],
            'edition_publication_date' => ['nullable', 'date'],
            'format' => ['nullable', 'string', 'max:255'],
            'edition_language' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'length_minutes' => ['nullable', 'integer', 'min:1'],
            'cover_url' => ['nullable', 'url'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'genre_ids' => ['sometimes', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
            'motif_ids' => ['sometimes', 'array'],
            'motif_ids.*' => ['integer', 'exists:motifs,id'],
        ];
    }
}
