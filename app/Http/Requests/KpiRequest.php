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
            'insentif_pokok' => ['nullable', 'numeric', 'min:0'],
            
            // Kategori: Indikator Proses (Total Bobot: 100)
            'sosmed_content_plan_pct' => ['nullable', 'numeric', 'between:0,100'],
            'sales_digital_posting_target' => ['nullable', 'integer', 'min:0'],
            'tiktok_live_daily_hours' => ['nullable', 'numeric', 'min:0'],
            'google_review_target' => ['nullable', 'integer', 'min:0'],
            'google_rating_target' => ['nullable', 'numeric', 'between:0,5'],
            'google_foto_daily_target' => ['nullable', 'integer', 'min:0'],
            'meta_ads_deadline_day' => ['nullable', 'integer', 'between:1,31'],
            'report_compliance_pct' => ['nullable', 'numeric', 'between:0,100'],
            'rapat_review_target' => ['nullable', 'integer', 'min:0'],

            // Kategori: Indikator Result (Total Bobot: 100)
            'ig_followers_growth_target' => ['nullable', 'integer', 'min:0'],
            'tiktok_followers_growth_target' => ['nullable', 'integer', 'min:0'],
            'prospect_digital_ratio_pct' => ['nullable', 'numeric', 'min:0'],
            'dealer_stu_target' => ['nullable', 'integer', 'min:0'],
            'ads_cpm_max' => ['nullable', 'numeric', 'min:0'],
            'ads_cpc_max' => ['nullable', 'numeric', 'min:0'],
            'ads_ctr_min' => ['nullable', 'numeric', 'min:0'],
            'stu_digital_ratio_pct' => ['nullable', 'numeric', 'min:0'],

            // Syarat Pembayaran
            'min_result_score' => ['nullable', 'numeric', 'between:0,100'],
            'min_proses_score' => ['nullable', 'numeric', 'between:0,100'],
            'branch_dealer_stu_targets' => ['nullable', 'array'],

            // Legacy backward-compatibility fields (optional)
            'ig_feed_target' => ['nullable', 'integer', 'min:0'],
            'ig_reels_target' => ['nullable', 'integer', 'min:0'],
            'ig_story_target' => ['nullable', 'integer', 'min:0'],
            'ig_followers_target' => ['nullable', 'integer', 'min:0'],
            'fb_post_target' => ['nullable', 'integer', 'min:0'],
            'fb_marketplace_target' => ['nullable', 'integer', 'min:0'],
            'fb_followers_target' => ['nullable', 'integer', 'min:0'],
            'tiktok_post_target' => ['nullable', 'integer', 'min:0'],
            'tiktok_live_target' => ['nullable', 'integer', 'min:0'],
            'tiktok_followers_target' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
