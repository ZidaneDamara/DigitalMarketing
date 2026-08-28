<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExporterService
{
    /**
     * Export report data to a beautifully styled Excel workbook (.xlsx).
     */
    public function export(string $type, $reports, array $meta = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridLines(true);

        $titleType = ucfirst($type);
        $sheet->setTitle(substr("DMPMS " . $titleType, 0, 31));

        switch ($type) {
            case 'tiktok_live':
                $this->buildTiktokLiveSheet($sheet, $reports, $meta);
                break;
            case 'weekly':
                $this->buildWeeklySheet($sheet, $reports, $meta);
                break;
            case 'daily':
                $this->buildDailySheet($sheet, $reports, $meta);
                break;
            case 'monthly':
            default:
                $this->buildMonthlySheet($sheet, $reports, $meta);
                break;
        }

        $fileName = "DMPMS_{$titleType}_Report_" . date('Ymd_His') . ".xlsx";
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Build TikTok Live Report Worksheet.
     */
    private function buildTiktokLiveSheet($sheet, $reports, array $meta): void
    {
        $lastCol = 'M';
        $subtitle = $this->buildSubtitleText($meta, 'TikTok Live');
        $this->createHeaderBanner($sheet, 'LAPORAN HARIAN LIVE TIKTOK - DMPMS YAMAHA', $subtitle, $lastCol);

        // Calculate KPI Metrics
        $totalSesi = $reports->count();
        $totalPenonton = $reports->sum('jumlah_penonton');
        $totalLikes = $reports->sum('jumlah_like');
        $totalStu = $reports->sum('stu');

        $cards = [
            ['title' => 'Total Sesi Live', 'value' => number_format($totalSesi) . ' Sesi', 'color' => '0284C7'],
            ['title' => 'Total Penonton', 'value' => number_format($totalPenonton) . ' Penonton', 'color' => '16A34A'],
            ['title' => 'Total Likes', 'value' => number_format($totalLikes) . ' Likes', 'color' => 'EA580C'],
            ['title' => 'Total STU (Lead)', 'value' => number_format($totalStu) . ' Unit', 'color' => '9333EA'],
        ];
        $headerRow = $this->createKpiCards($sheet, $cards, 3, 4);

        $headers = [
            'No', 'Kode Cabang', 'Nama Cabang', 'Tanggal Live', 'Nama Host (Yang Live)',
            'Jabatan Host', 'Durasi Jam', 'Durasi Menit', 'Total Menit', 'Penonton',
            'Likes', 'Diinput Oleh', 'Bukti Screenshot', 'Catatan'
        ];
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $this->createTableHeaders($sheet, $headers, $headerRow, $lastCol);

        $dataStartRow = $headerRow + 1;
        $currentRow = $dataStartRow;

        foreach ($reports as $index => $row) {
            $isOdd = ($index % 2 === 1);
            $sheet->setCellValue("A{$currentRow}", $index + 1);
            $sheet->setCellValue("B{$currentRow}", $row->branch->kode ?? '-');
            $sheet->setCellValue("C{$currentRow}", $row->branch->nama_cabang ?? '-');
            $sheet->setCellValue("D{$currentRow}", $row->tanggal_live ? $row->tanggal_live->format('Y-m-d') : '-');
            $sheet->setCellValue("E{$currentRow}", $row->nama_host ?? '-');
            $sheet->setCellValue("F{$currentRow}", $row->jabatan ?? '-');
            $sheet->setCellValue("G{$currentRow}", $row->durasi_jam ?? 0);
            $sheet->setCellValue("H{$currentRow}", $row->durasi_menit ?? 0);
            $sheet->setCellValue("I{$currentRow}", $row->total_minutes ?? 0);
            $sheet->setCellValue("J{$currentRow}", $row->jumlah_penonton ?? 0);
            $sheet->setCellValue("K{$currentRow}", $row->jumlah_like ?? 0);
            $sheet->setCellValue("L{$currentRow}", $row->user->name ?? '-');
            
            $this->setHyperlinkCell($sheet, "M{$currentRow}", $row->bukti_screenshot_url);
            $sheet->setCellValue("N{$currentRow}", $row->catatan ?? '-');

            $this->applyDataRowStyle($sheet, $currentRow, $isOdd, 'N');

            // Alignments & Number formats
            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$currentRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$currentRow}:K{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

            $currentRow++;
        }

        // Add Total Summary Row
        if ($currentRow > $dataStartRow) {
            $sumCols = [
                'G' => $dataStartRow,
                'H' => $dataStartRow,
                'I' => $dataStartRow,
                'J' => $dataStartRow,
                'K' => $dataStartRow,
            ];
            $this->createSummaryRow($sheet, $currentRow, 'N', $sumCols);
        }

        $this->autoSizeColumns($sheet, 'N');
    }

    /**
     * Build Daily Report Worksheet.
     */
    private function buildDailySheet($sheet, $reports, array $meta): void
    {
        $subtitle = $this->buildSubtitleText($meta, 'Laporan Harian');
        $this->createHeaderBanner($sheet, 'LAPORAN HARIAN DIGITAL MARKETING - DMPMS YAMAHA', $subtitle, 'Q');

        $cards = [
            ['title' => 'Total IG Feed', 'value' => number_format($reports->sum('ig_feed')) . ' Post', 'color' => '0284C7'],
            ['title' => 'Total IG Reels', 'value' => number_format($reports->sum('ig_reels')) . ' Reels', 'color' => '9333EA'],
            ['title' => 'Total TikTok Post', 'value' => number_format($reports->sum('tiktok_post')) . ' Video', 'color' => 'EA580C'],
            ['title' => 'Followers Gained', 'value' => '+' . number_format($reports->sum('ig_followers_gained') + $reports->sum('fb_followers_gained') + $reports->sum('tiktok_followers_gained')), 'color' => '16A34A'],
        ];
        $headerRow = $this->createKpiCards($sheet, $cards, 3, 4);

        $headers = [
            'No', 'Kode Cabang', 'Nama Cabang', 'Tanggal', 
            'IG Feed', 'IG Reels', 'IG Story', 'IG Followers (+)',
            'FB Post', 'FB Marketplace', 'FB Followers (+)',
            'TikTok Post', 'TikTok Live', 'TikTok Followers (+)',
            'Google Rating', 'Google Review (+)', 'Catatan'
        ];
        $this->createTableHeaders($sheet, $headers, $headerRow, 'Q');

        $dataStartRow = $headerRow + 1;
        $currentRow = $dataStartRow;

        foreach ($reports as $index => $row) {
            $isOdd = ($index % 2 === 1);
            $sheet->setCellValue("A{$currentRow}", $index + 1);
            $sheet->setCellValue("B{$currentRow}", $row->branch->kode ?? '-');
            $sheet->setCellValue("C{$currentRow}", $row->branch->nama_cabang ?? '-');
            $sheet->setCellValue("D{$currentRow}", $row->tanggal ? $row->tanggal->format('Y-m-d') : '-');
            $sheet->setCellValue("E{$currentRow}", $row->ig_feed);
            $sheet->setCellValue("F{$currentRow}", $row->ig_reels);
            $sheet->setCellValue("G{$currentRow}", $row->ig_story);
            $sheet->setCellValue("H{$currentRow}", $row->ig_followers_gained);
            $sheet->setCellValue("I{$currentRow}", $row->fb_post);
            $sheet->setCellValue("J{$currentRow}", $row->fb_marketplace);
            $sheet->setCellValue("K{$currentRow}", $row->fb_followers_gained);
            $sheet->setCellValue("L{$currentRow}", $row->tiktok_post);
            $sheet->setCellValue("M{$currentRow}", $row->tiktok_live);
            $sheet->setCellValue("N{$currentRow}", $row->tiktok_followers_gained);
            $sheet->setCellValue("O{$currentRow}", $row->google_rating);
            $sheet->setCellValue("P{$currentRow}", $row->google_review_gained);
            $sheet->setCellValue("Q{$currentRow}", $row->catatan ?? '-');

            $this->applyDataRowStyle($sheet, $currentRow, $isOdd, 'Q');

            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}:P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$currentRow}:P{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

            $currentRow++;
        }

        if ($currentRow > $dataStartRow) {
            $sumCols = [];
            foreach (range('E', 'P') as $col) {
                $sumCols[$col] = $dataStartRow;
            }
            $this->createSummaryRow($sheet, $currentRow, 'Q', $sumCols);
        }

        $this->autoSizeColumns($sheet, 'Q');
    }

    /**
     * Build Weekly Report Worksheet.
     */
    private function buildWeeklySheet($sheet, $reports, array $meta): void
    {
        $subtitle = $this->buildSubtitleText($meta, 'Laporan Mingguan');
        $this->createHeaderBanner($sheet, 'LAPORAN MINGGUAN KONTEN & INSIGHT - DMPMS YAMAHA', $subtitle, 'AB');

        $cards = [
            ['title' => 'Total Views', 'value' => number_format($reports->sum('views')), 'color' => '0284C7'],
            ['title' => 'Total Reach', 'value' => number_format($reports->sum('account_reached')), 'color' => '16A34A'],
            ['title' => 'Total Interaksi', 'value' => number_format($reports->sum('total_interactions')), 'color' => 'EA580C'],
            ['title' => 'Total Follows', 'value' => '+' . number_format($reports->sum('follows')), 'color' => '9333EA'],
        ];
        $headerRow = $this->createKpiCards($sheet, $cards, 3, 4);

        $headers = [
            'No', 'Kode Cabang', 'Nama Cabang', 'Tanggal Post', 'Minggu Ke', 'Tahun', 'Link Content', 
            'Views', 'Account Reached', 'Interaksi Followers', 'Interaksi Non-Followers', 'Total Interaksi',
            'Likes', 'Shares', 'Saves', 'Comments', 'Reposts',
            'Profile Visits', 'External Link Taps', 'Follows',
            'Source Feed (%)', 'Source Profile (%)', 'Source Stories (%)',
            'Gender Men (%)', 'Gender Women (%)', 'Top Country', 'Top Age', 'Catatan'
        ];
        $this->createTableHeaders($sheet, $headers, $headerRow, 'AB');

        $dataStartRow = $headerRow + 1;
        $currentRow = $dataStartRow;

        foreach ($reports as $index => $row) {
            $isOdd = ($index % 2 === 1);
            $sheet->setCellValue("A{$currentRow}", $index + 1);
            $sheet->setCellValue("B{$currentRow}", $row->branch->kode ?? '-');
            $sheet->setCellValue("C{$currentRow}", $row->branch->nama_cabang ?? '-');
            $sheet->setCellValue("D{$currentRow}", $row->tanggal_post ? $row->tanggal_post->format('Y-m-d') : '-');
            $sheet->setCellValue("E{$currentRow}", $row->minggu_ke);
            $sheet->setCellValue("F{$currentRow}", $row->tahun);
            
            $this->setHyperlinkCell($sheet, "G{$currentRow}", $row->link_content, '🔗 Content Link');

            $sheet->setCellValue("H{$currentRow}", $row->views);
            $sheet->setCellValue("I{$currentRow}", $row->account_reached);
            $sheet->setCellValue("J{$currentRow}", $row->interactions_followers);
            $sheet->setCellValue("K{$currentRow}", $row->interactions_non_followers);
            $sheet->setCellValue("L{$currentRow}", $row->total_interactions);
            $sheet->setCellValue("M{$currentRow}", $row->likes);
            $sheet->setCellValue("N{$currentRow}", $row->shares);
            $sheet->setCellValue("O{$currentRow}", $row->saves);
            $sheet->setCellValue("P{$currentRow}", $row->comments);
            $sheet->setCellValue("Q{$currentRow}", $row->reposts);
            $sheet->setCellValue("R{$currentRow}", $row->profile_visits);
            $sheet->setCellValue("S{$currentRow}", $row->external_link_taps);
            $sheet->setCellValue("T{$currentRow}", $row->follows);
            $sheet->setCellValue("U{$currentRow}", $row->source_feed_pct);
            $sheet->setCellValue("V{$currentRow}", $row->source_profile_pct);
            $sheet->setCellValue("W{$currentRow}", $row->source_stories_pct);
            $sheet->setCellValue("X{$currentRow}", $row->gender_men_pct);
            $sheet->setCellValue("Y{$currentRow}", $row->gender_women_pct);
            $sheet->setCellValue("Z{$currentRow}", $row->top_country);
            $sheet->setCellValue("AA{$currentRow}", $row->top_age);
            $sheet->setCellValue("AB{$currentRow}", $row->catatan ?? '-');

            $this->applyDataRowStyle($sheet, $currentRow, $isOdd, 'AB');

            $sheet->getStyle("A{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$currentRow}:Y{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$currentRow}:T{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

            $currentRow++;
        }

        if ($currentRow > $dataStartRow) {
            $sumCols = [];
            foreach (range('H', 'T') as $col) {
                $sumCols[$col] = $dataStartRow;
            }
            $this->createSummaryRow($sheet, $currentRow, 'AB', $sumCols);
        }

        $this->autoSizeColumns($sheet, 'AB');
    }

    /**
     * Build Monthly Insight Worksheet.
     */
    private function buildMonthlySheet($sheet, $reports, array $meta): void
    {
        $subtitle = $this->buildSubtitleText($meta, 'Monthly Insight');
        $this->createHeaderBanner($sheet, 'LAPORAN MONTHLY INSIGHT & AUDIENCE - DMPMS YAMAHA', $subtitle, 'T');

        $cards = [
            ['title' => 'Total IG Views', 'value' => number_format($reports->sum('ig_views')), 'color' => '0284C7'],
            ['title' => 'Total IG Reach', 'value' => number_format($reports->sum('ig_reach')), 'color' => '16A34A'],
            ['title' => 'Total FB Views', 'value' => number_format($reports->sum('fb_views')), 'color' => 'EA580C'],
            ['title' => 'Total TikTok Views', 'value' => number_format($reports->sum('tiktok_views')), 'color' => '9333EA'],
        ];
        $headerRow = $this->createKpiCards($sheet, $cards, 3, 4);

        $headers = [
            'No', 'Kode Cabang', 'Nama Cabang', 'Tahun', 'Bulan',
            'IG Views', 'IG Reach', 'IG Accounts Reached', 'IG Profile Visits', 'IG Followers',
            'IG Male %', 'IG Female %', 'IG Top Age', 'IG Top Cities',
            'FB Views', 'FB Followers', 'TikTok Views', 'TikTok Followers',
            'Google Rating', 'Google Reviews'
        ];
        $this->createTableHeaders($sheet, $headers, $headerRow, 'T');

        $dataStartRow = $headerRow + 1;
        $currentRow = $dataStartRow;

        foreach ($reports as $index => $row) {
            $isOdd = ($index % 2 === 1);
            $sheet->setCellValue("A{$currentRow}", $index + 1);
            $sheet->setCellValue("B{$currentRow}", $row->branch->kode ?? '-');
            $sheet->setCellValue("C{$currentRow}", $row->branch->nama_cabang ?? '-');
            $sheet->setCellValue("D{$currentRow}", $row->tahun);
            $sheet->setCellValue("E{$currentRow}", $row->bulan);
            $sheet->setCellValue("F{$currentRow}", $row->ig_views);
            $sheet->setCellValue("G{$currentRow}", $row->ig_reach);
            $sheet->setCellValue("H{$currentRow}", $row->ig_accounts_reached);
            $sheet->setCellValue("I{$currentRow}", $row->ig_profile_visits);
            $sheet->setCellValue("J{$currentRow}", $row->ig_total_followers);
            $sheet->setCellValue("K{$currentRow}", $row->ig_male_pct);
            $sheet->setCellValue("L{$currentRow}", $row->ig_female_pct);
            $sheet->setCellValue("M{$currentRow}", $row->ig_top_age);
            $sheet->setCellValue("N{$currentRow}", $row->ig_top_cities);
            $sheet->setCellValue("O{$currentRow}", $row->fb_views);
            $sheet->setCellValue("P{$currentRow}", $row->fb_total_followers);
            $sheet->setCellValue("Q{$currentRow}", $row->tiktok_views);
            $sheet->setCellValue("R{$currentRow}", $row->tiktok_total_followers);
            $sheet->setCellValue("S{$currentRow}", $row->google_total_rating);
            $sheet->setCellValue("T{$currentRow}", $row->google_total_reviews);

            $this->applyDataRowStyle($sheet, $currentRow, $isOdd, 'T');

            $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("O{$currentRow}:T{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $currentRow++;
        }

        $this->autoSizeColumns($sheet, 'T');
    }

    /**
     * Create Header Banner at Row 1 & Row 2.
     */
    private function createHeaderBanner($sheet, string $title, string $subtitle, string $lastColumn): void
    {
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', $subtitle);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
            'font' => ['italic' => true, 'size' => 9.5, 'color' => ['rgb' => '94A3B8'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    /**
     * Create KPI Summary Cards Block.
     */
    private function createKpiCards($sheet, array $cards, int $startRow, int $cardCount): int
    {
        $colIndexes = ['A', 'D', 'G', 'J', 'M'];
        $rowTop = $startRow + 1; // Row 4
        $rowVal = $startRow + 2; // Row 5

        foreach ($cards as $idx => $card) {
            if (!isset($colIndexes[$idx])) break;
            $startCol = $colIndexes[$idx];
            $endCol = chr(ord($startCol) + 2);

            $sheet->mergeCells("{$startCol}{$rowTop}:{$endCol}{$rowTop}");
            $sheet->mergeCells("{$startCol}{$rowVal}:{$endCol}{$rowVal}");

            $sheet->setCellValue("{$startCol}{$rowTop}", strtoupper($card['title']));
            $sheet->setCellValue("{$startCol}{$rowVal}", $card['value']);

            $sheet->getStyle("{$startCol}{$rowTop}:{$endCol}{$rowVal}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                ],
            ]);

            $sheet->getStyle("{$startCol}{$rowTop}:{$endCol}{$rowTop}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 8.5, 'color' => ['rgb' => '64748B'], 'name' => 'Segoe UI'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("{$startCol}{$rowVal}:{$endCol}{$rowVal}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12.5, 'color' => ['rgb' => $card['color'] ?? '0F172A'], 'name' => 'Segoe UI'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        $sheet->getRowDimension($rowTop)->setRowHeight(18);
        $sheet->getRowDimension($rowVal)->setRowHeight(24);

        return $rowVal + 2; // Return table header row (e.g. Row 7)
    }

    /**
     * Create Styled Table Header Row.
     */
    private function createTableHeaders($sheet, array $headers, int $headerRow, string $lastColumn): void
    {
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        foreach ($headers as $colIdx => $headerText) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $headerText);
        }

        $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0F172A']],
            ],
        ]);
    }

    /**
     * Apply Data Row Styling with Zebra Striping.
     */
    private function applyDataRowStyle($sheet, int $row, bool $isOdd, string $lastColumn): void
    {
        $sheet->getRowDimension($row)->setRowHeight(22);
        $bgColor = $isOdd ? 'F8FAFC' : 'FFFFFF';

        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
            'font' => ['size' => 9.5, 'color' => ['rgb' => '1E293B'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
            ],
        ]);
    }

    /**
     * Format Hyperlink Cells.
     */
    private function setHyperlinkCell($sheet, string $cell, ?string $url, string $label = '🔗 Buka SS'): void
    {
        if (!$url || $url === '-') {
            $sheet->setCellValue($cell, '-');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            return;
        }

        $sheet->setCellValue($cell, $label);
        $sheet->getCell($cell)->getHyperlink()->setUrl($url);
        $sheet->getCell($cell)->getHyperlink()->setTooltip('Klik untuk membuka link');

        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'color' => ['rgb' => '0284C7'],
                'underline' => true,
                'bold' => true,
                'size' => 9.5,
                'name' => 'Segoe UI',
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    /**
     * Create Summary Total Row at the Bottom.
     */
    private function createSummaryRow($sheet, int $totalRow, string $lastColumn, array $sumColumns): void
    {
        $sheet->getRowDimension($totalRow)->setRowHeight(26);
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');

        foreach ($sumColumns as $colLetter => $startRow) {
            $sheet->setCellValue("{$colLetter}{$totalRow}", "=SUM({$colLetter}{$startRow}:{$colLetter}" . ($totalRow - 1) . ")");
            $sheet->getStyle("{$colLetter}{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E3A8A'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A8A']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '1E3A8A']],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Auto Size Columns with Comfort Padding.
     */
    private function autoSizeColumns($sheet, string $lastColumn): void
    {
        $lastColIdx = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $lastColIdx; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }

    /**
     * Build Subtitle Meta Text.
     */
    private function buildSubtitleText(array $meta, string $reportName): string
    {
        $parts = ["Laporan: {$reportName}"];

        if (!empty($meta['branch_name'])) {
            $parts[] = "Cabang: {$meta['branch_name']}";
        } else {
            $parts[] = "Cabang: Semua Cabang";
        }

        if (!empty($meta['tanggal'])) {
            $parts[] = "Tanggal: {$meta['tanggal']}";
        } elseif (!empty($meta['tanggal_awal']) && !empty($meta['tanggal_akhir'])) {
            $parts[] = "Periode: {$meta['tanggal_awal']} s.d. {$meta['tanggal_akhir']}";
        } elseif (!empty($meta['bulan']) && !empty($meta['tahun'])) {
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $mName = $monthNames[(int)$meta['bulan']] ?? $meta['bulan'];
            $parts[] = "Periode: {$mName} {$meta['tahun']}";
        }

        $parts[] = "Dicetak Pada: " . date('d M Y H:i');

        return implode('  |  ', $parts);
    }
}
