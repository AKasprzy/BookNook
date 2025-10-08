<?php

namespace App\Http\Requests;

use App\Enums\BookFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateBookEditionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => ['sometimes', 'exists:books,id'],
            'edition_title' => ['sometimes', 'string', 'max:255'],
            'edition_publication_date' => ['sometimes', 'date'],
            'format' => ['sometimes', new Enum(BookFormat::class)],
            'edition_language' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'length_minutes' => ['nullable', 'integer', 'min:1'],
            'cover_url' => ['nullable', 'url'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'average_rating' => ['nullable', 'numeric', 'between:0,10'],
        ];
    }
}
