<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\PersonalKpiController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\BranchPerformanceController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\LeaderboardController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Kpi\KpiManagementController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Report\DailyReportController;
use App\Http\Controllers\Report\MonthlyInsightController;
use App\Http\Controllers\Report\TiktokLiveReportController;
use App\Http\Controllers\Report\WeeklyReportController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Public Storage File Serving (Fix 403 Forbidden for uploaded screenshots)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated System Routes
Route::middleware(['auth'])->group(function () {

    // Executive Dashboard & Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/branch-performance', [BranchPerformanceController::class, 'index'])->name('branch.performance');

    // Daily, Weekly, TikTok Live & Monthly Report Engine
    Route::get('/reports/daily', [DailyReportController::class, 'index'])->name('reports.daily.index');
    Route::post('/reports/daily', [DailyReportController::class, 'store'])->name('reports.daily.store');
    Route::get('/reports/daily/{dailyReport}', [DailyReportController::class, 'show'])->name('reports.daily.show');

    // TikTok Live Reports
    Route::get('/reports/tiktok-live', [TiktokLiveReportController::class, 'index'])->name('reports.tiktok-live.index');
    Route::post('/reports/tiktok-live', [TiktokLiveReportController::class, 'store'])->name('reports.tiktok-live.store');
    Route::get('/reports/tiktok-live/{tiktokLive}', [TiktokLiveReportController::class, 'show'])->name('reports.tiktok-live.show');
    Route::delete('/reports/tiktok-live/{tiktokLive}', [TiktokLiveReportController::class, 'destroy'])->name('reports.tiktok-live.destroy');

    Route::get('/reports/weekly', [WeeklyReportController::class, 'index'])->name('reports.weekly.index');
    Route::post('/reports/weekly', [WeeklyReportController::class, 'store'])->name('reports.weekly.store');
    Route::get('/reports/weekly/{weeklyReport}', [WeeklyReportController::class, 'show'])->name('reports.weekly.show');
    Route::delete('/reports/weekly/{weeklyReport}', [WeeklyReportController::class, 'destroy'])->name('reports.weekly.destroy');

    Route::get('/reports/monthly', [MonthlyInsightController::class, 'index'])->name('reports.monthly.index');
    Route::post('/reports/monthly', [MonthlyInsightController::class, 'store'])->name('reports.monthly.store');

    // Super Admin Exclusive Routes
    Route::middleware(['role:Super Admin'])->group(function () {
        // Export Center & Export Features (Super Admin Only)
        Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');
        Route::get('/exports/pdf', [ExportController::class, 'exportPdf'])->name('exports.pdf');
        Route::get('/exports/excel', [ExportController::class, 'exportExcel'])->name('exports.excel');
        Route::get('/reports/tiktok-live/{tiktokLive}/pdf', [TiktokLiveReportController::class, 'exportSinglePdf'])->name('reports.tiktok-live.export-pdf');
        Route::get('/reports/tiktok-live/{tiktokLive}/jpg', [TiktokLiveReportController::class, 'exportSingleJpg'])->name('reports.tiktok-live.export-jpg');

        // Master Data CRUD
        Route::resource('master/branches', BranchController::class, ['names' => 'master.branches']);
        Route::resource('master/users', UserController::class, ['names' => 'master.users']);

        // KPI Management
        Route::get('/kpis', [KpiManagementController::class, 'index'])->name('kpis.index');
        Route::post('/kpis', [KpiManagementController::class, 'storeOrUpdate'])->name('kpis.store');
        Route::post('/kpis/copy', [KpiManagementController::class, 'copy'])->name('kpis.copy');

        // Kanban To-Do Board
        Route::get('/todo', [TodoController::class, 'index'])->name('todos.index');
        Route::post('/todo', [TodoController::class, 'store'])->name('todos.store');
        Route::put('/todo/{todo}/status', [TodoController::class, 'updateStatus'])->name('todos.updateStatus');
        Route::delete('/todo/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');

        // Personal KPI Tracking
        Route::get('/personal-kpis', [PersonalKpiController::class, 'index'])->name('personal-kpis.index');
        Route::post('/personal-kpis', [PersonalKpiController::class, 'store'])->name('personal-kpis.store');
        Route::put('/personal-kpis/{personalKpi}', [PersonalKpiController::class, 'update'])->name('personal-kpis.update');
        Route::delete('/personal-kpis/{personalKpi}', [PersonalKpiController::class, 'destroy'])->name('personal-kpis.destroy');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});
