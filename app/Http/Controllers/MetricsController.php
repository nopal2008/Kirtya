<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Book;
use App\Models\BookStock;
use App\Models\Fine;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    /**
     * Expose comprehensive Prometheus OpenMetrics format.
     */
    public function metrics(Request $request)
    {
        // Optional security token check
        $configuredToken = config('app.prometheus_token', env('PROMETHEUS_METRICS_TOKEN'));
        if (!empty($configuredToken)) {
            $providedToken = $request->query('token') ?? $request->header('X-Metrics-Token') ?? $request->bearerToken();
            if ($providedToken !== $configuredToken) {
                return response('Unauthorized Prometheus Scraping Request', 401, ['Content-Type' => 'text/plain']);
            }
        }

        $today = now()->toDateString();

        // 1. Gather application statistics
        $booksCount           = Book::count();
        $bookStocksCount      = BookStock::count();
        $stocksAvailableCount = BookStock::where('status', 'available')->count();
        $stocksBorrowedCount  = BookStock::where('status', 'borrowed')->count();
        $categoriesCount      = Book::distinct('category')->whereNotNull('category')->count();

        $usersCount           = User::count();
        $visitorsTodayCount   = VisitorLog::whereDate('check_in_at', $today)->count();

        $activeBorrowsCount   = Transaction::whereIn('status', ['borrowed', 'overdue'])->count();
        $overdueCount         = Transaction::where('status', 'overdue')
                                   ->orWhere(function ($q) use ($today) {
                                       $q->where('status', 'borrowed')->where('due_date', '<', $today);
                                   })->count();

        $returnedTodayCount   = Transaction::whereDate('return_date', $today)->count();
        $createdTodayCount    = Transaction::whereDate('borrow_date', $today)->count();

        $unpaidFinesTotal     = (float) Fine::where('payment_status', 'unpaid')->sum('amount');
        $paidFinesTotal       = (float) Fine::where('payment_status', 'paid')->sum('amount');
        $paidFinesTodayTotal  = (float) Fine::where('payment_status', 'paid')->whereDate('paid_at', $today)->sum('amount');

        $auditLogsTodayCount  = AuditLog::whereDate('created_at', $today)->count();

        // User Auth History (Logins & Logouts)
        $loginsTotal          = AuditLog::where('action', 'login')->count();
        $logoutsTotal         = AuditLog::where('action', 'logout')->count();
        $loginsToday          = AuditLog::where('action', 'login')->whereDate('created_at', $today)->count();
        $logoutsToday         = AuditLog::where('action', 'logout')->whereDate('created_at', $today)->count();

        // System resources
        $memoryUsage = memory_get_usage(true);
        $memoryPeak  = memory_get_peak_usage(true);
        $basePath    = base_path();
        $diskFree    = @disk_free_space($basePath) ?: 0;
        $diskTotal   = @disk_total_space($basePath) ?: 0;

        // 2. Format OpenMetrics Plain Text Output
        $lines = [];

        // --- SECTION: System & Health ---
        $lines[] = '# HELP siperpus_up Metric indicating application health (1 = healthy).';
        $lines[] = '# TYPE siperpus_up gauge';
        $lines[] = 'siperpus_up 1';

        $lines[] = '# HELP siperpus_info Static info about environment.';
        $lines[] = '# TYPE siperpus_info gauge';
        $phpVer = PHP_VERSION;
        $laravelVer = app()->version();
        $lines[] = "siperpus_info{php_version=\"{$phpVer}\",laravel_version=\"{$laravelVer}\"} 1";

        $lines[] = '# HELP php_memory_usage_bytes Current PHP memory usage in bytes.';
        $lines[] = '# TYPE php_memory_usage_bytes gauge';
        $lines[] = "php_memory_usage_bytes {$memoryUsage}";

        $lines[] = '# HELP php_memory_peak_bytes Peak PHP memory usage in bytes.';
        $lines[] = '# TYPE php_memory_peak_bytes gauge';
        $lines[] = "php_memory_peak_bytes {$memoryPeak}";

        $lines[] = '# HELP system_storage_free_bytes Free storage space on system drive in bytes.';
        $lines[] = '# TYPE system_storage_free_bytes gauge';
        $lines[] = "system_storage_free_bytes {$diskFree}";

        $lines[] = '# HELP system_storage_total_bytes Total storage capacity on system drive in bytes.';
        $lines[] = '# TYPE system_storage_total_bytes gauge';
        $lines[] = "system_storage_total_bytes {$diskTotal}";

        // --- SECTION: Catalog & Stocks ---
        $lines[] = '# HELP siperpus_books_total Total number of book titles in catalog.';
        $lines[] = '# TYPE siperpus_books_total gauge';
        $lines[] = "siperpus_books_total {$booksCount}";

        $lines[] = '# HELP siperpus_book_categories_total Total distinct book categories.';
        $lines[] = '# TYPE siperpus_book_categories_total gauge';
        $lines[] = "siperpus_book_categories_total {$categoriesCount}";

        $lines[] = '# HELP siperpus_book_stocks_total Total physical book stock exemplars.';
        $lines[] = '# TYPE siperpus_book_stocks_total gauge';
        $lines[] = "siperpus_book_stocks_total {$bookStocksCount}";

        $lines[] = '# HELP siperpus_book_stocks_available Physical book stock exemplars available for borrowing.';
        $lines[] = '# TYPE siperpus_book_stocks_available gauge';
        $lines[] = "siperpus_book_stocks_available {$stocksAvailableCount}";

        $lines[] = '# HELP siperpus_book_stocks_borrowed Physical book stock exemplars currently borrowed.';
        $lines[] = '# TYPE siperpus_book_stocks_borrowed gauge';
        $lines[] = "siperpus_book_stocks_borrowed {$stocksBorrowedCount}";

        // --- SECTION: Users & Visitors ---
        $lines[] = '# HELP siperpus_users_total Total registered users in system.';
        $lines[] = '# TYPE siperpus_users_total gauge';
        $lines[] = "siperpus_users_total {$usersCount}";

        $lines[] = '# HELP siperpus_visitors_today_total Total library visitors checked in today.';
        $lines[] = '# TYPE siperpus_visitors_today_total gauge';
        $lines[] = "siperpus_visitors_today_total {$visitorsTodayCount}";

        // --- SECTION: User Auth & Login History ---
        $lines[] = '# HELP siperpus_logins_total Total historical user logins count (all time).';
        $lines[] = '# TYPE siperpus_logins_total counter';
        $lines[] = "siperpus_logins_total {$loginsTotal}";

        $lines[] = '# HELP siperpus_logouts_total Total historical user logouts count (all time).';
        $lines[] = '# TYPE siperpus_logouts_total counter';
        $lines[] = "siperpus_logouts_total {$logoutsTotal}";

        $lines[] = '# HELP siperpus_logins_today_total Total user logins recorded today.';
        $lines[] = '# TYPE siperpus_logins_today_total gauge';
        $lines[] = "siperpus_logins_today_total {$loginsToday}";

        $lines[] = '# HELP siperpus_logouts_today_total Total user logouts recorded today.';
        $lines[] = '# TYPE siperpus_logouts_today_total gauge';
        $lines[] = "siperpus_logouts_today_total {$logoutsToday}";

        // --- SECTION: Circulation & Transactions ---
        $lines[] = '# HELP siperpus_transactions_active_total Currently active book borrowings.';
        $lines[] = '# TYPE siperpus_transactions_active_total gauge';
        $lines[] = "siperpus_transactions_active_total {$activeBorrowsCount}";

        $lines[] = '# HELP siperpus_transactions_overdue_total Total overdue book transactions.';
        $lines[] = '# TYPE siperpus_transactions_overdue_total gauge';
        $lines[] = "siperpus_transactions_overdue_total {$overdueCount}";

        $lines[] = '# HELP siperpus_transactions_created_today_total Total new book borrowings created today.';
        $lines[] = '# TYPE siperpus_transactions_created_today_total gauge';
        $lines[] = "siperpus_transactions_created_today_total {$createdTodayCount}";

        $lines[] = '# HELP siperpus_transactions_returned_today_total Total book returns processed today.';
        $lines[] = '# TYPE siperpus_transactions_returned_today_total gauge';
        $lines[] = "siperpus_transactions_returned_today_total {$returnedTodayCount}";

        // --- SECTION: Fines & Security ---
        $lines[] = '# HELP siperpus_fines_unpaid_total_rupiah Total accumulated unpaid fine amount in IDR.';
        $lines[] = '# TYPE siperpus_fines_unpaid_total_rupiah gauge';
        $lines[] = "siperpus_fines_unpaid_total_rupiah {$unpaidFinesTotal}";

        $lines[] = '# HELP siperpus_fines_paid_total_rupiah Total accumulated collected fine payments in IDR.';
        $lines[] = '# TYPE siperpus_fines_paid_total_rupiah counter';
        $lines[] = "siperpus_fines_paid_total_rupiah {$paidFinesTotal}";

        $lines[] = '# HELP siperpus_fines_paid_today_rupiah Fine payments collected today in IDR.';
        $lines[] = '# TYPE siperpus_fines_paid_today_rupiah gauge';
        $lines[] = "siperpus_fines_paid_today_rupiah {$paidFinesTodayTotal}";

        $lines[] = '# HELP siperpus_audit_logs_today_total Total security audit log events recorded today.';
        $lines[] = '# TYPE siperpus_audit_logs_today_total gauge';
        $lines[] = "siperpus_audit_logs_today_total {$auditLogsTodayCount}";

        $content = implode("\n", $lines) . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
