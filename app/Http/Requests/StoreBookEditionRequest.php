<?php

namespace App\Http\Requests;

use App\Enums\BookFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBookEditionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
            'edition_title' => ['required', 'string', 'max:255'],
            'edition_publication_date' => ['nullable', 'date'],
            'format' => ['required', new Enum(BookFormat::class)],
            'edition_language' => ['required', 'string', 'max:255'],
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
