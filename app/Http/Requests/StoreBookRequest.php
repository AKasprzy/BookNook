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
            'genre_ids' => ['sometimes', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
            'motif_ids' => ['sometimes', 'array'],
            'motif_ids.*' => ['integer', 'exists:motifs,id'],
            'edition' => ['required', 'array'],
            'edition.edition_title' => ['required', 'string', 'max:255'],
            'edition.edition_publication_date' => ['nullable', 'date'],
            'edition.format' => ['required', 'string', 'in:digital,print,audio'],
            'edition.edition_language' => ['required', 'string', 'max:255'],
            'edition.description' => ['nullable', 'string'],
            'edition.isbn' => ['nullable', 'string', 'max:20'],
            'edition.page_count' => ['nullable', 'integer', 'min:1'],
            'edition.length_minutes' => ['nullable', 'integer', 'min:1'],
            'edition.cover_url' => ['nullable', 'url'],
            'edition.publisher' => ['nullable', 'string', 'max:255'],
        ];
    }
}
