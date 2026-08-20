<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'priority' => ['required', 'in:Low,Medium,High'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:To Do,Progress,Done'],
            'color_badge' => ['required', 'string', 'max:20'],
        ];
    }
}
