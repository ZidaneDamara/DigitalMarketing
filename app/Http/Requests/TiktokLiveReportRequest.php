<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TiktokLiveReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'branch_id' => 'required|exists:branches,id',
            'nama_host' => 'required|string|max:255',
            'jabatan' => 'required|in:PIC Digital,Sales Digital',
            'tanggal_live' => 'required|date',
            'durasi_jam' => 'required|integer|min:0|max:24',
            'durasi_menit' => 'required|integer|min:0|max:59',
            'jumlah_penonton' => 'required|integer|min:0',
            'jumlah_like' => 'required|integer|min:0',
            'jumlah_komentar' => 'required|integer|min:0',
            'jumlah_share' => 'required|integer|min:0',
            'stu' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ];

        if ($this->isMethod('POST')) {
            $rules['bukti_screenshot'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['bukti_screenshot'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'branch_id.required' => 'Cabang wajib dipilih.',
            'nama_host.required' => 'Nama host / orang yang live wajib diisi.',
            'jabatan.required' => 'Jabatan wajib dipilih.',
            'jabatan.in' => 'Jabatan harus PIC Digital atau Sales Digital.',
            'tanggal_live.required' => 'Tanggal live wajib diisi.',
            'durasi_jam.required' => 'Durasi jam wajib diisi.',
            'durasi_menit.required' => 'Durasi menit wajib diisi.',
            'bukti_screenshot.image' => 'File bukti screenshot harus berupa gambar (JPG, PNG, WEBP).',
            'bukti_screenshot.max' => 'Ukuran file screenshot tidak boleh lebih dari 5MB.',
        ];
    }
}
