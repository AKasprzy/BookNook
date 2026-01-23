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
            'status' => ['nullable', 'in:read,reading,tbr,dnf'],
            'times_read' => ['nullable', 'integer', 'min:0'],
            'favourite' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
