<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
            'rating' => ['nullable', 'integer', 'between:1,10'],
            'review_text' => ['nullable', 'string'],
            'spoiler' => ['boolean'],
            'reread' => ['boolean'],
        ];
    }
}
