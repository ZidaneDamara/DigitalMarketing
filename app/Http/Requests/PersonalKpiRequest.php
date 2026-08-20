<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalKpiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun' => ['required', 'integer', 'between:2020,2099'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'kategori' => ['required', 'string', 'max:255'],
            'target' => ['required', 'integer', 'min:0'],
            'realisasi' => ['required', 'integer', 'min:0'],
        ];
    }
}
