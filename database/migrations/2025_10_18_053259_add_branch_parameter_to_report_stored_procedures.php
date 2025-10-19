<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Add Branch Parameter to Report Stored Procedures
 *
 * This migration adds the p_branch_id parameter to ALL reporting stored procedures
 * to enable branch-based filtering in the metadata reporting system.
 */
return new class extends Migration
{
    /**
     * List of all report stored procedures that need branch parameter
     */
    private array $reportProcedures = [
        'GetDiscountReport',
        'GetFeeGenerationCollectionReport',
        'GetFeeGenerationReport',
        'GetGuardianListReport',
        'GetPaidStudentsReport',
        'GetStudentExamReport',
        'GetStudentGradebook',
        'GetStudentListReport',
        'GetStudentRegistrationReport',
        'GetUnpaidStudentsReport',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->updateGetDiscountReport();
        $this->updateGetFeeGenerationCollectionReport();
        $this->updateGetFeeGenerationReport();
        $this->updateGetGuardianListReport();
        $this->updateGetPaidStudentsReport();
        $this->updateGetStudentExamReport();
        $this->updateGetStudentGradebook();
        $this->updateGetStudentListReport();
        $this->updateGetStudentRegistrationReport();
        $this->updateGetUnpaidStudentsReport();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->rollbackGetDiscountReport();
        $this->rollbackGetFeeGenerationCollectionReport();
        $this->rollbackGetFeeGenerationReport();
        $this->rollbackGetGuardianListReport();
        $this->rollbackGetPaidStudentsReport();
        $this->rollbackGetStudentExamReport();
        $this->rollbackGetStudentGradebook();
        $this->rollbackGetStudentListReport();
        $this->rollbackGetStudentRegistrationReport();
        $this->rollbackGetUnpaidStudentsReport();
    }

    /**
     * Update GetDiscountReport with branch parameter
     */
    private function updateGetDiscountReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetDiscountReport');

        DB::unprepared("
            CREATE PROCEDURE GetDiscountReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                SELECT
                    CONCAT(s.first_name COLLATE utf8mb4_unicode_ci, ' ', s.last_name COLLATE utf8mb4_unicode_ci) AS student_name,
                    s.mobile,
                    s.grade,
                    c.name COLLATE utf8mb4_unicode_ci AS class,
                    sec.name COLLATE utf8mb4_unicode_ci AS section,
                    SUM(fc.discount_applied) AS discount_amount
                FROM students s
                INNER JOIN session_class_students scs ON s.id = scs.student_id
                INNER JOIN classes c ON scs.classes_id = c.id
                INNER JOIN sections sec ON scs.section_id = sec.id
                INNER JOIN fees_collects fc ON s.id = fc.student_id
                    AND fc.discount_applied > 0
                    AND fc.date BETWEEN p_start_date AND p_end_date
                WHERE
                    (p_grade IS NULL OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                GROUP BY s.id, student_name, s.mobile, s.grade, c.name, sec.name
                ORDER BY s.grade, c.name COLLATE utf8mb4_unicode_ci, sec.name COLLATE utf8mb4_unicode_ci, student_name;
            END
        ");
    }

    /**
     * Rollback GetDiscountReport
     */
    private function rollbackGetDiscountReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetDiscountReport');

        DB::unprepared("
            CREATE PROCEDURE GetDiscountReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT
            )
            BEGIN
                SELECT
                    CONCAT(s.first_name COLLATE utf8mb4_unicode_ci, ' ', s.last_name COLLATE utf8mb4_unicode_ci) AS student_name,
                    s.mobile,
                    s.grade,
                    c.name COLLATE utf8mb4_unicode_ci AS class,
                    sec.name COLLATE utf8mb4_unicode_ci AS section,
                    SUM(fc.discount_applied) AS discount_amount
                FROM students s
                INNER JOIN session_class_students scs ON s.id = scs.student_id
                INNER JOIN classes c ON scs.classes_id = c.id
                INNER JOIN sections sec ON scs.section_id = sec.id
                INNER JOIN fees_collects fc ON s.id = fc.student_id
                    AND fc.discount_applied > 0
                    AND fc.date BETWEEN p_start_date AND p_end_date
                WHERE
                    (p_grade IS NULL OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                GROUP BY s.id, student_name, s.mobile, s.grade, c.name, sec.name
                ORDER BY s.grade, c.name COLLATE utf8mb4_unicode_ci, sec.name COLLATE utf8mb4_unicode_ci, student_name;
            END
        ");
    }

    /**
     * Update GetFeeGenerationCollectionReport with branch parameter
     */
    private function updateGetFeeGenerationCollectionReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetFeeGenerationCollectionReport');

        DB::unprepared("
            CREATE PROCEDURE GetFeeGenerationCollectionReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                DECLARE v_previous_invoice DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_current_invoice DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_total_invoice DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_unpaid_total DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_total_paid DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_total_discount DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_sub_total DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_deposit DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_grand_total DECIMAL(16,2) DEFAULT 0.00;

                -- Calculate Previous Invoice
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_previous_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE DATE(fc.created_at) < p_start_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND fc.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_previous_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE DATE(fc.created_at) < p_start_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND fc.amount > 0;
                END IF;

                -- Calculate Current Invoice
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_current_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND fc.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_current_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND fc.amount > 0;
                END IF;

                SET v_total_invoice = v_previous_invoice + v_current_invoice;

                -- Calculate Total Paid
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN 0
                            ELSE pt.amount
                        END
                    ), 0.00)
                    INTO v_total_paid
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND pt.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN 0
                            ELSE pt.amount
                        END
                    ), 0.00)
                    INTO v_total_paid
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND pt.amount > 0;
                END IF;

                -- Calculate Total Discount
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(fc.discount_amount), 0.00)
                    INTO v_total_discount
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND fc.discount_amount > 0;
                ELSE
                    SELECT COALESCE(SUM(fc.discount_amount), 0.00)
                    INTO v_total_discount
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND fc.discount_amount > 0;
                END IF;

                SET v_sub_total = v_total_paid + v_total_discount;

                -- Calculate Deposit
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN pt.amount
                            ELSE 0
                        END
                    ), 0.00)
                    INTO v_deposit
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND pt.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN pt.amount
                            ELSE 0
                        END
                    ), 0.00)
                    INTO v_deposit
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                        AND pt.amount > 0;
                END IF;

                SET v_grand_total = v_sub_total + v_deposit;
                SET v_unpaid_total = v_total_invoice - (v_total_paid + v_total_discount + v_deposit);

                SELECT
                    CONCAT('$', FORMAT(v_previous_invoice, 2)) AS previous_invoice,
                    CONCAT('$', FORMAT(v_current_invoice, 2)) AS current_invoice,
                    CONCAT('$', FORMAT(v_total_invoice, 2)) AS total_invoice,
                    CONCAT('$', FORMAT(v_unpaid_total, 2)) AS unpaid_total,
                    CONCAT('$', FORMAT(v_total_paid, 2)) AS total_paid,
                    CONCAT('$', FORMAT(v_total_discount, 2)) AS total_discount,
                    CONCAT('$', FORMAT(v_sub_total, 2)) AS sub_total,
                    CONCAT('$', FORMAT(v_deposit, 2)) AS deposit,
                    CONCAT('$', FORMAT(v_grand_total, 2)) AS grand_total;
            END
        ");
    }

    /**
     * Rollback GetFeeGenerationCollectionReport
     */
    private function rollbackGetFeeGenerationCollectionReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetFeeGenerationCollectionReport');

        DB::unprepared("
            CREATE PROCEDURE GetFeeGenerationCollectionReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT
            )
            BEGIN
                DECLARE v_previous_invoice DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_current_invoice DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_total_invoice DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_unpaid_total DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_total_paid DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_total_discount DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_sub_total DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_deposit DECIMAL(16,2) DEFAULT 0.00;
                DECLARE v_grand_total DECIMAL(16,2) DEFAULT 0.00;

                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_previous_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE DATE(fc.created_at) < p_start_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND fc.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_previous_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE DATE(fc.created_at) < p_start_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND fc.amount > 0;
                END IF;

                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_current_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND fc.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(fc.amount), 0.00)
                    INTO v_current_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND fc.amount > 0;
                END IF;

                SET v_total_invoice = v_previous_invoice + v_current_invoice;

                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN 0
                            ELSE pt.amount
                        END
                    ), 0.00)
                    INTO v_total_paid
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND pt.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN 0
                            ELSE pt.amount
                        END
                    ), 0.00)
                    INTO v_total_paid
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND pt.amount > 0;
                END IF;

                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(fc.discount_amount), 0.00)
                    INTO v_total_discount
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND fc.discount_amount > 0;
                ELSE
                    SELECT COALESCE(SUM(fc.discount_amount), 0.00)
                    INTO v_total_discount
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND fc.discount_amount > 0;
                END IF;

                SET v_sub_total = v_total_paid + v_total_discount;

                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN pt.amount
                            ELSE 0
                        END
                    ), 0.00)
                    INTO v_deposit
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND pt.amount > 0;
                ELSE
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN pt.payment_gateway = 'deposit' THEN pt.amount
                            ELSE 0
                        END
                    ), 0.00)
                    INTO v_deposit
                    FROM payment_transactions pt
                    INNER JOIN students s ON pt.student_id = s.id
                    WHERE pt.payment_date BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND pt.amount > 0;
                END IF;

                SET v_grand_total = v_sub_total + v_deposit;
                SET v_unpaid_total = v_total_invoice - (v_total_paid + v_total_discount + v_deposit);

                SELECT
                    CONCAT('$', FORMAT(v_previous_invoice, 2)) AS previous_invoice,
                    CONCAT('$', FORMAT(v_current_invoice, 2)) AS current_invoice,
                    CONCAT('$', FORMAT(v_total_invoice, 2)) AS total_invoice,
                    CONCAT('$', FORMAT(v_unpaid_total, 2)) AS unpaid_total,
                    CONCAT('$', FORMAT(v_total_paid, 2)) AS total_paid,
                    CONCAT('$', FORMAT(v_total_discount, 2)) AS total_discount,
                    CONCAT('$', FORMAT(v_sub_total, 2)) AS sub_total,
                    CONCAT('$', FORMAT(v_deposit, 2)) AS deposit,
                    CONCAT('$', FORMAT(v_grand_total, 2)) AS grand_total;
            END
        ");
    }

    /**
     * Update GetFeeGenerationReport with branch parameter
     */
    private function updateGetFeeGenerationReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetFeeGenerationReport');

        DB::unprepared("
            CREATE PROCEDURE GetFeeGenerationReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT
                        s.grade,
                        CONCAT('$', SUM(fc.amount)) AS total_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE
                        DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                    GROUP BY s.grade
                    ORDER BY s.grade;
                ELSE
                    SELECT
                        s.grade,
                        CONCAT('$', SUM(fc.amount)) AS total_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE
                        DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                    GROUP BY s.grade
                    ORDER BY s.grade;
                END IF;
            END
        ");
    }

    /**
     * Rollback GetFeeGenerationReport
     */
    private function rollbackGetFeeGenerationReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetFeeGenerationReport');

        DB::unprepared("
            CREATE PROCEDURE GetFeeGenerationReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT
            )
            BEGIN
                IF p_class_id IS NOT NULL OR p_section_id IS NOT NULL THEN
                    SELECT
                        s.grade,
                        CONCAT('$', SUM(fc.amount)) AS total_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    INNER JOIN session_class_students scs ON s.id = scs.student_id
                    WHERE
                        DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    GROUP BY s.grade
                    ORDER BY s.grade;
                ELSE
                    SELECT
                        s.grade,
                        CONCAT('$', SUM(fc.amount)) AS total_invoice
                    FROM fees_collects fc
                    INNER JOIN students s ON fc.student_id = s.id
                    WHERE
                        DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                        AND (p_grade IS NULL OR p_grade = '' OR s.grade COLLATE utf8mb4_unicode_ci = p_grade COLLATE utf8mb4_unicode_ci)
                    GROUP BY s.grade
                    ORDER BY s.grade;
                END IF;
            END
        ");
    }

    /**
     * Update GetGuardianListReport with branch parameter
     */
    private function updateGetGuardianListReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetGuardianListReport');

        DB::unprepared("
            CREATE PROCEDURE GetGuardianListReport(
                IN p_branch_id INT
            )
            BEGIN
                SELECT 
                    guardian_name,
                    guardian_mobile,
                    guardian_address,
                    relation_type,
                    SUM(total_students) AS total_students
                FROM (
                    SELECT 
                        pg.guardian_name AS guardian_name,
                        pg.guardian_mobile AS guardian_mobile,
                        pg.guardian_address AS guardian_address,
                        COALESCE(pg.guardian_relation, 'Guardian') AS relation_type,
                        COUNT(DISTINCT s.id) AS total_students
                    FROM 
                        parent_guardians pg
                    LEFT JOIN 
                        students s ON pg.id = s.parent_guardian_id
                    WHERE 
                        pg.guardian_name IS NOT NULL 
                        AND pg.guardian_name != ''
                        AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                    GROUP BY 
                        pg.guardian_name, 
                        pg.guardian_mobile, 
                        pg.guardian_address, 
                        pg.guardian_relation
                ) AS guardian_list
                GROUP BY 
                    guardian_name,
                    guardian_mobile,
                    guardian_address,
                    relation_type
                HAVING 
                    total_students > 0
                ORDER BY 
                    total_students DESC,
                    guardian_name ASC;
            END
        ");
    }

    /**
     * Rollback GetGuardianListReport
     */
    private function rollbackGetGuardianListReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetGuardianListReport');

        DB::unprepared("
            CREATE PROCEDURE GetGuardianListReport()
            BEGIN
                SELECT 
                    guardian_name,
                    guardian_mobile,
                    guardian_address,
                    relation_type,
                    SUM(total_students) AS total_students
                FROM (
                    SELECT 
                        pg.guardian_name AS guardian_name,
                        pg.guardian_mobile AS guardian_mobile,
                        pg.guardian_address AS guardian_address,
                        COALESCE(pg.guardian_relation, 'Guardian') AS relation_type,
                        COUNT(DISTINCT s.id) AS total_students
                    FROM 
                        parent_guardians pg
                    LEFT JOIN 
                        students s ON pg.id = s.parent_guardian_id
                    WHERE 
                        pg.guardian_name IS NOT NULL 
                        AND pg.guardian_name != ''
                    GROUP BY 
                        pg.guardian_name, 
                        pg.guardian_mobile, 
                        pg.guardian_address, 
                        pg.guardian_relation
                ) AS guardian_list
                GROUP BY 
                    guardian_name,
                    guardian_mobile,
                    guardian_address,
                    relation_type
                HAVING 
                    total_students > 0
                ORDER BY 
                    total_students DESC,
                    guardian_name ASC;
            END
        ");
    }

    /**
     * Update GetPaidStudentsReport with branch parameter
     */
    private function updateGetPaidStudentsReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetPaidStudentsReport');

        DB::unprepared("
            CREATE PROCEDURE GetPaidStudentsReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_gender_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                SELECT 
                    pt.payment_date,
                    j.name AS journal,
                    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    s.mobile,
                    CASE 
                        WHEN pt.payment_gateway = 'deposit' THEN 0
                        ELSE pt.amount
                    END AS paid_amount,
                    CASE 
                        WHEN pt.payment_gateway = 'deposit' THEN pt.amount
                        ELSE 0
                    END AS deposit_used,
                    COALESCE(fc.discount_amount, 0) AS discount
                FROM 
                    payment_transactions pt
                INNER JOIN 
                    students s ON pt.student_id = s.id
                LEFT JOIN 
                    journals j ON pt.journal_id = j.id
                LEFT JOIN 
                    fees_collects fc ON pt.fees_collect_id = fc.id
                LEFT JOIN 
                    session_class_students scs ON s.id = scs.student_id
                LEFT JOIN 
                    genders g ON s.gender_id = g.id
                WHERE 
                    pt.payment_date BETWEEN p_start_date AND p_end_date
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_gender_id IS NULL OR s.gender_id = p_gender_id)
                    AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                ORDER BY 
                    pt.payment_date DESC,
                    student_name;
            END
        ");
    }

    /**
     * Rollback GetPaidStudentsReport
     */
    private function rollbackGetPaidStudentsReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetPaidStudentsReport');

        DB::unprepared("
            CREATE PROCEDURE GetPaidStudentsReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_gender_id BIGINT
            )
            BEGIN
                SELECT 
                    pt.payment_date,
                    j.name AS journal,
                    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    s.mobile,
                    CASE 
                        WHEN pt.payment_gateway = 'deposit' THEN 0
                        ELSE pt.amount
                    END AS paid_amount,
                    CASE 
                        WHEN pt.payment_gateway = 'deposit' THEN pt.amount
                        ELSE 0
                    END AS deposit_used,
                    COALESCE(fc.discount_amount, 0) AS discount
                FROM 
                    payment_transactions pt
                INNER JOIN 
                    students s ON pt.student_id = s.id
                LEFT JOIN 
                    journals j ON pt.journal_id = j.id
                LEFT JOIN 
                    fees_collects fc ON pt.fees_collect_id = fc.id
                LEFT JOIN 
                    session_class_students scs ON s.id = scs.student_id
                LEFT JOIN 
                    genders g ON s.gender_id = g.id
                WHERE 
                    pt.payment_date BETWEEN p_start_date AND p_end_date
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_gender_id IS NULL OR s.gender_id = p_gender_id)
                ORDER BY 
                    pt.payment_date DESC,
                    student_name;
            END
        ");
    }

    /**
     * Update GetStudentExamReport with branch parameter
     */
    private function updateGetStudentExamReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentExamReport');

        DB::unprepared("
            CREATE PROCEDURE GetStudentExamReport(
                IN p_session_id BIGINT,
                IN p_term_id BIGINT,
                IN p_exam_type_id BIGINT,
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_student_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                SELECT
                    s.name AS subject_name,
                    eer.obtained_marks AS result,
                    CASE
                        WHEN eer.is_absent = 1 THEN 'Absent'
                        WHEN eer.obtained_marks IS NULL THEN 'Not Graded'
                        ELSE COALESCE(mg.name, 'N/A')
                    END AS grade,
                    COALESCE(ee.total_marks, 100) AS total_marks
                FROM
                    subject_assigns sa
                INNER JOIN
                    subject_assign_childrens sac ON sa.id = sac.subject_assign_id
                INNER JOIN
                    subjects s ON sac.subject_id = s.id
                LEFT JOIN
                    exam_entries ee ON (
                        ee.class_id = p_class_id
                        AND ee.section_id = p_section_id
                        AND ee.exam_type_id = p_exam_type_id
                        AND ee.session_id = p_session_id
                        AND ee.term_id = p_term_id
                        AND ee.status = 'published'
                        AND (
                            ee.subject_id = s.id
                            OR ee.is_all_subjects = 1
                        )
                    )
                LEFT JOIN
                    exam_entry_results eer ON (
                        eer.exam_entry_id = ee.id
                        AND eer.student_id = p_student_id
                        AND eer.subject_id = s.id
                    )
                LEFT JOIN
                    marks_grades mg ON (
                        eer.is_absent = 0
                        AND eer.obtained_marks IS NOT NULL
                        AND ee.total_marks IS NOT NULL
                        AND ee.total_marks > 0
                        AND (eer.obtained_marks / ee.total_marks) * 100 >= mg.percent_from
                        AND (eer.obtained_marks / ee.total_marks) * 100 <= mg.percent_upto
                    )
                INNER JOIN
                    students st ON st.id = p_student_id
                WHERE
                    sa.session_id = p_session_id
                    AND sa.classes_id = p_class_id
                    AND sa.section_id = p_section_id
                    AND (p_branch_id IS NULL OR st.branch_id = p_branch_id)
                ORDER BY
                    s.name;
            END
        ");
    }

    /**
     * Rollback GetStudentExamReport
     */
    private function rollbackGetStudentExamReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentExamReport');

        DB::unprepared("
            CREATE PROCEDURE GetStudentExamReport(
                IN p_session_id BIGINT,
                IN p_term_id BIGINT,
                IN p_exam_type_id BIGINT,
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_student_id BIGINT
            )
            BEGIN
                SELECT
                    s.name AS subject_name,
                    eer.obtained_marks AS result,
                    CASE
                        WHEN eer.is_absent = 1 THEN 'Absent'
                        WHEN eer.obtained_marks IS NULL THEN 'Not Graded'
                        ELSE COALESCE(mg.name, 'N/A')
                    END AS grade,
                    COALESCE(ee.total_marks, 100) AS total_marks
                FROM
                    subject_assigns sa
                INNER JOIN
                    subject_assign_childrens sac ON sa.id = sac.subject_assign_id
                INNER JOIN
                    subjects s ON sac.subject_id = s.id
                LEFT JOIN
                    exam_entries ee ON (
                        ee.class_id = p_class_id
                        AND ee.section_id = p_section_id
                        AND ee.exam_type_id = p_exam_type_id
                        AND ee.session_id = p_session_id
                        AND ee.term_id = p_term_id
                        AND ee.status = 'published'
                        AND (
                            ee.subject_id = s.id
                            OR ee.is_all_subjects = 1
                        )
                    )
                LEFT JOIN
                    exam_entry_results eer ON (
                        eer.exam_entry_id = ee.id
                        AND eer.student_id = p_student_id
                        AND eer.subject_id = s.id
                    )
                LEFT JOIN
                    marks_grades mg ON (
                        eer.is_absent = 0
                        AND eer.obtained_marks IS NOT NULL
                        AND ee.total_marks IS NOT NULL
                        AND ee.total_marks > 0
                        AND (eer.obtained_marks / ee.total_marks) * 100 >= mg.percent_from
                        AND (eer.obtained_marks / ee.total_marks) * 100 <= mg.percent_upto
                    )
                WHERE
                    sa.session_id = p_session_id
                    AND sa.classes_id = p_class_id
                    AND sa.section_id = p_section_id
                ORDER BY
                    s.name;
            END
        ");
    }

    /**
     * Update GetStudentGradebook with branch parameter
     */
    private function updateGetStudentGradebook(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentGradebook');

        DB::unprepared("
            CREATE PROCEDURE GetStudentGradebook(
                IN p_session_id BIGINT,
                IN p_term_id BIGINT,
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_student_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                DECLARE v_student_exists INT;

                SELECT COUNT(*)
                INTO v_student_exists
                FROM session_class_students scs
                INNER JOIN students s ON scs.student_id = s.id
                WHERE scs.session_id = p_session_id
                  AND scs.student_id = p_student_id
                  AND scs.classes_id = p_class_id
                  AND scs.section_id = p_section_id
                  AND (p_branch_id IS NULL OR s.branch_id = p_branch_id);

                IF v_student_exists = 0 THEN
                    SELECT 'Student not enrolled in the specified class and section for this session' AS error_message;
                ELSE
                    SELECT
                        subj.name AS subject_name,
                        MAX(CASE
                            WHEN et.id = 6 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS monthly_exam_1,
                        MAX(CASE
                            WHEN et.id = 4 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS mid_term,
                        MAX(CASE
                            WHEN et.id = 5 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS monthly_exam_2,
                        MAX(CASE
                            WHEN et.id = 7 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS final_term
                    FROM
                        students st
                    INNER JOIN
                        session_class_students scs ON st.id = scs.student_id
                            AND scs.session_id = p_session_id
                            AND scs.student_id = p_student_id
                            AND scs.classes_id = p_class_id
                            AND scs.section_id = p_section_id
                    INNER JOIN
                        subject_assigns sa ON sa.session_id = p_session_id
                            AND sa.classes_id = p_class_id
                            AND sa.section_id = p_section_id
                    INNER JOIN
                        subject_assign_childrens sac ON sa.id = sac.subject_assign_id
                    INNER JOIN
                        subjects subj ON sac.subject_id = subj.id
                            AND subj.status = 1
                    LEFT JOIN
                        exam_entries ee ON ee.class_id = p_class_id
                            AND ee.section_id = p_section_id
                            AND ee.session_id = p_session_id
                            AND ee.term_id = p_term_id
                            AND ee.status = 'published'
                            AND (ee.subject_id = subj.id OR ee.is_all_subjects = 1)
                    LEFT JOIN
                        exam_types et ON ee.exam_type_id = et.id
                            AND et.status = 1
                            AND et.id IN (4, 5, 6, 7)
                    LEFT JOIN
                        exam_entry_results eer ON eer.exam_entry_id = ee.id
                            AND eer.student_id = p_student_id
                            AND eer.subject_id = subj.id
                    WHERE
                        (p_branch_id IS NULL OR st.branch_id = p_branch_id)
                    GROUP BY
                        subj.id,
                        subj.name
                    ORDER BY
                        subj.name;
                END IF;
            END
        ");
    }

    /**
     * Rollback GetStudentGradebook
     */
    private function rollbackGetStudentGradebook(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentGradebook');

        DB::unprepared("
            CREATE PROCEDURE GetStudentGradebook(
                IN p_session_id BIGINT,
                IN p_term_id BIGINT,
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_student_id BIGINT
            )
            BEGIN
                DECLARE v_student_exists INT;

                SELECT COUNT(*)
                INTO v_student_exists
                FROM session_class_students
                WHERE session_id = p_session_id
                  AND student_id = p_student_id
                  AND classes_id = p_class_id
                  AND section_id = p_section_id;

                IF v_student_exists = 0 THEN
                    SELECT 'Student not enrolled in the specified class and section for this session' AS error_message;
                ELSE
                    SELECT
                        subj.name AS subject_name,
                        MAX(CASE
                            WHEN et.id = 6 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS monthly_exam_1,
                        MAX(CASE
                            WHEN et.id = 4 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS mid_term,
                        MAX(CASE
                            WHEN et.id = 5 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS monthly_exam_2,
                        MAX(CASE
                            WHEN et.id = 7 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS final_term
                    FROM
                        students st
                    INNER JOIN
                        session_class_students scs ON st.id = scs.student_id
                            AND scs.session_id = p_session_id
                            AND scs.student_id = p_student_id
                            AND scs.classes_id = p_class_id
                            AND scs.section_id = p_section_id
                    INNER JOIN
                        subject_assigns sa ON sa.session_id = p_session_id
                            AND sa.classes_id = p_class_id
                            AND sa.section_id = p_section_id
                    INNER JOIN
                        subject_assign_childrens sac ON sa.id = sac.subject_assign_id
                    INNER JOIN
                        subjects subj ON sac.subject_id = subj.id
                            AND subj.status = 1
                    LEFT JOIN
                        exam_entries ee ON ee.class_id = p_class_id
                            AND ee.section_id = p_section_id
                            AND ee.session_id = p_session_id
                            AND ee.term_id = p_term_id
                            AND ee.status = 'published'
                            AND (ee.subject_id = subj.id OR ee.is_all_subjects = 1)
                    LEFT JOIN
                        exam_types et ON ee.exam_type_id = et.id
                            AND et.status = 1
                            AND et.id IN (4, 5, 6, 7)
                    LEFT JOIN
                        exam_entry_results eer ON eer.exam_entry_id = ee.id
                            AND eer.student_id = p_student_id
                            AND eer.subject_id = subj.id
                    GROUP BY
                        subj.id,
                        subj.name
                    ORDER BY
                        subj.name;
                END IF;
            END
        ");
    }

    /**
     * Update GetStudentListReport with branch parameter
     */
    private function updateGetStudentListReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentListReport');

        DB::unprepared("
            CREATE PROCEDURE GetStudentListReport(
                IN p_session_id BIGINT,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_shift_id BIGINT,
                IN p_category_id BIGINT,
                IN p_status TINYINT,
                IN p_gender_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                SELECT
                    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                    s.mobile,
                    s.grade,
                    c.name AS class,
                    sec.name AS section,
                    sc.name AS student_type
                FROM
                    students s
                LEFT JOIN
                    session_class_students scs ON s.id = scs.student_id
                        AND (p_session_id IS NULL OR scs.session_id = p_session_id)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                LEFT JOIN
                    classes c ON scs.classes_id = c.id
                LEFT JOIN
                    sections sec ON scs.section_id = sec.id
                LEFT JOIN
                    student_categories sc ON s.student_category_id = sc.id
                        AND (p_category_id IS NULL OR sc.id = p_category_id)
                WHERE
                    (p_session_id IS NULL OR scs.session_id = p_session_id)
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                    AND (p_category_id IS NULL OR s.student_category_id = p_category_id)
                    AND (p_status IS NULL OR s.status = p_status)
                    AND (p_gender_id IS NULL OR s.gender_id = p_gender_id)
                    AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                ORDER BY
                    c.name,
                    sec.name,
                    s.grade,
                    scs.roll,
                    s.last_name,
                    s.first_name;
            END
        ");
    }

    /**
     * Rollback GetStudentListReport
     */
    private function rollbackGetStudentListReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentListReport');

        DB::unprepared("
            CREATE PROCEDURE GetStudentListReport(
                IN p_session_id BIGINT,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_shift_id BIGINT,
                IN p_category_id BIGINT,
                IN p_status TINYINT,
                IN p_gender_id BIGINT
            )
            BEGIN
                SELECT
                    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                    s.mobile,
                    s.grade,
                    c.name AS class,
                    sec.name AS section,
                    sc.name AS student_type
                FROM
                    students s
                LEFT JOIN
                    session_class_students scs ON s.id = scs.student_id
                        AND (p_session_id IS NULL OR scs.session_id = p_session_id)
                        AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                        AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                        AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                LEFT JOIN
                    classes c ON scs.classes_id = c.id
                LEFT JOIN
                    sections sec ON scs.section_id = sec.id
                LEFT JOIN
                    student_categories sc ON s.student_category_id = sc.id
                        AND (p_category_id IS NULL OR sc.id = p_category_id)
                WHERE
                    (p_session_id IS NULL OR scs.session_id = p_session_id)
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                    AND (p_category_id IS NULL OR s.student_category_id = p_category_id)
                    AND (p_status IS NULL OR s.status = p_status)
                    AND (p_gender_id IS NULL OR s.gender_id = p_gender_id)
                ORDER BY
                    c.name,
                    sec.name,
                    s.grade,
                    scs.roll,
                    s.last_name,
                    s.first_name;
            END
        ");
    }

    /**
     * Update GetStudentRegistrationReport with branch parameter
     */
    private function updateGetStudentRegistrationReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentRegistrationReport');

        DB::unprepared("
            CREATE PROCEDURE GetStudentRegistrationReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_shift_id BIGINT,
                IN p_status TINYINT,
                IN p_gender_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                SELECT
                    s.admission_date,
                    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                    s.mobile,
                    s.grade,
                    c.name AS class,
                    sec.name AS section,
                    sh.name AS shift
                FROM
                    students s
                LEFT JOIN
                    session_class_students scs ON s.id = scs.student_id
                LEFT JOIN
                    classes c ON scs.classes_id = c.id
                LEFT JOIN
                    sections sec ON scs.section_id = sec.id
                LEFT JOIN
                    shifts sh ON scs.shift_id = sh.id
                WHERE
                    s.admission_date BETWEEN p_start_date AND p_end_date
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                    AND (p_status IS NULL OR s.status = p_status)
                    AND (p_gender_id IS NULL OR s.gender_id = p_gender_id)
                    AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                ORDER BY
                    s.admission_date DESC,
                    c.name,
                    sec.name,
                    s.last_name,
                    s.first_name;
            END
        ");
    }

    /**
     * Rollback GetStudentRegistrationReport
     */
    private function rollbackGetStudentRegistrationReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetStudentRegistrationReport');

        DB::unprepared("
            CREATE PROCEDURE GetStudentRegistrationReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_shift_id BIGINT,
                IN p_status TINYINT,
                IN p_gender_id BIGINT
            )
            BEGIN
                SELECT
                    s.admission_date,
                    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                    s.mobile,
                    s.grade,
                    c.name AS class,
                    sec.name AS section,
                    sh.name AS shift
                FROM
                    students s
                LEFT JOIN
                    session_class_students scs ON s.id = scs.student_id
                LEFT JOIN
                    classes c ON scs.classes_id = c.id
                LEFT JOIN
                    sections sec ON scs.section_id = sec.id
                LEFT JOIN
                    shifts sh ON scs.shift_id = sh.id
                WHERE
                    s.admission_date BETWEEN p_start_date AND p_end_date
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                    AND (p_status IS NULL OR s.status = p_status)
                    AND (p_gender_id IS NULL OR s.gender_id = p_gender_id)
                ORDER BY
                    s.admission_date DESC,
                    c.name,
                    sec.name,
                    s.last_name,
                    s.first_name;
            END
        ");
    }

    /**
     * Update GetUnpaidStudentsReport with branch parameter
     */
    private function updateGetUnpaidStudentsReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetUnpaidStudentsReport');

        DB::unprepared("
            CREATE PROCEDURE GetUnpaidStudentsReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_status TINYINT,
                IN p_shift_id BIGINT,
                IN p_branch_id INT
            )
            BEGIN
                SELECT
                    DATE(fc.created_at) AS date,
                    CONCAT(s.first_name, ' ', s.last_name) AS name,
                    s.mobile,
                    s.grade,
                    c.name AS class,
                    sec.name AS section,
                    SUM(
                        COALESCE(fc.amount, 0)
                        + COALESCE(fc.fine_amount, 0)
                        - COALESCE(fc.discount_amount, 0)
                        - COALESCE(fc.total_paid, 0)
                    ) AS total_amount
                FROM
                    fees_collects fc
                INNER JOIN
                    students s ON fc.student_id = s.id
                LEFT JOIN
                    session_class_students scs ON s.id = scs.student_id
                        AND fc.session_id = scs.session_id
                LEFT JOIN
                    classes c ON scs.classes_id = c.id
                LEFT JOIN
                    sections sec ON scs.section_id = sec.id
                WHERE
                    fc.payment_status IN ('unpaid', 'partial')
                    AND DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_status IS NULL OR s.status = p_status)
                    AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                    AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                GROUP BY
                    DATE(fc.created_at),
                    s.id,
                    s.first_name,
                    s.last_name,
                    s.mobile,
                    s.grade,
                    c.name,
                    sec.name
                HAVING
                    total_amount > 0
                ORDER BY
                    DATE(fc.created_at) ASC,
                    name;
            END
        ");
    }

    /**
     * Rollback GetUnpaidStudentsReport
     */
    private function rollbackGetUnpaidStudentsReport(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetUnpaidStudentsReport');

        DB::unprepared("
            CREATE PROCEDURE GetUnpaidStudentsReport(
                IN p_start_date DATE,
                IN p_end_date DATE,
                IN p_grade VARCHAR(50),
                IN p_class_id BIGINT,
                IN p_section_id BIGINT,
                IN p_status TINYINT,
                IN p_shift_id BIGINT
            )
            BEGIN
                SELECT
                    DATE(fc.created_at) AS date,
                    CONCAT(s.first_name, ' ', s.last_name) AS name,
                    s.mobile,
                    s.grade,
                    c.name AS class,
                    sec.name AS section,
                    SUM(
                        COALESCE(fc.amount, 0)
                        + COALESCE(fc.fine_amount, 0)
                        - COALESCE(fc.discount_amount, 0)
                        - COALESCE(fc.total_paid, 0)
                    ) AS total_amount
                FROM
                    fees_collects fc
                INNER JOIN
                    students s ON fc.student_id = s.id
                LEFT JOIN
                    session_class_students scs ON s.id = scs.student_id
                        AND fc.session_id = scs.session_id
                LEFT JOIN
                    classes c ON scs.classes_id = c.id
                LEFT JOIN
                    sections sec ON scs.section_id = sec.id
                WHERE
                    fc.payment_status IN ('unpaid', 'partial')
                    AND DATE(fc.created_at) BETWEEN p_start_date AND p_end_date
                    AND (p_grade IS NULL OR s.grade = p_grade COLLATE utf8mb4_0900_ai_ci)
                    AND (p_class_id IS NULL OR scs.classes_id = p_class_id)
                    AND (p_section_id IS NULL OR scs.section_id = p_section_id)
                    AND (p_status IS NULL OR s.status = p_status)
                    AND (p_shift_id IS NULL OR scs.shift_id = p_shift_id)
                GROUP BY
                    DATE(fc.created_at),
                    s.id,
                    s.first_name,
                    s.last_name,
                    s.mobile,
                    s.grade,
                    c.name,
                    sec.name
                HAVING
                    total_amount > 0
                ORDER BY
                    DATE(fc.created_at) ASC,
                    name;
            END
        ");
    }
};
