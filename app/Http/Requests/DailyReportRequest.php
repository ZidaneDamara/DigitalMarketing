<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $today = now()->format('Y-m-d');

        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'tanggal' => [
                'required',
                'date',
                "in:$today",
            ],
            'ig_feed' => ['required', 'integer', 'min:0'],
            'ig_reels' => ['required', 'integer', 'min:0'],
            'ig_story' => ['required', 'integer', 'min:0'],
            'ig_followers_gained' => ['required', 'integer', 'min:0'],
            'fb_post' => ['required', 'integer', 'min:0'],
            'fb_marketplace' => ['required', 'integer', 'min:0'],
            'fb_followers_gained' => ['required', 'integer', 'min:0'],
            'tiktok_post' => ['required', 'integer', 'min:0'],
            'tiktok_live' => ['required', 'integer', 'min:0'],
            'tiktok_followers_gained' => ['required', 'integer', 'min:0'],
            'google_rating' => ['required', 'numeric', 'between:0,5'],
            'google_review_gained' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.in' => 'Laporan Harian hanya dapat diisi atau diedit untuk tanggal berjalan (00:00 - 23:59). Laporan hari sebelumnya otomatis terkunci.',
        ];
    }
}
