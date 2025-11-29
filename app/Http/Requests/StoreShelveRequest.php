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
            'book_id' => ['required', 'exists:books,id'],
            'status' => ['nullable', 'in:read,reading,tbr,dnf'],
            'times_read' => ['nullable', 'integer', 'min:0'],
            'favourite' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
