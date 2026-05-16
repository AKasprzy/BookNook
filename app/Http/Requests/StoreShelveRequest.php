<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShelveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_edition_id' => ['required', 'exists:book_editions,id'],
            'status' => ['required', 'string'],
            'favourite' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
            'times_read' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
