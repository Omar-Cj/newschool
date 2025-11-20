<?php

declare(strict_types=1);

namespace Modules\MainApp\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Dashboard Metrics Service
 *
 * Provides centralized access to dashboard metrics with caching strategy.
 * All data fetched via stored procedures for optimal performance.
 */
class DashboardMetricsService
{
    /**
     * Default cache TTL in seconds (5 minutes)
     */
    private const DEFAULT_CACHE_TTL = 300;

    /**
     * Cache key prefix for metrics
     */
    private const CACHE_PREFIX = 'dashboard_metrics';

    /**
     * Get dashboard metric cards data
     *
     * Returns key performance indicators for dashboard header cards
     *
     * @param string|null $dateFrom Start date (defaults to start of month)
     * @param string|null $dateTo End date (defaults to today)
     * @return array Metrics data array
     */
    public function getMetricCards(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $dateFrom = $dateFrom ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = $dateTo ?? Carbon::now()->format('Y-m-d');

        $cacheKey = $this->buildCacheKey('metrics', [$dateFrom, $dateTo]);

        return $this->cacheMetrics($cacheKey, function () use ($dateFrom, $dateTo) {
            try {
                $result = DB::select('CALL sp_get_dashboard_metrics(?, ?)', [$dateFrom, $dateTo]);

                if (empty($result)) {
                    return $this->getDefaultMetrics();
                }

                $metrics = $result[0];

                // Calculate trends (compare with previous period)
                $previousPeriod = $this->getPreviousPeriodMetrics($dateFrom, $dateTo);

                return [
                    'total_revenue' => [
                        'value' => (float) $metrics->total_revenue,
                        'label' => 'Total Revenue',
                        'trend' => $this->calculateTrend(
                            (float) $metrics->total_revenue,
                            (float) ($previousPeriod['total_revenue'] ?? 0)
                        ),
                        'icon' => 'fas fa-dollar-sign',
                        'color' => 'success',
                    ],
                    'active_subscriptions' => [
                        'value' => (int) $metrics->active_subscriptions,
                        'label' => 'Active Subscriptions',
                        'trend' => $this->calculateTrend(
                            (int) $metrics->active_subscriptions,
                            (int) ($previousPeriod['active_subscriptions'] ?? 0)
                        ),
                        'icon' => 'fas fa-school',
                        'color' => 'primary',
                    ],
                    'outstanding_payments' => [
                        'value' => (float) $metrics->outstanding_payments,
                        'count' => (int) $metrics->pending_payments,
                        'label' => 'Outstanding Payments',
                        'icon' => 'fas fa-exclamation-triangle',
                        'color' => 'warning',
                    ],
                    'new_schools' => [
                        'value' => (int) $metrics->new_schools_last_30_days,
                        'weekly' => (int) $metrics->new_schools_last_7_days,
                        'label' => 'New Schools',
                        'trend' => $this->calculateTrend(
                            (int) $metrics->new_schools_last_30_days,
                            (int) ($previousPeriod['new_schools_last_30_days'] ?? 0)
                        ),
                        'icon' => 'fas fa-chart-line',
                        'color' => 'info',
                    ],
                    'mrr' => (float) $metrics->mrr,
                    'arr' => (float) $metrics->arr,
                    'total_schools' => (int) $metrics->total_schools,
                ];
            } catch (\Exception $e) {
                Log::error('Error fetching dashboard metrics', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return $this->getDefaultMetrics();
            }
        }, self::DEFAULT_CACHE_TTL);
    }

    /**
     * Get revenue chart data
     *
     * @param string $period 'monthly' or 'yearly'
     * @param int|null $year Year filter (defaults to current year)
     * @return array Chart data structure
     */
    public function getRevenueChart(string $period = 'monthly', ?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $cacheKey = $this->buildCacheKey('revenue_chart', [$period, $year]);

        return $this->cacheMetrics($cacheKey, function () use ($period, $year) {
            try {
                $result = DB::select('CALL sp_get_revenue_trends(?, ?)', [$period, $year]);

                $labels = [];
                $subscriptionRevenue = [];
                $actualPayments = [];
                $outstandingAmount = [];

                foreach ($result as $row) {
                    $labels[] = $row->period_label;
                    $subscriptionRevenue[] = (float) $row->subscription_revenue;
                    $actualPayments[] = (float) $row->actual_payments;
                    $outstandingAmount[] = (float) $row->outstanding_amount;
                }

                return [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Actual Payments',
                            'data' => $actualPayments,
                            'borderColor' => '#5669FF',
                            'backgroundColor' => 'rgba(86, 105, 255, 0.1)',
                            'fill' => true,
                            'tension' => 0.4,
                        ],
                        [
                            'label' => 'Subscription Revenue',
                            'data' => $subscriptionRevenue,
                            'borderColor' => '#00C48C',
                            'backgroundColor' => 'rgba(0, 196, 140, 0.1)',
                            'fill' => true,
                            'tension' => 0.4,
                        ],
                        [
                            'label' => 'Outstanding',
                            'data' => $outstandingAmount,
                            'borderColor' => '#FF6B6B',
                            'backgroundColor' => 'rgba(255, 107, 107, 0.1)',
                            'fill' => true,
                            'tension' => 0.4,
                        ],
                    ],
                ];
            } catch (\Exception $e) {
                Log::error('Error fetching revenue chart data', [
                    'error' => $e->getMessage(),
                    'period' => $period,
                    'year' => $year
                ]);
                return $this->getDefaultChartData();
            }
        }, self::DEFAULT_CACHE_TTL);
    }

    /**
     * Get package distribution data for pie/donut chart
     *
     * @return array Chart data structure
     */
    public function getPackageDistribution(): array
    {
        $cacheKey = $this->buildCacheKey('package_distribution');

        return $this->cacheMetrics($cacheKey, function () {
            try {
                $result = DB::select('CALL sp_get_package_distribution()');

                $labels = [];
                $data = [];
                $colors = ['#5669FF', '#00C48C', '#FF6B6B', '#FFA94D', '#9775FA', '#748FFC'];

                foreach ($result as $index => $row) {
                    $labels[] = $row->package_name;
                    $data[] = (int) $row->school_count;
                }

                return [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'data' => $data,
                            'backgroundColor' => array_slice($colors, 0, count($data)),
                            'borderWidth' => 2,
                            'borderColor' => '#fff',
                        ],
                    ],
                    'details' => array_map(function ($row) {
                        return [
                            'package_name' => $row->package_name,
                            'school_count' => (int) $row->school_count,
                            'total_revenue' => (float) $row->total_revenue,
                            'market_share' => (float) $row->market_share_percentage,
                        ];
                    }, $result),
                ];
            } catch (\Exception $e) {
                Log::error('Error fetching package distribution', [
                    'error' => $e->getMessage()
                ]);
                return $this->getDefaultChartData();
            }
        }, 600); // 10 minutes cache for package distribution
    }

    /**
     * Get school growth trends
     *
     * @param string|null $dateFrom Start date
     * @param string|null $dateTo End date
     * @return array Growth data
     */
    public function getSchoolGrowth(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $dateFrom = $dateFrom ?? Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
        $dateTo = $dateTo ?? Carbon::now()->format('Y-m-d');

        $cacheKey = $this->buildCacheKey('school_growth', [$dateFrom, $dateTo]);

        return $this->cacheMetrics($cacheKey, function () use ($dateFrom, $dateTo) {
            try {
                $result = DB::select('CALL sp_get_school_growth_report(?, ?)', [$dateFrom, $dateTo]);

                return array_map(function ($row) {
                    return [
                        'period' => $row->period_label,
                        'new_schools' => (int) $row->new_schools,
                        'growth_percentage' => (float) $row->growth_percentage,
                        'cumulative_schools' => (int) $row->cumulative_schools,
                    ];
                }, $result);
            } catch (\Exception $e) {
                Log::error('Error fetching school growth data', [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }, self::DEFAULT_CACHE_TTL);
    }

    /**
     * Get recent payments
     *
     * @param int $limit Number of recent payments to retrieve
     * @return array Payment records
     */
    public function getRecentPayments(int $limit = 10): array
    {
        $cacheKey = $this->buildCacheKey('recent_payments', [$limit]);

        return $this->cacheMetrics($cacheKey, function () use ($limit) {
            try {
                $dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
                $dateTo = Carbon::now()->format('Y-m-d');

                $result = DB::select(
                    'CALL sp_get_payment_collection_report(?, ?, ?)',
                    [$dateFrom, $dateTo, null]
                );

                return array_slice(
                    array_map(function ($row) {
                        return [
                            'id' => $row->id,
                            'school_name' => $row->school_name,
                            'amount' => (float) $row->amount,
                            'payment_date' => $row->payment_date,
                            'status' => $row->status,
                            'status_code' => $row->status_code,
                            'payment_method' => $row->payment_method,
                            'invoice_number' => $row->invoice_number,
                        ];
                    }, $result),
                    0,
                    $limit
                );
            } catch (\Exception $e) {
                Log::error('Error fetching recent payments', [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }, 120); // 2 minutes cache for recent payments
    }

    /**
     * Get schools near expiry
     *
     * @param int $daysThreshold Days threshold for expiry warning
     * @return array Schools near expiry
     */
    public function getSchoolsNearExpiry(int $daysThreshold = 30): array
    {
        $cacheKey = $this->buildCacheKey('schools_near_expiry', [$daysThreshold]);

        return $this->cacheMetrics($cacheKey, function () use ($daysThreshold) {
            try {
                $result = DB::select('CALL sp_get_outstanding_payments_report(?)', [0]);

                return array_map(function ($row) {
                    return [
                        'school_id' => $row->school_id,
                        'school_name' => $row->school_name,
                        'package_name' => $row->package_name,
                        'expiry_date' => $row->expiry_date,
                        'days_overdue' => (int) $row->days_overdue,
                        'urgency_level' => $row->urgency_level,
                        'outstanding_amount' => (float) $row->outstanding_amount,
                    ];
                }, $result);
            } catch (\Exception $e) {
                Log::error('Error fetching schools near expiry', [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }, 180); // 3 minutes cache
    }

    /**
     * Cache metrics with specified TTL
     *
     * @param string $key Cache key
     * @param callable $callback Data retrieval callback
     * @param int $ttl Time to live in seconds
     * @return mixed Cached or fresh data
     */
    public function cacheMetrics(string $key, callable $callback, int $ttl = self::DEFAULT_CACHE_TTL)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Cache operation failed, executing callback directly', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $callback();
        }
    }

    /**
     * Clear all dashboard metrics cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        $patterns = [
            'metrics',
            'revenue_chart',
            'package_distribution',
            'school_growth',
            'recent_payments',
            'schools_near_expiry',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($this->buildCacheKey($pattern));
        }
    }

    /**
     * Build cache key with prefix and parameters
     *
     * @param string $suffix Key suffix
     * @param array $params Additional parameters for key uniqueness
     * @return string Complete cache key
     */
    private function buildCacheKey(string $suffix, array $params = []): string
    {
        $keyParts = [self::CACHE_PREFIX, $suffix];

        if (!empty($params)) {
            $keyParts[] = md5(serialize($params));
        }

        return implode(':', $keyParts);
    }

    /**
     * Calculate percentage trend between current and previous values
     *
     * @param float $current Current period value
     * @param float $previous Previous period value
     * @return array Trend data
     */
    private function calculateTrend(float $current, float $previous): array
    {
        if ($previous == 0) {
            return [
                'percentage' => $current > 0 ? 100 : 0,
                'direction' => $current > 0 ? 'up' : 'neutral',
                'color' => $current > 0 ? 'success' : 'secondary',
            ];
        }

        $percentage = (($current - $previous) / $previous) * 100;
        $direction = $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral');
        $color = $percentage > 0 ? 'success' : ($percentage < 0 ? 'danger' : 'secondary');

        return [
            'percentage' => round(abs($percentage), 2),
            'direction' => $direction,
            'color' => $color,
        ];
    }

    /**
     * Get metrics for previous period (for trend calculation)
     *
     * @param string $dateFrom Current period start
     * @param string $dateTo Current period end
     * @return array Previous period metrics
     */
    private function getPreviousPeriodMetrics(string $dateFrom, string $dateTo): array
    {
        try {
            $from = Carbon::parse($dateFrom);
            $to = Carbon::parse($dateTo);
            $daysDiff = $from->diffInDays($to);

            $prevFrom = $from->copy()->subDays($daysDiff)->format('Y-m-d');
            $prevTo = $from->copy()->subDay()->format('Y-m-d');

            $result = DB::select('CALL sp_get_dashboard_metrics(?, ?)', [$prevFrom, $prevTo]);

            if (empty($result)) {
                return [];
            }

            $metrics = $result[0];
            return [
                'total_revenue' => (float) $metrics->total_revenue,
                'active_subscriptions' => (int) $metrics->active_subscriptions,
                'new_schools_last_30_days' => (int) $metrics->new_schools_last_30_days,
            ];
        } catch (\Exception $e) {
            Log::warning('Error fetching previous period metrics', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get default metrics structure when data is unavailable
     *
     * @return array Default metrics
     */
    private function getDefaultMetrics(): array
    {
        return [
            'total_revenue' => ['value' => 0, 'label' => 'Total Revenue', 'trend' => ['percentage' => 0, 'direction' => 'neutral', 'color' => 'secondary'], 'icon' => 'fas fa-dollar-sign', 'color' => 'success'],
            'active_subscriptions' => ['value' => 0, 'label' => 'Active Subscriptions', 'trend' => ['percentage' => 0, 'direction' => 'neutral', 'color' => 'secondary'], 'icon' => 'fas fa-school', 'color' => 'primary'],
            'outstanding_payments' => ['value' => 0, 'count' => 0, 'label' => 'Outstanding Payments', 'icon' => 'fas fa-exclamation-triangle', 'color' => 'warning'],
            'new_schools' => ['value' => 0, 'weekly' => 0, 'label' => 'New Schools', 'trend' => ['percentage' => 0, 'direction' => 'neutral', 'color' => 'secondary'], 'icon' => 'fas fa-chart-line', 'color' => 'info'],
            'mrr' => 0,
            'arr' => 0,
            'total_schools' => 0,
        ];
    }

    /**
     * Get default chart data structure
     *
     * @return array Default chart data
     */
    private function getDefaultChartData(): array
    {
        return [
            'labels' => [],
            'datasets' => [],
        ];
    }
}
