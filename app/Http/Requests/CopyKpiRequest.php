<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopyKpiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_tahun' => ['required', 'integer', 'between:2020,2099'],
            'from_bulan' => ['required', 'integer', 'between:1,12'],
            'to_tahun' => ['required', 'integer', 'between:2020,2099'],
            'to_bulan' => ['required', 'integer', 'between:1,12'],
        ];
    }
}
