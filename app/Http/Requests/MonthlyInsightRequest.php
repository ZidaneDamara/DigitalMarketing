<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyInsightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'tahun' => ['required', 'integer', 'between:2020,2099'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'ig_views' => ['required', 'integer', 'min:0'],
            'ig_reach' => ['required', 'integer', 'min:0'],
            'ig_accounts_reached' => ['required', 'integer', 'min:0'],
            'ig_profile_visits' => ['required', 'integer', 'min:0'],
            'ig_total_followers' => ['required', 'integer', 'min:0'],
            'ig_male_pct' => ['required', 'numeric', 'between:0,100'],
            'ig_female_pct' => ['required', 'numeric', 'between:0,100'],
            'ig_top_age' => ['nullable', 'string', 'max:255'],
            'ig_top_cities' => ['nullable', 'string', 'max:255'],
            'fb_views' => ['required', 'integer', 'min:0'],
            'fb_total_followers' => ['required', 'integer', 'min:0'],
            'tiktok_views' => ['required', 'integer', 'min:0'],
            'tiktok_total_followers' => ['required', 'integer', 'min:0'],
            'google_total_rating' => ['required', 'numeric', 'between:0,5'],
            'google_total_reviews' => ['required', 'integer', 'min:0'],
            
            // Screenshot uploads
            'screenshot_ig' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'screenshot_fb' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'screenshot_tiktok' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'screenshot_google' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
