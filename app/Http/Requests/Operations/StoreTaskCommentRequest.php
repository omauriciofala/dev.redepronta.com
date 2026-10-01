<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'O comentário não pode ser vazio.',
            'comment.max' => 'O comentário não pode exceder 5.000 caracteres.',
        ];
    }
}
