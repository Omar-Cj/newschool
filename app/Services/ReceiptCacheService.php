<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Receipt caching service for performance optimization
 * Implements strategic caching for frequently accessed receipt data
 */
class ReceiptCacheService
{
    private const CACHE_PREFIX = 'receipt_cache_';
    private const STATS_CACHE_TTL = 3600; // 1 hour
    private const RECEIPT_CACHE_TTL = 1800; // 30 minutes
    private const STUDENT_CACHE_TTL = 900; // 15 minutes

    /**
     * Get cached receipt statistics for dashboard
     */
    public function getReceiptStats(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'stats_' . date('Y-m-d');

        return Cache::remember($cacheKey, self::STATS_CACHE_TTL, function () {
            $currentYear = date('Y');
            $currentMonth = date('m');

            return [
                'today' => $this->getTodayStats(),
                'this_month' => $this->getMonthStats($currentYear, $currentMonth),
                'this_year' => $this->getYearStats($currentYear),
                'payment_methods' => $this->getPaymentMethodStats(),
                'top_collectors' => $this->getTopCollectors(),
                'performance_metrics' => $this->getPerformanceMetrics(),
            ];
        });
    }

    /**
     * Get cached student payment summary
     */
    public function getStudentPaymentSummary(int $studentId, string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $endDate ?? Carbon::now()->format('Y-m-d');
        $cacheKey = self::CACHE_PREFIX . "student_{$studentId}_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, self::STUDENT_CACHE_TTL, function () use ($studentId, $startDate, $endDate) {
            // Get PaymentTransaction records
            $paymentTransactions = DB::table('payment_transactions as pt')
                ->leftJoin('users as u', 'pt.collected_by', '=', 'u.id')
                ->leftJoin('students as s', 'pt.student_id', '=', 's.id')
                ->select([
                    'pt.id',
                    'pt.receipt_number',
                    'pt.payment_date',
                    'pt.amount',
                    'pt.payment_method',
                    'pt.transaction_reference',
                    'pt.payment_notes',
                    'u.name as collector_name',
                    's.first_name',
                    's.last_name',
                ])
                ->where('pt.student_id', $studentId)
                ->whereBetween('pt.payment_date', [$startDate, $endDate])
                ->orderBy('pt.payment_date', 'desc')
                ->get();

            // Get FeesCollect records (legacy)
            $feesCollects = DB::table('fees_collects as fc')
                ->leftJoin('users as u', 'fc.fees_collect_by', '=', 'u.id')
                ->leftJoin('students as s', 'fc.student_id', '=', 's.id')
                ->select([
                    'fc.id',
                    'fc.receipt_number',
                    'fc.date as payment_date',
                    'fc.amount',
                    'fc.payment_method',
                    'fc.transaction_reference',
                    'fc.payment_notes',
                    'u.name as collector_name',
                    's.first_name',
                    's.last_name',
                ])
                ->where('fc.student_id', $studentId)
                ->whereNotNull('fc.payment_method')
                ->whereBetween('fc.date', [$startDate, $endDate])
                ->orderBy('fc.date', 'desc')
                ->get();

            // Combine and calculate totals
            $allPayments = $paymentTransactions->merge($feesCollects)->sortByDesc('payment_date');
            $totalAmount = $allPayments->sum('amount');
            $paymentCount = $allPayments->count();

            return [
                'student_id' => $studentId,
                'period' => ['start' => $startDate, 'end' => $endDate],
                'payments' => $allPayments->values()->toArray(),
                'summary' => [
                    'total_amount' => $totalAmount,
                    'payment_count' => $paymentCount,
                    'average_payment' => $paymentCount > 0 ? $totalAmount / $paymentCount : 0,
                    'first_payment_date' => $allPayments->last()->payment_date ?? null,
                    'last_payment_date' => $allPayments->first()->payment_date ?? null,
                ],
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get cached receipt data for fast rendering
     */
    public function getCachedReceiptData(string $receiptNumber): ?array
    {
        $cacheKey = self::CACHE_PREFIX . 'receipt_' . $receiptNumber;

        return Cache::get($cacheKey);
    }

    /**
     * Cache receipt data for fast rendering
     */
    public function cacheReceiptData(string $receiptNumber, array $receiptData): void
    {
        $cacheKey = self::CACHE_PREFIX . 'receipt_' . $receiptNumber;
        Cache::put($cacheKey, $receiptData, self::RECEIPT_CACHE_TTL);
    }

    /**
     * Invalidate student-related cache when payments change
     */
    public function invalidateStudentCache(int $studentId): void
    {
        $pattern = self::CACHE_PREFIX . "student_{$studentId}_*";
        $this->clearCacheByPattern($pattern);

        Log::debug("Invalidated cache for student {$studentId}");
    }

    /**
     * Invalidate receipt cache when receipt data changes
     */
    public function invalidateReceiptCache(string $receiptNumber): void
    {
        $cacheKey = self::CACHE_PREFIX . 'receipt_' . $receiptNumber;
        Cache::forget($cacheKey);

        Log::debug("Invalidated cache for receipt {$receiptNumber}");
    }

    /**
     * Invalidate statistics cache (call after significant data changes)
     */
    public function invalidateStatsCache(): void
    {
        $statsKey = self::CACHE_PREFIX . 'stats_' . date('Y-m-d');
        Cache::forget($statsKey);

        // Also clear previous days if they exist
        for ($i = 1; $i <= 7; $i++) {
            $previousDay = self::CACHE_PREFIX . 'stats_' . Carbon::now()->subDays($i)->format('Y-m-d');
            Cache::forget($previousDay);
        }

        Log::debug("Invalidated statistics cache");
    }

    /**
     * Get today's payment statistics
     */
    private function getTodayStats(): array
    {
        $today = date('Y-m-d');

        $ptStats = DB::table('payment_transactions')
            ->whereDate('payment_date', $today)
            ->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount
            ')
            ->first();

        $fcStats = DB::table('fees_collects')
            ->whereDate('date', $today)
            ->whereNotNull('payment_method')
            ->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount
            ')
            ->first();

        return [
            'date' => $today,
            'total_receipts' => ($ptStats->count ?? 0) + ($fcStats->count ?? 0),
            'total_amount' => ($ptStats->total_amount ?? 0) + ($fcStats->total_amount ?? 0),
            'average_amount' => (($ptStats->total_amount ?? 0) + ($fcStats->total_amount ?? 0)) / (($ptStats->count ?? 0) + ($fcStats->count ?? 0) ?: 1),
            'payment_transactions' => $ptStats->count ?? 0,
            'legacy_payments' => $fcStats->count ?? 0,
        ];
    }

    /**
     * Get monthly payment statistics
     */
    private function getMonthStats(string $year, string $month): array
    {
        $startOfMonth = Carbon::create($year, $month)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month)->endOfMonth();

        $ptStats = DB::table('payment_transactions')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total_amount,
                COUNT(DISTINCT student_id) as unique_students
            ')
            ->first();

        $fcStats = DB::table('fees_collects')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('payment_method')
            ->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total_amount,
                COUNT(DISTINCT student_id) as unique_students
            ')
            ->first();

        return [
            'period' => $startOfMonth->format('F Y'),
            'total_receipts' => ($ptStats->count ?? 0) + ($fcStats->count ?? 0),
            'total_amount' => ($ptStats->total_amount ?? 0) + ($fcStats->total_amount ?? 0),
            'unique_students' => max($ptStats->unique_students ?? 0, $fcStats->unique_students ?? 0),
            'payment_transactions' => $ptStats->count ?? 0,
            'legacy_payments' => $fcStats->count ?? 0,
        ];
    }

    /**
     * Get yearly payment statistics
     */
    private function getYearStats(string $year): array
    {
        $startOfYear = Carbon::create($year)->startOfYear();
        $endOfYear = Carbon::create($year)->endOfYear();

        $ptStats = DB::table('payment_transactions')
            ->whereBetween('payment_date', [$startOfYear, $endOfYear])
            ->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total_amount,
                COUNT(DISTINCT student_id) as unique_students,
                COUNT(DISTINCT collected_by) as unique_collectors
            ')
            ->first();

        $fcStats = DB::table('fees_collects')
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->whereNotNull('payment_method')
            ->selectRaw('
                COUNT(*) as count,
                SUM(amount) as total_amount,
                COUNT(DISTINCT student_id) as unique_students,
                COUNT(DISTINCT fees_collect_by) as unique_collectors
            ')
            ->first();

        return [
            'year' => $year,
            'total_receipts' => ($ptStats->count ?? 0) + ($fcStats->count ?? 0),
            'total_amount' => ($ptStats->total_amount ?? 0) + ($fcStats->total_amount ?? 0),
            'unique_students' => max($ptStats->unique_students ?? 0, $fcStats->unique_students ?? 0),
            'unique_collectors' => max($ptStats->unique_collectors ?? 0, $fcStats->unique_collectors ?? 0),
            'payment_transactions' => $ptStats->count ?? 0,
            'legacy_payments' => $fcStats->count ?? 0,
        ];
    }

    /**
     * Get payment method distribution statistics
     */
    private function getPaymentMethodStats(): array
    {
        $paymentMethods = config('site.payment_methods', [
            1 => 'Cash',
            2 => 'Stripe',
            3 => 'Zaad',
            4 => 'Edahab',
            5 => 'PayPal'
        ]);

        $ptStats = DB::table('payment_transactions')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $fcStats = DB::table('fees_collects')
            ->whereNotNull('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $stats = [];
        foreach ($paymentMethods as $methodId => $methodName) {
            $ptCount = $ptStats->get($methodId)->count ?? 0;
            $ptAmount = $ptStats->get($methodId)->total_amount ?? 0;
            $fcCount = $fcStats->get($methodId)->count ?? 0;
            $fcAmount = $fcStats->get($methodId)->total_amount ?? 0;

            $stats[$methodName] = [
                'method_id' => $methodId,
                'total_count' => $ptCount + $fcCount,
                'total_amount' => $ptAmount + $fcAmount,
                'payment_transactions' => $ptCount,
                'legacy_payments' => $fcCount,
            ];
        }

        return $stats;
    }

    /**
     * Get top collectors statistics
     */
    private function getTopCollectors(int $limit = 10): array
    {
        $ptCollectors = DB::table('payment_transactions as pt')
            ->join('users as u', 'pt.collected_by', '=', 'u.id')
            ->selectRaw('u.id, u.name, COUNT(*) as count, SUM(pt.amount) as total_amount')
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get()
            ->keyBy('id');

        $fcCollectors = DB::table('fees_collects as fc')
            ->join('users as u', 'fc.fees_collect_by', '=', 'u.id')
            ->whereNotNull('fc.payment_method')
            ->selectRaw('u.id, u.name, COUNT(*) as count, SUM(fc.amount) as total_amount')
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get()
            ->keyBy('id');

        // Merge collector stats
        $allCollectorIds = $ptCollectors->keys()->merge($fcCollectors->keys())->unique();
        $collectors = [];

        foreach ($allCollectorIds as $collectorId) {
            $ptData = $ptCollectors->get($collectorId, (object)['count' => 0, 'total_amount' => 0, 'name' => '']);
            $fcData = $fcCollectors->get($collectorId, (object)['count' => 0, 'total_amount' => 0, 'name' => '']);

            $collectors[] = [
                'id' => $collectorId,
                'name' => $ptData->name ?: $fcData->name,
                'total_count' => $ptData->count + $fcData->count,
                'total_amount' => $ptData->total_amount + $fcData->total_amount,
                'payment_transactions' => $ptData->count,
                'legacy_payments' => $fcData->count,
            ];
        }

        // Sort by total amount and return top performers
        return collect($collectors)
            ->sortByDesc('total_amount')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'cache_hit_rate' => 0, // This would be implemented with actual cache metrics
            'average_response_time' => 0, // This would be implemented with performance monitoring
            'database_query_count' => 0, // This would be implemented with query monitoring
            'last_cache_clear' => Cache::get(self::CACHE_PREFIX . 'last_clear', null),
        ];
    }

    /**
     * Clear cache by pattern (requires Redis or similar cache driver)
     */
    private function clearCacheByPattern(string $pattern): void
    {
        // This is a simplified implementation
        // In production, you'd use cache tags or implement pattern matching for your cache driver
        $keys = Cache::getStore()->getRedis()->keys($pattern);
        foreach ($keys as $key) {
            Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
    }

    /**
     * Warm up frequently accessed caches
     */
    public function warmUpCache(): array
    {
        $startTime = microtime(true);
        $warmedItems = 0;

        try {
            // Warm up statistics cache
            $this->getReceiptStats();
            $warmedItems++;

            // Warm up recent student caches (top 50 most active students)
            $activeStudents = DB::table('payment_transactions')
                ->select('student_id')
                ->where('payment_date', '>=', Carbon::now()->subDays(30))
                ->groupBy('student_id')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(50)
                ->pluck('student_id');

            foreach ($activeStudents as $studentId) {
                $this->getStudentPaymentSummary($studentId);
                $warmedItems++;
            }

            $executionTime = microtime(true) - $startTime;

            Log::info("Cache warm-up completed", [
                'items_warmed' => $warmedItems,
                'execution_time' => round($executionTime, 2) . 's'
            ]);

            return [
                'success' => true,
                'items_warmed' => $warmedItems,
                'execution_time' => $executionTime,
            ];

        } catch (\Exception $e) {
            Log::error("Cache warm-up failed", [
                'error' => $e->getMessage(),
                'items_warmed' => $warmedItems,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'items_warmed' => $warmedItems,
            ];
        }
    }

    /**
     * Clear all receipt-related caches
     */
    public function clearAllCaches(): void
    {
        $this->clearCacheByPattern(self::CACHE_PREFIX . '*');
        Cache::put(self::CACHE_PREFIX . 'last_clear', now()->toISOString(), 86400);

        Log::info("All receipt caches cleared");
    }
}