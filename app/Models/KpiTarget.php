<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_id',
        'insentif_pokok',
        // Kategori Proses
        'sosmed_content_plan_pct',
        'sales_digital_posting_target',
        'tiktok_live_daily_hours',
        'google_review_target',
        'google_rating_target',
        'google_foto_daily_target',
        'meta_ads_deadline_day',
        'report_compliance_pct',
        'rapat_review_target',
        // Kategori Result
        'ig_followers_growth_target',
        'tiktok_followers_growth_target',
        'prospect_digital_ratio_pct',
        'dealer_stu_target',
        'ads_cpm_max',
        'ads_cpc_max',
        'ads_ctr_min',
        'stu_digital_ratio_pct',
        // Syarat Pembayaran
        'min_result_score',
        'min_proses_score',
        'branch_dealer_stu_targets',
        // Legacy fields for backward compatibility
        'ig_feed_target',
        'ig_reels_target',
        'ig_story_target',
        'ig_followers_target',
        'fb_post_target',
        'fb_marketplace_target',
        'fb_followers_target',
        'tiktok_post_target',
        'tiktok_live_target',
        'tiktok_followers_target',
    ];

    protected $casts = [
        'insentif_pokok' => 'decimal:2',
        'sosmed_content_plan_pct' => 'decimal:2',
        'sales_digital_posting_target' => 'integer',
        'tiktok_live_daily_hours' => 'decimal:2',
        'google_review_target' => 'integer',
        'google_rating_target' => 'decimal:2',
        'google_foto_daily_target' => 'integer',
        'meta_ads_deadline_day' => 'integer',
        'report_compliance_pct' => 'decimal:2',
        'rapat_review_target' => 'integer',
        'ig_followers_growth_target' => 'integer',
        'tiktok_followers_growth_target' => 'integer',
        'prospect_digital_ratio_pct' => 'decimal:2',
        'dealer_stu_target' => 'integer',
        'ads_cpm_max' => 'decimal:2',
        'ads_cpc_max' => 'decimal:2',
        'ads_ctr_min' => 'decimal:2',
        'stu_digital_ratio_pct' => 'decimal:2',
        'min_result_score' => 'decimal:2',
        'min_proses_score' => 'decimal:2',
        'branch_dealer_stu_targets' => 'array',
    ];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }

    /**
     * Get dealer STU target for a specific branch (with fallback to default)
     */
    public function getDealerStuTargetForBranch(int $branchId): int
    {
        if (!empty($this->branch_dealer_stu_targets) && isset($this->branch_dealer_stu_targets[$branchId])) {
            return (int) $this->branch_dealer_stu_targets[$branchId];
        }
        return (int) ($this->dealer_stu_target ?: 50);
    }
}
