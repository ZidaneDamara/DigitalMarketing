<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KpiRequest extends FormRequest
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
            'ig_feed_target' => ['required', 'integer', 'min:0'],
            'ig_reels_target' => ['required', 'integer', 'min:0'],
            'ig_story_target' => ['required', 'integer', 'min:0'],
            'ig_followers_target' => ['required', 'integer', 'min:0'],
            'fb_post_target' => ['required', 'integer', 'min:0'],
            'fb_marketplace_target' => ['required', 'integer', 'min:0'],
            'fb_followers_target' => ['required', 'integer', 'min:0'],
            'tiktok_post_target' => ['required', 'integer', 'min:0'],
            'tiktok_live_target' => ['required', 'integer', 'min:0'],
            'tiktok_followers_target' => ['required', 'integer', 'min:0'],
            'google_rating_target' => ['required', 'numeric', 'between:0,5'],
            'google_review_target' => ['required', 'integer', 'min:0'],
        ];
    }
}
