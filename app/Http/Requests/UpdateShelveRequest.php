<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShelveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string'],
            'favourite' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
            'times_read' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
