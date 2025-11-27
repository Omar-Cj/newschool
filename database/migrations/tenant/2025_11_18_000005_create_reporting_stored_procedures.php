<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates comprehensive stored procedures for dashboard metrics and reporting.
     * All procedures optimize performance using indexes and cached calculations.
     *
     * @return void
     */
    public function up()
    {
        // Drop existing procedures if they exist
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_dashboard_metrics');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_payment_collection_report');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_school_growth_report');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_package_distribution');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_outstanding_payments_report');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_revenue_trends');

        /**
         * sp_get_dashboard_metrics
         *
         * Returns key dashboard metrics for super admin overview
         *
         * Parameters:
         * - date_from: Start date for metrics calculation
         * - date_to: End date for metrics calculation
         *
         * Returns: Single row with aggregated metrics
         */
        DB::unprepared("
            CREATE PROCEDURE sp_get_dashboard_metrics(
                IN date_from DATE,
                IN date_to DATE
            )
            BEGIN
                SELECT
                    -- School metrics
                    (SELECT COUNT(*) FROM schools) as total_schools,
                    (SELECT COUNT(*) FROM subscriptions WHERE status = 1) as active_subscriptions,

                    -- Revenue metrics
                    COALESCE((SELECT SUM(amount)
                              FROM subscription_payments
                              WHERE status = 1
                              AND payment_date BETWEEN date_from AND date_to), 0) as total_revenue,

                    COALESCE((SELECT SUM(amount)
                              FROM subscription_payments
                              WHERE status = 0
                              AND payment_date BETWEEN date_from AND date_to), 0) as pending_payments,

                    COALESCE((SELECT SUM(sp.amount)
                              FROM subscription_payments sp
                              INNER JOIN subscriptions s ON sp.subscription_id = s.id
                              WHERE sp.status = 0
                              AND s.expiry_date < CURRENT_DATE), 0) as outstanding_payments,

                    -- Branch and student metrics (from tenant databases would need separate query)
                    0 as total_students,
                    0 as total_branches,

                    -- Monthly Recurring Revenue (MRR) - subscriptions expiring next 30 days
                    COALESCE((SELECT SUM(s.price)
                              FROM subscriptions s
                              WHERE s.status = 1
                              AND s.expiry_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)), 0) as mrr,

                    -- Annual Recurring Revenue (ARR)
                    COALESCE((SELECT SUM(s.price)
                              FROM subscriptions s
                              WHERE s.status = 1
                              AND s.expiry_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 365 DAY)), 0) as arr,

                    -- Growth metrics
                    (SELECT COUNT(*) FROM schools WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)) as new_schools_last_30_days,
                    (SELECT COUNT(*) FROM schools WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)) as new_schools_last_7_days;
            END
        ");

        /**
         * sp_get_payment_collection_report
         *
         * Returns detailed payment collection report with filters
         *
         * Parameters:
         * - date_from: Start date filter
         * - date_to: End date filter
         * - school_id_filter: School filter (NULL for all schools)
         *
         * Returns: Multiple rows of payment records
         */
        DB::unprepared("
            CREATE PROCEDURE sp_get_payment_collection_report(
                IN date_from DATE,
                IN date_to DATE,
                IN school_id_filter INT
            )
            BEGIN
                SELECT
                    sp.id,
                    sch.name as school_name,
                    sch.email as school_email,
                    sch.phone as school_phone,
                    sch.sub_domain_key,
                    sp.payment_date,
                    sp.amount,
                    sp.payment_method,
                    CASE sp.status
                        WHEN 0 THEN 'Pending'
                        WHEN 1 THEN 'Approved'
                        WHEN 2 THEN 'Rejected'
                        ELSE 'Unknown'
                    END as status,
                    sp.status as status_code,
                    u.name as approver_name,
                    sp.approved_at,
                    sp.invoice_number,
                    sp.transaction_id,
                    sp.reference_number,
                    s.name as package_name,
                    s.expiry_date as subscription_expiry
                FROM subscription_payments sp
                INNER JOIN schools sch ON sp.school_id = sch.id
                LEFT JOIN subscriptions s ON sp.subscription_id = s.id
                LEFT JOIN users u ON sp.approved_by = u.id
                WHERE sp.payment_date BETWEEN date_from AND date_to
                AND (school_id_filter IS NULL OR sp.school_id = school_id_filter)
                ORDER BY sp.payment_date DESC, sp.created_at DESC;
            END
        ");

        /**
         * sp_get_school_growth_report
         *
         * Returns school growth trends by month
         *
         * Parameters:
         * - date_from: Start date for analysis
         * - date_to: End date for analysis
         *
         * Returns: Multiple rows grouped by month/year
         */
        DB::unprepared("
            CREATE PROCEDURE sp_get_school_growth_report(
                IN date_from DATE,
                IN date_to DATE
            )
            BEGIN
                WITH monthly_stats AS (
                    SELECT
                        YEAR(created_at) as year,
                        MONTH(created_at) as month,
                        DATE_FORMAT(created_at, '%Y-%m-01') as period_start,
                        COUNT(*) as new_schools
                    FROM schools
                    WHERE created_at BETWEEN date_from AND date_to
                    GROUP BY YEAR(created_at), MONTH(created_at)
                )
                SELECT
                    year,
                    month,
                    DATE_FORMAT(period_start, '%b %Y') as period_label,
                    new_schools,
                    -- Growth percentage vs previous month
                    CASE
                        WHEN LAG(new_schools) OVER (ORDER BY year, month) > 0 THEN
                            ROUND(((new_schools - LAG(new_schools) OVER (ORDER BY year, month)) /
                                   LAG(new_schools) OVER (ORDER BY year, month) * 100), 2)
                        ELSE 0
                    END as growth_percentage,
                    -- Cumulative total
                    SUM(new_schools) OVER (ORDER BY year, month) as cumulative_schools
                FROM monthly_stats
                ORDER BY year DESC, month DESC;
            END
        ");

        /**
         * sp_get_package_distribution
         *
         * Returns package distribution and revenue analysis
         *
         * Returns: Multiple rows, one per package
         */
        DB::unprepared("
            CREATE PROCEDURE sp_get_package_distribution()
            BEGIN
                SELECT
                    p.id as package_id,
                    p.name as package_name,
                    p.price as package_price,
                    COUNT(DISTINCT s.school_id) as school_count,
                    COUNT(DISTINCT s.id) as total_subscriptions,
                    COALESCE(SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END), 0) as active_subscriptions,
                    COALESCE(SUM(sp.amount), 0) as total_revenue,
                    COALESCE(AVG(sp.amount), 0) as avg_payment_amount,
                    -- Calculate percentage of total schools
                    ROUND((COUNT(DISTINCT s.school_id) * 100.0 /
                           (SELECT COUNT(*) FROM schools WHERE schools.id IS NOT NULL)), 2) as market_share_percentage
                FROM packages p
                LEFT JOIN subscriptions s ON p.id = s.package_id
                LEFT JOIN subscription_payments sp ON s.id = sp.subscription_id AND sp.status = 1
                GROUP BY p.id, p.name, p.price
                ORDER BY school_count DESC;
            END
        ");

        /**
         * sp_get_outstanding_payments_report
         *
         * Returns schools with outstanding payments and expired subscriptions
         *
         * Parameters:
         * - grace_period_exceeded: Filter for schools beyond grace period (1=yes, 0=all)
         *
         * Returns: Multiple rows of schools with payment issues
         */
        DB::unprepared("
            CREATE PROCEDURE sp_get_outstanding_payments_report(
                IN grace_period_exceeded TINYINT
            )
            BEGIN
                SELECT
                    sch.id as school_id,
                    sch.name as school_name,
                    sch.email as school_email,
                    sch.phone as school_phone,
                    sch.sub_domain_key,
                    p.name as package_name,
                    s.price as subscription_price,
                    s.expiry_date,
                    DATE_ADD(s.expiry_date, INTERVAL 15 DAY) as grace_expiry_date,
                    DATEDIFF(CURRENT_DATE, s.expiry_date) as days_overdue,
                    DATEDIFF(CURRENT_DATE, DATE_ADD(s.expiry_date, INTERVAL 15 DAY)) as days_beyond_grace,
                    CASE
                        WHEN CURRENT_DATE > DATE_ADD(s.expiry_date, INTERVAL 15 DAY) THEN 'Critical'
                        WHEN CURRENT_DATE > s.expiry_date THEN 'In Grace Period'
                        WHEN DATEDIFF(s.expiry_date, CURRENT_DATE) <= 7 THEN 'Expiring Soon'
                        ELSE 'Active'
                    END as urgency_level,
                    -- Outstanding amount (pending payments for this subscription)
                    COALESCE((SELECT SUM(amount)
                              FROM subscription_payments
                              WHERE subscription_id = s.id
                              AND status = 0), 0) as outstanding_amount,
                    -- Last payment date
                    (SELECT MAX(payment_date)
                     FROM subscription_payments
                     WHERE subscription_id = s.id
                     AND status = 1) as last_payment_date
                FROM schools sch
                INNER JOIN subscriptions s ON sch.id = s.school_id
                INNER JOIN packages p ON s.package_id = p.id
                WHERE s.status = 1
                AND (
                    (grace_period_exceeded = 1 AND CURRENT_DATE > DATE_ADD(s.expiry_date, INTERVAL 15 DAY))
                    OR
                    (grace_period_exceeded = 0 AND s.expiry_date < DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY))
                )
                ORDER BY days_overdue DESC, outstanding_amount DESC;
            END
        ");

        /**
         * sp_get_revenue_trends
         *
         * Returns revenue trends by period (monthly or yearly)
         *
         * Parameters:
         * - period: 'monthly' or 'yearly'
         * - year_filter: Year to filter (for monthly), or starting year (for yearly)
         *
         * Returns: Multiple rows of revenue data by period
         */
        DB::unprepared("
            CREATE PROCEDURE sp_get_revenue_trends(
                IN period VARCHAR(10),
                IN year_filter INT
            )
            BEGIN
                IF period = 'monthly' THEN
                    -- Monthly trends for specified year
                    WITH months AS (
                        SELECT 1 as month_num, 'Jan' as month_name UNION ALL
                        SELECT 2, 'Feb' UNION ALL
                        SELECT 3, 'Mar' UNION ALL
                        SELECT 4, 'Apr' UNION ALL
                        SELECT 5, 'May' UNION ALL
                        SELECT 6, 'Jun' UNION ALL
                        SELECT 7, 'Jul' UNION ALL
                        SELECT 8, 'Aug' UNION ALL
                        SELECT 9, 'Sep' UNION ALL
                        SELECT 10, 'Oct' UNION ALL
                        SELECT 11, 'Nov' UNION ALL
                        SELECT 12, 'Dec'
                    )
                    SELECT
                        m.month_num,
                        m.month_name as period_label,
                        year_filter as year,
                        -- Subscription revenue (package prices)
                        COALESCE(SUM(CASE WHEN s.status = 1 THEN s.price ELSE 0 END), 0) as subscription_revenue,
                        -- Actual payments received
                        COALESCE(SUM(sp.amount), 0) as actual_payments,
                        -- Outstanding amount
                        COALESCE(SUM(CASE WHEN sp.status = 0 THEN sp.amount ELSE 0 END), 0) as outstanding_amount,
                        -- Payment count
                        COUNT(sp.id) as payment_count
                    FROM months m
                    LEFT JOIN subscription_payments sp ON
                        MONTH(sp.payment_date) = m.month_num
                        AND YEAR(sp.payment_date) = year_filter
                    LEFT JOIN subscriptions s ON sp.subscription_id = s.id
                    GROUP BY m.month_num, m.month_name
                    ORDER BY m.month_num;

                ELSE
                    -- Yearly trends for last 5 years from year_filter
                    SELECT
                        YEAR(sp.payment_date) as year,
                        CONCAT(YEAR(sp.payment_date)) as period_label,
                        -- Subscription revenue
                        COALESCE(SUM(CASE WHEN s.status = 1 THEN s.price ELSE 0 END), 0) as subscription_revenue,
                        -- Actual payments received
                        COALESCE(SUM(CASE WHEN sp.status = 1 THEN sp.amount ELSE 0 END), 0) as actual_payments,
                        -- Outstanding amount
                        COALESCE(SUM(CASE WHEN sp.status = 0 THEN sp.amount ELSE 0 END), 0) as outstanding_amount,
                        -- Payment count
                        COUNT(sp.id) as payment_count
                    FROM subscription_payments sp
                    LEFT JOIN subscriptions s ON sp.subscription_id = s.id
                    WHERE YEAR(sp.payment_date) BETWEEN (year_filter - 4) AND year_filter
                    GROUP BY YEAR(sp.payment_date)
                    ORDER BY YEAR(sp.payment_date) DESC;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_dashboard_metrics');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_payment_collection_report');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_school_growth_report');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_package_distribution');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_outstanding_payments_report');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_revenue_trends');
    }
};
