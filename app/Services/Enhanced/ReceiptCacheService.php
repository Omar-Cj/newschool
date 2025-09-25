<?php

namespace App\Services\Enhanced;

use App\Models\Fees\Receipt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

/**
 * Receipt Cache Service for Performance Optimization
 * Implements intelligent caching for high-volume receipt operations
 */
class ReceiptCacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'receipts:';

    /**
     * Cache receipt data for fast retrieval
     */
    public function cacheReceipt(Receipt $receipt): void
    {
        $cacheKey = $this->getReceiptCacheKey($receipt->receipt_number);
        $cacheData = $receipt->getComprehensiveReceiptData();

        Cache::put($cacheKey, $cacheData, self::CACHE_TTL);

        // Also cache in student-specific index
        $this->addToStudentReceiptIndex($receipt->student_id, $receipt->receipt_number);
    }

    /**
     * Get cached receipt data
     */
    public function getCachedReceipt(string $receiptNumber): ?array
    {
        $cacheKey = $this->getReceiptCacheKey($receiptNumber);
        return Cache::get($cacheKey);
    }

    /**
     * Cache frequently accessed receipt statistics
     */
    public function cacheReceiptStatistics(array $filters, array $statistics): void
    {
        $cacheKey = $this->getStatisticsCacheKey($filters);
        Cache::put($cacheKey, $statistics, self::CACHE_TTL / 2); // 30 minutes
    }

    /**
     * Get cached receipt statistics
     */
    public function getCachedStatistics(array $filters): ?array
    {
        $cacheKey = $this->getStatisticsCacheKey($filters);
        return Cache::get($cacheKey);
    }

    /**
     * Cache student receipt index for fast lookup
     */
    public function cacheStudentReceiptIndex(int $studentId, array $receiptNumbers): void
    {
        $cacheKey = $this->getStudentIndexCacheKey($studentId);
        Cache::put($cacheKey, $receiptNumbers, self::CACHE_TTL * 2); // 2 hours
    }

    /**
     * Add receipt to student index
     */
    public function addToStudentReceiptIndex(int $studentId, string $receiptNumber): void
    {
        $cacheKey = $this->getStudentIndexCacheKey($studentId);
        $currentIndex = Cache::get($cacheKey, []);

        if (!in_array($receiptNumber, $currentIndex)) {
            $currentIndex[] = $receiptNumber;
            Cache::put($cacheKey, $currentIndex, self::CACHE_TTL * 2);
        }
    }

    /**
     * Cache daily collection summaries for collectors
     */
    public function cacheDailyCollectionSummary(int $collectorId, Carbon $date, array $summary): void
    {
        $cacheKey = $this->getDailyCollectionCacheKey($collectorId, $date);
        Cache::put($cacheKey, $summary, self::CACHE_TTL * 4); // 4 hours for daily summaries
    }

    /**
     * Invalidate receipt-related caches
     */
    public function invalidateReceiptCache(string $receiptNumber): void
    {
        $cacheKey = $this->getReceiptCacheKey($receiptNumber);
        Cache::forget($cacheKey);

        // Invalidate related statistics cache
        $this->invalidateStatisticsCache();
    }

    /**
     * Invalidate all receipt statistics cache
     */
    public function invalidateStatisticsCache(): void
    {
        $pattern = self::CACHE_PREFIX . 'stats:*';
        $this->deleteByPattern($pattern);
    }

    /**
     * Warm up cache for frequently accessed receipts
     */
    public function warmUpCache(): void
    {
        // Cache recent receipts
        $recentReceipts = Receipt::with(['student', 'collector', 'allocations'])
            ->where('payment_date', '>=', now()->subDays(7))
            ->limit(100)
            ->get();

        foreach ($recentReceipts as $receipt) {
            $this->cacheReceipt($receipt);
        }

        // Cache daily statistics for current week
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i);
            $this->warmUpDailyStatistics($date);
        }
    }

    /**
     * Get cache usage statistics
     */
    public function getCacheStatistics(): array
    {
        $pattern = self::CACHE_PREFIX . '*';
        $keys = $this->getKeysByPattern($pattern);

        return [
            'total_cached_items' => count($keys),
            'cache_size_estimate' => count($keys) * 1024, // Rough estimate
            'cache_hit_rate' => $this->calculateHitRate(),
        ];
    }

    // Private helper methods

    private function getReceiptCacheKey(string $receiptNumber): string
    {
        return self::CACHE_PREFIX . 'receipt:' . $receiptNumber;
    }

    private function getStatisticsCacheKey(array $filters): string
    {
        $filterHash = md5(json_encode($filters));
        return self::CACHE_PREFIX . 'stats:' . $filterHash;
    }

    private function getStudentIndexCacheKey(int $studentId): string
    {
        return self::CACHE_PREFIX . 'student_index:' . $studentId;
    }

    private function getDailyCollectionCacheKey(int $collectorId, Carbon $date): string
    {
        return self::CACHE_PREFIX . 'daily_collection:' . $collectorId . ':' . $date->format('Y-m-d');
    }

    private function deleteByPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis') {
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            // For non-Redis cache drivers, we'd need a different approach
            // This is a simplified version - in production, consider using cache tags
            $keys = $this->getKeysByPattern($pattern);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    private function getKeysByPattern(string $pattern): array
    {
        if (config('cache.default') === 'redis') {
            return Redis::keys($pattern);
        }

        // For other cache drivers, this would need a different implementation
        return [];
    }

    private function calculateHitRate(): float
    {
        // This would need Redis info or custom tracking
        // Placeholder implementation
        return 0.85; // 85% hit rate estimate
    }

    private function warmUpDailyStatistics(Carbon $date): void
    {
        $receipts = Receipt::whereDate('payment_date', $date)->get();

        $statistics = [
            'total_receipts' => $receipts->count(),
            'total_amount' => $receipts->sum('total_amount'),
            'by_payment_method' => $receipts->groupBy('payment_method')->map->sum('total_amount'),
            'average_amount' => $receipts->avg('total_amount'),
        ];

        $filters = ['date' => $date->format('Y-m-d')];
        $this->cacheReceiptStatistics($filters, $statistics);
    }
}