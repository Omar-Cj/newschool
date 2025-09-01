-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 03, 2025 at 07:09 AM
-- Server version: 9.3.0
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `icon_upload_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `serial` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `abouts`
--

INSERT INTO `abouts` (`id`, `name`, `upload_id`, `icon_upload_id`, `description`, `serial`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Special Campus Tour', 64, 65, 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.', 0, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Graduation', 66, 67, 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.', 1, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Powerful Alumni', 68, 69, 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.', 2, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `about_translates`
--

CREATE TABLE `about_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `about_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_translates`
--

INSERT INTO `about_translates` (`id`, `about_id`, `locale`, `name`, `description`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Special Campus Tour', 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Graduation', 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Powerful Alumni', 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 1, 'bn', 'বিশেষ ক্যাম্পাস সফর', 'তারা খুব নরম এবং কোথাও কোন ব্যথা আছে ছেড়ে না. তিনি তার পরিবারের যত্ন নিতে পছন্দ করেন। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 2, 'bn', 'স্নাতক', 'তারা খুব নরম এবং কোথাও কোন ব্যথা আছে ছেড়ে না. তিনি তার পরিবারের যত্ন নিতে পছন্দ করেন। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 3, 'bn', 'শক্তিশালী প্রাক্তন ছাত্র', 'তারা খুব নরম এবং কোথাও কোন ব্যথা আছে ছেড়ে না. তিনি তার পরিবারের যত্ন নিতে পছন্দ করেন। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে।', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `account_heads`
--

CREATE TABLE `account_heads` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint UNSIGNED NOT NULL,
  `online_exam_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `result` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `answer_childrens`
--

CREATE TABLE `answer_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `answer_id` bigint UNSIGNED NOT NULL,
  `question_bank_id` bigint UNSIGNED NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `evaluation_mark` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assign_fees_discounts`
--

CREATE TABLE `assign_fees_discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `fees_assign_children_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_amount` double NOT NULL,
  `discount_percentage` double NOT NULL,
  `discount_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `classes_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `roll` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `attendance` tinyint DEFAULT '3' COMMENT '1=present, 2=late, 3=absent, 4=half_day',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blood_groups`
--

CREATE TABLE `blood_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_groups`
--

INSERT INTO `blood_groups` (`id`, `name`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'A+', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'A-', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'B+', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'B-', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'O+', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'O-', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'AB+', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'AB-', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rack_no` int NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `long` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `country_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `phone`, `email`, `address`, `lat`, `long`, `status`, `country_id`, `created_at`, `updated_at`) VALUES
(1, 'Head Office', '1234567890', 'headoffice@example.com', '123 Main St, City, Country', '23.8103', '90.4125', '1', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `top_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_show` tinyint(1) NOT NULL DEFAULT '1',
  `bg_image` bigint UNSIGNED DEFAULT NULL,
  `bottom_left_signature` bigint UNSIGNED DEFAULT NULL,
  `bottom_left_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `bottom_right_signature` bigint UNSIGNED DEFAULT NULL,
  `bottom_right_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` tinyint(1) NOT NULL DEFAULT '1',
  `name` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_rooms`
--

CREATE TABLE `class_rooms` (
  `id` bigint UNSIGNED NOT NULL,
  `room_no` int DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_routines`
--

CREATE TABLE `class_routines` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `day` tinyint DEFAULT NULL COMMENT 'sat=1, fri=7',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_routine_childrens`
--

CREATE TABLE `class_routine_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `class_routine_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `time_schedule_id` bigint UNSIGNED NOT NULL,
  `class_room_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_section_translates`
--

CREATE TABLE `class_section_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_setups`
--

CREATE TABLE `class_setups` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_setup_childrens`
--

CREATE TABLE `class_setup_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `class_setup_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_translates`
--

CREATE TABLE `class_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_infos`
--

CREATE TABLE `contact_infos` (
  `id` bigint UNSIGNED NOT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_infos`
--

INSERT INTO `contact_infos` (`id`, `upload_id`, `name`, `address`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 56, 'Our School', '222, Tower Building, Country Hall, California 777, United States', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 57, 'Our School', '222, Tower Building, Country Hall, California 777, United States', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 58, 'Our School', '222, Tower Building, Country Hall, California 777, United States', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 59, 'Our School', '222, Tower Building, Country Hall, California 777, United States', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_info_translates`
--

CREATE TABLE `contact_info_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `contact_info_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_info_translates`
--

INSERT INTO `contact_info_translates` (`id`, `contact_info_id`, `locale`, `name`, `address`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Our School', '222, Tower Building, Country Hall, California 777, United States', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Our School', '222, Tower Building, Country Hall, California 777, United States', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Our School', '222, Tower Building, Country Hall, California 777, United States', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 4, 'en', 'Our School', '222, Tower Building, Country Hall, California 777, United States', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 1, 'bn', 'আমাদের পাঠশালা', '222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 2, 'bn', 'আমাদের পাঠশালা', '222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 3, 'bn', 'আমাদের পাঠশালা', '222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 4, 'bn', 'আমাদের পাঠশালা', '222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_count` int DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `serial` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counters`
--

INSERT INTO `counters` (`id`, `name`, `total_count`, `upload_id`, `serial`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Curriculum', 0, 14, 0, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Students', 45, 15, 1, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Expert Teachers', 90, 16, 2, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'User', 135, 17, 3, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'Parents', 180, 18, 4, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `counter_translates`
--

CREATE TABLE `counter_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `counter_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_count` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counter_translates`
--

INSERT INTO `counter_translates` (`id`, `counter_id`, `locale`, `name`, `total_count`, `serial`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Curriculum', '0', '0', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Students', '45', '1', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Expert Teachers', '90', '2', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 4, 'en', 'User', '135', '3', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 5, 'en', 'Parents', '180', '4', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 1, 'bn', 'পাঠ্যক্রম', '০', '০', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 2, 'bn', 'ছাত্ররা', '৪৫', '১', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 3, 'bn', 'বিশেষজ্ঞ শিক্ষক', '৯০', '২', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 4, 'bn', 'ব্যবহারকারী', '১৩৫', '৩', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 5, 'bn', 'পিতামাতা', '১৮০', '৪', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decimal_digits` int DEFAULT '2',
  `decimal_point_separator` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thousand_separator` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `with_space` tinyint DEFAULT '0',
  `position` tinyint NOT NULL DEFAULT '1' COMMENT '0 => Suffix, 1 => Prefix',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `currency`, `code`, `symbol`, `decimal_digits`, `decimal_point_separator`, `thousand_separator`, `with_space`, `position`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'US Dollar', 'USD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(2, 'Canadian Dollar', 'CAD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(3, 'Euro', 'EUR', '€', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(4, 'UAE Dirham', 'AED', 'د.إ.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(5, 'Afghan Afghani', 'AFN', '؋', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(6, 'Albanian Lek', 'ALL', 'Lek', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(7, 'Armenian Dram', 'AMD', 'դր.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(8, 'Argentine Peso', 'ARS', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(9, 'Australian Dollar', 'AUD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(10, 'Azerbaijani Manat', 'AZN', 'ман.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(11, 'Bosnia-Herzegovina Convertible Mark', 'BAM', 'KM', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(12, 'Bangladeshi Taka', 'BDT', '৳', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(13, 'Bulgarian Lev', 'BGN', 'лв.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(14, 'Bahraini Dinar', 'BHD', 'د.ب.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(15, 'Burundian Franc', 'BIF', 'FBu', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(16, 'Brunei Dollar', 'BND', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(17, 'Bolivian Boliviano', 'BOB', 'Bs', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(18, 'Brazilian Real', 'BRL', 'R$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(19, 'Botswanan Pula', 'BWP', 'P', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(20, 'Belarusian Ruble', 'BYN', 'руб.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(21, 'Belize Dollar', 'BZD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(22, 'Congolese Franc', 'CDF', 'FrCD', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(23, 'Swiss Franc', 'CHF', 'CHF', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(24, 'Chilean Peso', 'CLP', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(25, 'Chinese Yuan', 'CNY', 'CN¥', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(26, 'Colombian Peso', 'COP', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(27, 'Costa Rican Colón', 'CRC', '₡', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(28, 'Cape Verdean Escudo', 'CVE', 'CV$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(29, 'Czech Republic Koruna', 'CZK', 'Kč', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(30, 'Djiboutian Franc', 'DJF', 'Fdj', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(31, 'Danish Krone', 'DKK', 'kr', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(32, 'Dominican Peso', 'DOP', 'RD$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(33, 'Algerian Dinar', 'DZD', 'د.ج.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(34, 'Estonian Kroon', 'EEK', 'kr', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(35, 'Egyptian Pound', 'EGP', 'ج.م.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(36, 'Eritrean Nakfa', 'ERN', 'Nfk', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(37, 'Ethiopian Birr', 'ETB', 'Br', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(38, 'British Pound Sterling', 'GBP', '£', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(39, 'Georgian Lari', 'GEL', 'GEL', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(40, 'Ghanaian Cedi', 'GHS', 'GH₵', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(41, 'Guinean Franc', 'GNF', 'FG', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(42, 'Guatemalan Quetzal', 'GTQ', 'Q', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(43, 'Hong Kong Dollar', 'HKD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(44, 'Honduran Lempira', 'HNL', 'L', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(45, 'Croatian Kuna', 'HRK', 'kn', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(46, 'Hungarian Forint', 'HUF', 'Ft', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(47, 'Indonesian Rupiah', 'IDR', 'Rp', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(48, 'Indian Rupee', 'INR', '₹', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(49, 'Iraqi Dinar', 'IQD', 'د.ع.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(50, 'Iranian Rial', 'IRR', '﷼', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(51, 'Icelandic Króna', 'ISK', 'kr', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(52, 'Jamaican Dollar', 'JMD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(53, 'Jordanian Dinar', 'JOD', 'د.أ.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(54, 'Japanese Yen', 'JPY', '￥', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(55, 'Kenyan Shilling', 'KES', 'Ksh', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(56, 'Cambodian Riel', 'KHR', '៛', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(57, 'Comorian Franc', 'KMF', 'FC', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(58, 'South Korean Won', 'KRW', '₩', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(59, 'Kuwaiti Dinar', 'KWD', 'د.ك.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(60, 'Kazakhstani Tenge', 'KZT', 'тңг.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(61, 'Lebanese Pound', 'LBP', 'ل.ل.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(62, 'Sri Lankan Rupee', 'LKR', 'SL Re', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(63, 'Lithuanian Litas', 'LTL', 'Lt', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(64, 'Latvian Lats', 'LVL', 'Ls', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(65, 'Libyan Dinar', 'LYD', 'د.ل.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(66, 'Moroccan Dirham', 'MAD', 'د.م.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(67, 'Moldovan Leu', 'MDL', 'MDL', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(68, 'Malagasy Ariary', 'MGA', 'MGA', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(69, 'Macedonian Denar', 'MKD', 'MKD', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(70, 'Myanma Kyat', 'MMK', 'K', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(71, 'Macanese Pataca', 'MOP', 'MOP$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(72, 'Mauritian Rupee', 'MUR', 'MURs', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(73, 'Mexican Peso', 'MXN', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(74, 'Malaysian Ringgit', 'MYR', 'RM', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(75, 'Mozambican Metical', 'MZN', 'MTn', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(76, 'Namibian Dollar', 'NAD', 'N$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(77, 'Nigerian Naira', 'NGN', '₦', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(78, 'Nicaraguan Córdoba', 'NIO', 'C$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(79, 'Norwegian Krone', 'NOK', 'kr', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(80, 'Nepalese Rupee', 'NPR', 'नेरू', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(81, 'New Zealand Dollar', 'NZD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(82, 'Omani Rial', 'OMR', 'ر.ع.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(83, 'Panamanian Balboa', 'PAB', 'B/.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(84, 'Peruvian Nuevo Sol', 'PEN', 'S/.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(85, 'Philippine Peso', 'PHP', '₱', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(86, 'Pakistani Rupee', 'PKR', '₨', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(87, 'Polish Zloty', 'PLN', 'zł', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(88, 'Paraguayan Guarani', 'PYG', '₲', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(89, 'Qatari Rial', 'QAR', 'ر.ق.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(90, 'Romanian Leu', 'RON', 'RON', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(91, 'Serbian Dinar', 'RSD', 'дин.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(92, 'Russian Ruble', 'RUB', '₽.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(93, 'Rwandan Franc', 'RWF', 'FR', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(94, 'Saudi Riyal', 'SAR', 'ر.س.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(95, 'Sudanese Pound', 'SDG', 'SDG', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(96, 'Swedish Krona', 'SEK', 'kr', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(97, 'Singapore Dollar', 'SGD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(98, 'Somali Shilling', 'SOS', 'Ssh', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(99, 'Syrian Pound', 'SYP', 'ل.س.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(100, 'Thai Baht', 'THB', '฿', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(101, 'Tunisian Dinar', 'TND', 'د.ت.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(102, 'Tongan Pa\'anga', 'TOP', 'T$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(103, 'Turkish Lira', 'TRY', 'TL', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(104, 'Trinidad and Tobago Dollar', 'TTD', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(105, 'New Taiwan Dollar', 'TWD', 'NT$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(106, 'Tanzanian Shilling', 'TZS', 'TSh', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(107, 'Ukrainian Hryvnia', 'UAH', '₴', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(108, 'Ugandan Shilling', 'UGX', 'USh', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(109, 'Uruguayan Peso', 'UYU', '$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(110, 'Uzbekistan Som', 'UZS', 'UZS', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(111, 'Venezuelan Bolívar', 'VEF', 'Bs.F.', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(112, 'Vietnamese Dong', 'VND', '₫', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(113, 'CFA Franc BEAC', 'XAF', 'FCFA', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(114, 'CFA Franc BCEAO', 'XOF', 'CFA', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(115, 'Yemeni Rial', 'YER', 'ر.ي.‏', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(116, 'South African Rand', 'ZAR', 'R', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(117, 'Zambian Kwacha', 'ZMK', 'ZK', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(118, 'Zimbabwean Dollar', 'ZWL', 'ZWL$', 2, NULL, NULL, 0, 1, '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_user_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `staff_user_id`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'History', NULL, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Science', NULL, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Business', NULL, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `department_contacts`
--

CREATE TABLE `department_contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_contacts`
--

INSERT INTO `department_contacts` (`id`, `upload_id`, `name`, `phone`, `email`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 60, 'Admission', '+883459783849', 'admission@mail.Com', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 61, 'Examination', '+883459783849', 'examination@mail.Com', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 62, 'Library', '+883459783849', 'library@mail.Com', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 63, 'media', '+883459783849', 'media@mail.Com', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `department_contact_translates`
--

CREATE TABLE `department_contact_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `department_contact_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_contact_translates`
--

INSERT INTO `department_contact_translates` (`id`, `department_contact_id`, `locale`, `name`, `phone`, `email`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Admission', '+883459783849', 'admission@mail.Com', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Examination', '+883459783849', 'examination@mail.Com', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Library', '+883459783849', 'library@mail.Com', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 4, 'en', 'media', '+883459783849', 'media@mail.Com', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 1, 'bn', 'ভর্তি', '+৮৮৩৪৫৯৭৮৩৮৪৯', 'এডমিশন@মেইল.কম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 2, 'bn', 'পরীক্ষা', '+৮৮৩৪৫৯৭৮৩৮৪৯', 'এক্সামিনেশন@মেইল.কম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 3, 'bn', 'লাইব্রেরি', '+৮৮৩৪৫৯৭৮৩৮৪৯', 'লাইব্রেরি@মেইল.কম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 4, 'bn', 'মিডিয়া', '+৮৮৩৪৫৯৭৮৩৮৪৯', 'মিডিয়া@মেইল.কম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `name`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'HRM', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Admin', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Accounts', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'Development', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'Software', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `early_payment_discounts`
--

CREATE TABLE `early_payment_discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `discount_percentage` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_translates`
--

CREATE TABLE `event_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `examination_results`
--

CREATE TABLE `examination_results` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `exam_type_id` bigint UNSIGNED DEFAULT NULL,
  `classes_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Failed',
  `grade_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_point` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int DEFAULT NULL,
  `total_marks` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `examination_settings`
--

CREATE TABLE `examination_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_assigns`
--

CREATE TABLE `exam_assigns` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `exam_type_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `total_mark` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_assign_childrens`
--

CREATE TABLE `exam_assign_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `exam_assign_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mark` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_routines`
--

CREATE TABLE `exam_routines` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `type_id` bigint UNSIGNED NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_routine_childrens`
--

CREATE TABLE `exam_routine_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `exam_routine_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `time_schedule_id` bigint UNSIGNED NOT NULL,
  `class_room_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_types`
--

CREATE TABLE `exam_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_head` bigint UNSIGNED NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_assigns`
--

CREATE TABLE `fees_assigns` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `gender_id` bigint UNSIGNED DEFAULT NULL,
  `fees_group_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_assign_childrens`
--

CREATE TABLE `fees_assign_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `fees_assign_id` bigint UNSIGNED NOT NULL,
  `fees_master_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_collects`
--

CREATE TABLE `fees_collects` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `payment_method` tinyint DEFAULT NULL,
  `payment_gateway` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fees_assign_children_id` bigint UNSIGNED NOT NULL,
  `fees_collect_by` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(16,2) DEFAULT NULL COMMENT 'total amount + fine',
  `fine_amount` decimal(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_groups`
--

CREATE TABLE `fees_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `online_admission_fees` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_masters`
--

CREATE TABLE `fees_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `fees_group_id` bigint UNSIGNED NOT NULL,
  `fees_type_id` bigint UNSIGNED NOT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT '0.00',
  `fine_type` tinyint NOT NULL DEFAULT '0' COMMENT '0 = none, 1 = percentage, 2 = fixed',
  `percentage` int DEFAULT '0',
  `fine_amount` decimal(16,2) DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_master_childrens`
--

CREATE TABLE `fees_master_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `fees_master_id` bigint UNSIGNED NOT NULL,
  `fees_type_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_types`
--

CREATE TABLE `fees_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flag_icons`
--

CREATE TABLE `flag_icons` (
  `id` bigint UNSIGNED NOT NULL,
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `flag_icons`
--

INSERT INTO `flag_icons` (`id`, `icon_class`, `title`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'flag-icon flag-icon-ad', 'ad', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'flag-icon flag-icon-ae', 'ae', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'flag-icon flag-icon-af', 'af', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'flag-icon flag-icon-ag', 'ag', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'flag-icon flag-icon-ai', 'ai', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'flag-icon flag-icon-al', 'al', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'flag-icon flag-icon-am', 'am', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'flag-icon flag-icon-ao', 'ao', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 'flag-icon flag-icon-aq', 'aq', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 'flag-icon flag-icon-ar', 'ar', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 'flag-icon flag-icon-as', 'as', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 'flag-icon flag-icon-at', 'at', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 'flag-icon flag-icon-au', 'au', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 'flag-icon flag-icon-aw', 'aw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 'flag-icon flag-icon-ax', 'ax', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 'flag-icon flag-icon-az', 'az', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 'flag-icon flag-icon-ba', 'ba', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 'flag-icon flag-icon-bb', 'bb', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 'flag-icon flag-icon-bd', 'bd', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 'flag-icon flag-icon-be', 'be', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 'flag-icon flag-icon-bf', 'bf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 'flag-icon flag-icon-bg', 'bg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 'flag-icon flag-icon-bh', 'bh', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 'flag-icon flag-icon-bi', 'bi', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(25, 'flag-icon flag-icon-bj', 'bj', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(26, 'flag-icon flag-icon-bl', 'bl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(27, 'flag-icon flag-icon-bm', 'bm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(28, 'flag-icon flag-icon-bn', 'bn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(29, 'flag-icon flag-icon-bo', 'bo', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(30, 'flag-icon flag-icon-bq', 'bq', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(31, 'flag-icon flag-icon-br', 'br', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(32, 'flag-icon flag-icon-bs', 'bs', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(33, 'flag-icon flag-icon-bt', 'bt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(34, 'flag-icon flag-icon-bv', 'bv', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(35, 'flag-icon flag-icon-bw', 'bw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(36, 'flag-icon flag-icon-by', 'by', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(37, 'flag-icon flag-icon-bz', 'bz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(38, 'flag-icon flag-icon-ca', 'ca', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(39, 'flag-icon flag-icon-cc', 'cc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(40, 'flag-icon flag-icon-cd', 'cd', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(41, 'flag-icon flag-icon-cf', 'cf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(42, 'flag-icon flag-icon-cg', 'cg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(43, 'flag-icon flag-icon-ch', 'ch', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(44, 'flag-icon flag-icon-ci', 'ci', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(45, 'flag-icon flag-icon-ck', 'ck', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(46, 'flag-icon flag-icon-cl', 'cl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(47, 'flag-icon flag-icon-cm', 'cm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(48, 'flag-icon flag-icon-cn', 'cn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(49, 'flag-icon flag-icon-co', 'co', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(50, 'flag-icon flag-icon-cr', 'cr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(51, 'flag-icon flag-icon-cu', 'cu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(52, 'flag-icon flag-icon-cv', 'cv', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(53, 'flag-icon flag-icon-cw', 'cw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(54, 'flag-icon flag-icon-cx', 'cx', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(55, 'flag-icon flag-icon-cy', 'cy', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(56, 'flag-icon flag-icon-cz', 'cz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(57, 'flag-icon flag-icon-de', 'de', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(58, 'flag-icon flag-icon-dj', 'dj', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(59, 'flag-icon flag-icon-dk', 'dk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(60, 'flag-icon flag-icon-dm', 'dm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(61, 'flag-icon flag-icon-do', 'do', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(62, 'flag-icon flag-icon-dz', 'dz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(63, 'flag-icon flag-icon-ec', 'ec', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(64, 'flag-icon flag-icon-ee', 'ee', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(65, 'flag-icon flag-icon-eg', 'eg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(66, 'flag-icon flag-icon-eh', 'eh', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(67, 'flag-icon flag-icon-er', 'er', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(68, 'flag-icon flag-icon-es', 'es', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(69, 'flag-icon flag-icon-et', 'et', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(70, 'flag-icon flag-icon-fi', 'fi', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(71, 'flag-icon flag-icon-fj', 'fj', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(72, 'flag-icon flag-icon-fk', 'fk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(73, 'flag-icon flag-icon-fm', 'fm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(74, 'flag-icon flag-icon-fo', 'fo', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(75, 'flag-icon flag-icon-fr', 'fr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(76, 'flag-icon flag-icon-ga', 'ga', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(77, 'flag-icon flag-icon-gb', 'gb', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(78, 'flag-icon flag-icon-gd', 'gd', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(79, 'flag-icon flag-icon-ge', 'ge', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(80, 'flag-icon flag-icon-gf', 'gf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(81, 'flag-icon flag-icon-gg', 'gg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(82, 'flag-icon flag-icon-gh', 'gh', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(83, 'flag-icon flag-icon-gi', 'gi', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(84, 'flag-icon flag-icon-gl', 'gl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(85, 'flag-icon flag-icon-gm', 'gm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(86, 'flag-icon flag-icon-gn', 'gn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(87, 'flag-icon flag-icon-gp', 'gp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(88, 'flag-icon flag-icon-gq', 'gq', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(89, 'flag-icon flag-icon-gr', 'gr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(90, 'flag-icon flag-icon-gs', 'gs', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(91, 'flag-icon flag-icon-gt', 'gt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(92, 'flag-icon flag-icon-gu', 'gu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(93, 'flag-icon flag-icon-gw', 'gw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(94, 'flag-icon flag-icon-gy', 'gy', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(95, 'flag-icon flag-icon-hk', 'hk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(96, 'flag-icon flag-icon-hm', 'hm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(97, 'flag-icon flag-icon-hn', 'hn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(98, 'flag-icon flag-icon-hr', 'hr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(99, 'flag-icon flag-icon-ht', 'ht', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(100, 'flag-icon flag-icon-hu', 'hu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(101, 'flag-icon flag-icon-id', 'id', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(102, 'flag-icon flag-icon-ie', 'ie', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(103, 'flag-icon flag-icon-il', 'il', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(104, 'flag-icon flag-icon-im', 'im', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(105, 'flag-icon flag-icon-in', 'in', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(106, 'flag-icon flag-icon-io', 'io', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(107, 'flag-icon flag-icon-iq', 'iq', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(108, 'flag-icon flag-icon-ir', 'ir', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(109, 'flag-icon flag-icon-is', 'is', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(110, 'flag-icon flag-icon-it', 'it', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(111, 'flag-icon flag-icon-je', 'je', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(112, 'flag-icon flag-icon-jm', 'jm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(113, 'flag-icon flag-icon-jo', 'jo', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(114, 'flag-icon flag-icon-jp', 'jp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(115, 'flag-icon flag-icon-ke', 'ke', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(116, 'flag-icon flag-icon-kg', 'kg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(117, 'flag-icon flag-icon-kh', 'kh', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(118, 'flag-icon flag-icon-ki', 'ki', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(119, 'flag-icon flag-icon-km', 'km', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(120, 'flag-icon flag-icon-kn', 'kn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(121, 'flag-icon flag-icon-kp', 'kp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(122, 'flag-icon flag-icon-kr', 'kr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(123, 'flag-icon flag-icon-kw', 'kw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(124, 'flag-icon flag-icon-ky', 'ky', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(125, 'flag-icon flag-icon-kz', 'kz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(126, 'flag-icon flag-icon-la', 'la', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(127, 'flag-icon flag-icon-lb', 'lb', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(128, 'flag-icon flag-icon-lc', 'lc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(129, 'flag-icon flag-icon-li', 'li', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(130, 'flag-icon flag-icon-lk', 'lk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(131, 'flag-icon flag-icon-lr', 'lr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(132, 'flag-icon flag-icon-ls', 'ls', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(133, 'flag-icon flag-icon-lt', 'lt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(134, 'flag-icon flag-icon-lu', 'lu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(135, 'flag-icon flag-icon-lv', 'lv', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(136, 'flag-icon flag-icon-ly', 'ly', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(137, 'flag-icon flag-icon-ma', 'ma', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(138, 'flag-icon flag-icon-mc', 'mc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(139, 'flag-icon flag-icon-md', 'md', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(140, 'flag-icon flag-icon-me', 'me', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(141, 'flag-icon flag-icon-mf', 'mf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(142, 'flag-icon flag-icon-mg', 'mg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(143, 'flag-icon flag-icon-mh', 'mh', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(144, 'flag-icon flag-icon-mk', 'mk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(145, 'flag-icon flag-icon-ml', 'ml', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(146, 'flag-icon flag-icon-mm', 'mm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(147, 'flag-icon flag-icon-mn', 'mn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(148, 'flag-icon flag-icon-mo', 'mo', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(149, 'flag-icon flag-icon-mp', 'mp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(150, 'flag-icon flag-icon-mq', 'mq', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(151, 'flag-icon flag-icon-mr', 'mr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(152, 'flag-icon flag-icon-ms', 'ms', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(153, 'flag-icon flag-icon-mt', 'mt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(154, 'flag-icon flag-icon-mu', 'mu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(155, 'flag-icon flag-icon-mv', 'mv', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(156, 'flag-icon flag-icon-mw', 'mw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(157, 'flag-icon flag-icon-mx', 'mx', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(158, 'flag-icon flag-icon-my', 'my', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(159, 'flag-icon flag-icon-mz', 'mz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(160, 'flag-icon flag-icon-na', 'na', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(161, 'flag-icon flag-icon-nc', 'nc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(162, 'flag-icon flag-icon-ne', 'ne', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(163, 'flag-icon flag-icon-nf', 'nf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(164, 'flag-icon flag-icon-ng', 'ng', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(165, 'flag-icon flag-icon-ni', 'ni', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(166, 'flag-icon flag-icon-nl', 'nl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(167, 'flag-icon flag-icon-no', 'no', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(168, 'flag-icon flag-icon-np', 'np', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(169, 'flag-icon flag-icon-nr', 'nr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(170, 'flag-icon flag-icon-nu', 'nu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(171, 'flag-icon flag-icon-nz', 'nz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(172, 'flag-icon flag-icon-om', 'om', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(173, 'flag-icon flag-icon-pa', 'pa', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(174, 'flag-icon flag-icon-pe', 'pe', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(175, 'flag-icon flag-icon-pf', 'pf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(176, 'flag-icon flag-icon-pg', 'pg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(177, 'flag-icon flag-icon-ph', 'ph', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(178, 'flag-icon flag-icon-pk', 'pk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(179, 'flag-icon flag-icon-pl', 'pl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(180, 'flag-icon flag-icon-pm', 'pm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(181, 'flag-icon flag-icon-pn', 'pn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(182, 'flag-icon flag-icon-pr', 'pr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(183, 'flag-icon flag-icon-ps', 'ps', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(184, 'flag-icon flag-icon-pt', 'pt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(185, 'flag-icon flag-icon-pw', 'pw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(186, 'flag-icon flag-icon-py', 'py', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(187, 'flag-icon flag-icon-qa', 'qa', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(188, 'flag-icon flag-icon-re', 're', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(189, 'flag-icon flag-icon-ro', 'ro', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(190, 'flag-icon flag-icon-rs', 'rs', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(191, 'flag-icon flag-icon-ru', 'ru', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(192, 'flag-icon flag-icon-rw', 'rw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(193, 'flag-icon flag-icon-sa', 'sa', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(194, 'flag-icon flag-icon-sb', 'sb', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(195, 'flag-icon flag-icon-sc', 'sc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(196, 'flag-icon flag-icon-sd', 'sd', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(197, 'flag-icon flag-icon-se', 'se', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(198, 'flag-icon flag-icon-sg', 'sg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(199, 'flag-icon flag-icon-sh', 'sh', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(200, 'flag-icon flag-icon-si', 'si', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(201, 'flag-icon flag-icon-sj', 'sj', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(202, 'flag-icon flag-icon-sk', 'sk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(203, 'flag-icon flag-icon-sl', 'sl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(204, 'flag-icon flag-icon-sm', 'sm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(205, 'flag-icon flag-icon-sn', 'sn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(206, 'flag-icon flag-icon-so', 'so', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(207, 'flag-icon flag-icon-sr', 'sr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(208, 'flag-icon flag-icon-ss', 'ss', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(209, 'flag-icon flag-icon-st', 'st', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(210, 'flag-icon flag-icon-sv', 'sv', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(211, 'flag-icon flag-icon-sx', 'sx', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(212, 'flag-icon flag-icon-sy', 'sy', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(213, 'flag-icon flag-icon-sz', 'sz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(214, 'flag-icon flag-icon-tc', 'tc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(215, 'flag-icon flag-icon-td', 'td', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(216, 'flag-icon flag-icon-tf', 'tf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(217, 'flag-icon flag-icon-tg', 'tg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(218, 'flag-icon flag-icon-th', 'th', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(219, 'flag-icon flag-icon-tj', 'tj', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(220, 'flag-icon flag-icon-tk', 'tk', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(221, 'flag-icon flag-icon-tl', 'tl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(222, 'flag-icon flag-icon-tm', 'tm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(223, 'flag-icon flag-icon-tn', 'tn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(224, 'flag-icon flag-icon-to', 'to', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(225, 'flag-icon flag-icon-tr', 'tr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(226, 'flag-icon flag-icon-tt', 'tt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(227, 'flag-icon flag-icon-tv', 'tv', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(228, 'flag-icon flag-icon-tw', 'tw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(229, 'flag-icon flag-icon-tz', 'tz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(230, 'flag-icon flag-icon-ua', 'ua', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(231, 'flag-icon flag-icon-ug', 'ug', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(232, 'flag-icon flag-icon-um', 'um', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(233, 'flag-icon flag-icon-us', 'us', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(234, 'flag-icon flag-icon-uy', 'uy', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(235, 'flag-icon flag-icon-uz', 'uz', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(236, 'flag-icon flag-icon-va', 'va', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(237, 'flag-icon flag-icon-vc', 'vc', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(238, 'flag-icon flag-icon-ve', 've', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(239, 'flag-icon flag-icon-vg', 'vg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(240, 'flag-icon flag-icon-vi', 'vi', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(241, 'flag-icon flag-icon-vn', 'vn', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(242, 'flag-icon flag-icon-vu', 'vu', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(243, 'flag-icon flag-icon-wf', 'wf', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(244, 'flag-icon flag-icon-ws', 'ws', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(245, 'flag-icon flag-icon-ye', 'ye', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(246, 'flag-icon flag-icon-yt', 'yt', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(247, 'flag-icon flag-icon-za', 'za', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(248, 'flag-icon flag-icon-zm', 'zm', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(249, 'flag-icon flag-icon-zw', 'zw', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `target_roles` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_by` bigint UNSIGNED DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `rejected_by` int DEFAULT NULL,
  `pending_by` int DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_post_comments`
--

CREATE TABLE `forum_post_comments` (
  `id` bigint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `forum_post_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int DEFAULT NULL,
  `published_by` bigint UNSIGNED NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint UNSIGNED NOT NULL,
  `gallery_category_id` bigint UNSIGNED NOT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `gallery_category_id`, `upload_id`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 32, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 3, 33, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 1, 34, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 1, 35, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 2, 36, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 3, 37, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 4, 38, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 3, 39, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 4, 40, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 1, 41, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 4, 42, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 3, 43, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 1, 44, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 2, 45, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 1, 46, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 2, 47, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 4, 48, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 3, 49, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 2, 50, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 1, 51, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 1, 52, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 2, 53, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 4, 54, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 4, 55, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `name`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Admission', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Annual Program', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Awards', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'Curriculum', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_category_translates`
--

CREATE TABLE `gallery_category_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `gallery_category_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_category_translates`
--

INSERT INTO `gallery_category_translates` (`id`, `gallery_category_id`, `locale`, `name`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Admission', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Annual Program', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Awards', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 4, 'en', 'Curriculum', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 1, 'bn', 'ভর্তি', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 2, 'bn', 'বার্ষিক প্রোগ্রাম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 3, 'bn', 'পুরস্কার', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 4, 'bn', 'পাঠ্যক্রম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `genders`
--

CREATE TABLE `genders` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genders`
--

INSERT INTO `genders` (`id`, `name`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Male', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Female', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gender_translates`
--

CREATE TABLE `gender_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `gender_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmeets`
--

CREATE TABLE `gmeets` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gmeet_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `submission_date` date DEFAULT NULL,
  `marks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `document_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homework_students`
--

CREATE TABLE `homework_students` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `homework_id` bigint UNSIGNED NOT NULL,
  `homework` bigint UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `marks` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `id_cards`
--

CREATE TABLE `id_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_date` date DEFAULT NULL,
  `frontside_bg_image` bigint UNSIGNED DEFAULT NULL,
  `backside_bg_image` bigint UNSIGNED DEFAULT NULL,
  `signature` bigint UNSIGNED DEFAULT NULL,
  `qr_code` bigint UNSIGNED DEFAULT NULL,
  `backside_description` text COLLATE utf8mb4_unicode_ci,
  `student_name` tinyint(1) NOT NULL DEFAULT '1',
  `admission_no` tinyint(1) NOT NULL DEFAULT '1',
  `roll_no` tinyint(1) NOT NULL DEFAULT '1',
  `class_name` tinyint(1) NOT NULL DEFAULT '1',
  `section_name` tinyint(1) NOT NULL DEFAULT '1',
  `blood_group` tinyint(1) NOT NULL DEFAULT '1',
  `dob` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

CREATE TABLE `incomes` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_head` bigint UNSIGNED NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `fees_collect_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_books`
--

CREATE TABLE `issue_books` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `book_id` bigint UNSIGNED NOT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `icon_class`, `direction`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'English', 'en', 'flag-icon flag-icon-us', 'ltr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Bangla', 'bn', 'flag-icon flag-icon-bd', 'ltr', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Arabic', 'ar', 'flag-icon flag-icon-sa', 'rtl', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `request_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `attachment_id` bigint UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_days` int NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks_grades`
--

CREATE TABLE `marks_grades` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent_from` double NOT NULL,
  `percent_upto` double NOT NULL,
  `point` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks_registers`
--

CREATE TABLE `marks_registers` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `exam_type_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `is_marksheet_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks_register_childrens`
--

CREATE TABLE `marks_register_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `marks_register_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mark` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mark_sheet_approvals`
--

CREATE TABLE `mark_sheet_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `exam_type_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_categories`
--

CREATE TABLE `member_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memories`
--

CREATE TABLE `memories` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_image_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_by` bigint UNSIGNED DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `rejected_by` int DEFAULT NULL,
  `pending_by` int DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memory_galleries`
--

CREATE TABLE `memory_galleries` (
  `id` bigint UNSIGNED NOT NULL,
  `memory_id` bigint UNSIGNED DEFAULT NULL,
  `gallery_image_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `receiver_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`, `branch_id`) VALUES
(1, '2013_08_03_072002_create_uploads_table', 1, 1),
(2, '2013_08_03_072003_create_roles_table', 1, 1),
(3, '2014_10_12_000000_create_users_table', 1, 1),
(4, '2014_10_12_100000_create_password_resets_table', 1, 1),
(5, '2019_08_19_000000_create_failed_jobs_table', 1, 1),
(6, '2019_12_14_000001_create_personal_access_tokens_table', 1, 1),
(7, '2022_07_19_045514_create_flag_icons_table', 1, 1),
(8, '2022_08_08_043550_create_permissions_table', 1, 1),
(9, '2022_08_16_103633_create_settings_table', 1, 1),
(10, '2022_08_17_092623_create_languages_table', 1, 1),
(11, '2022_10_04_044255_create_searches_table', 1, 1),
(12, '2022_10_13_064230_create_designations_table', 1, 1),
(13, '2023_02_20_101104_create_genders_table', 1, 1),
(14, '2023_02_22_044252_create_religions_table', 1, 1),
(15, '2023_02_22_053608_create_blood_groups_table', 1, 1),
(16, '2023_02_22_070416_create_sessions_table', 1, 1),
(17, '2023_02_22_100221_create_classes_table', 1, 1),
(18, '2023_02_22_102118_create_student_categories_table', 1, 1),
(19, '2023_02_22_115507_create_sections_table', 1, 1),
(20, '2023_02_23_042918_create_shifts_table', 1, 1),
(21, '2023_02_23_081806_create_subjects_table', 1, 1),
(22, '2023_02_23_095042_create_parent_guardians_table', 1, 1),
(23, '2023_02_23_113001_create_departments_table', 1, 1),
(24, '2023_02_24_124400_create_students_table', 1, 1),
(25, '2023_02_25_052716_create_class_rooms_table', 1, 1),
(26, '2023_02_25_071052_create_fees_groups_table', 1, 1),
(27, '2023_02_25_091226_create_fees_types_table', 1, 1),
(28, '2023_02_25_102359_create_fees_masters_table', 1, 1),
(29, '2023_02_27_045430_create_staff_table', 1, 1),
(30, '2023_02_28_051437_create_exam_types_table', 1, 1),
(31, '2023_02_28_065459_create_class_setups_table', 1, 1),
(32, '2023_02_28_065614_create_class_setup_childrens_table', 1, 1),
(33, '2023_02_28_090453_create_session_class_students_table', 1, 1),
(34, '2023_03_01_115144_create_subject_assigns_table', 1, 1),
(35, '2023_03_01_115229_create_subject_assign_childrens_table', 1, 1),
(36, '2023_03_03_114236_create_marks_grades_table', 1, 1),
(37, '2023_03_07_062402_create_exam_assigns_table', 1, 1),
(38, '2023_03_12_053023_create_fees_assigns_table', 1, 1),
(39, '2023_03_12_053024_create_fees_assign_childrens_table', 1, 1),
(40, '2023_03_12_053025_create_account_heads_table', 1, 1),
(41, '2023_03_12_053025_create_fees_collects_table', 1, 1),
(42, '2023_03_12_053026_create_incomes_table', 1, 1),
(43, '2023_03_12_090806_create_expenses_table', 1, 1),
(44, '2023_03_13_054359_create_marks_registers_table', 1, 1),
(45, '2023_03_13_101938_create_exam_assign_childrens_table', 1, 1),
(46, '2023_03_13_132615_create_marks_register_childrens_table', 1, 1),
(47, '2023_03_14_090857_create_fees_master_childrens_table', 1, 1),
(48, '2023_03_17_113815_create_promote_students_table', 1, 1),
(49, '2023_03_22_062320_create_time_schedules_table', 1, 1),
(50, '2023_03_22_062321_create_class_routines_table', 1, 1),
(51, '2023_03_24_053514_create_class_routine_childrens_table', 1, 1),
(52, '2023_04_07_045518_create_exam_routines_table', 1, 1),
(53, '2023_04_07_045719_create_exam_routine_childrens_table', 1, 1),
(54, '2023_04_27_105438_create_examination_settings_table', 1, 1),
(55, '2023_04_28_093751_create_sliders_table', 1, 1),
(56, '2023_04_28_105549_create_counters_table', 1, 1),
(57, '2023_04_30_070252_create_news_table', 1, 1),
(58, '2023_04_30_123236_create_examination_results_table', 1, 1),
(59, '2023_05_02_054153_create_gallery_categories_table', 1, 1),
(60, '2023_05_02_060903_create_galleries_table', 1, 1),
(61, '2023_05_03_033302_create_attendances_table', 1, 1),
(62, '2023_05_09_095159_create_events_table', 1, 1),
(63, '2023_05_18_095505_create_page_sections_table', 1, 1),
(64, '2023_05_21_104600_create_contact_infos_table', 1, 1),
(65, '2023_05_21_122123_create_department_contacts_table', 1, 1),
(66, '2023_05_22_045924_create_contacts_table', 1, 1),
(67, '2023_05_22_095703_create_subscribes_table', 1, 1),
(68, '2023_05_24_044715_create_abouts_table', 1, 1),
(69, '2023_06_14_071848_create_online_admissions_table', 1, 1),
(70, '2023_06_17_090920_create_book_categories_table', 1, 1),
(71, '2023_06_18_080708_create_books_table', 1, 1),
(72, '2023_06_18_091300_create_member_categories_table', 1, 1),
(73, '2023_06_18_091301_create_members_table', 1, 1),
(74, '2023_06_18_093638_create_issue_books_table', 1, 1),
(75, '2023_06_22_044425_create_homework_table', 1, 1),
(76, '2023_07_12_083329_add_user_type_column_in_searches_table', 1, 1),
(77, '2023_07_18_045644_create_question_groups_table', 1, 1),
(78, '2023_07_18_055005_create_question_banks_table', 1, 1),
(79, '2023_07_18_091545_create_question_bank_childrens_table', 1, 1),
(80, '2023_07_19_085237_create_online_exams_table', 1, 1),
(81, '2023_07_20_074247_create_online_exam_children_students_table', 1, 1),
(82, '2023_07_20_074318_create_online_exam_children_questions_table', 1, 1),
(83, '2023_07_26_041901_create_answers_table', 1, 1),
(84, '2023_07_26_041949_create_answer_childrens_table', 1, 1),
(85, '2023_07_28_150210_create_currencies_table', 1, 1),
(86, '2023_08_02_132147_add_payment_gateway_and_transaction_id_in_fees_collects_table', 1, 1),
(87, '2023_08_30_111142_create_subscriptions_table', 1, 1),
(88, '2023_11_10_120311_create_homework_students_table', 1, 1),
(89, '2023_11_14_155008_create_id_cards_table', 1, 1),
(90, '2023_11_15_152219_create_certificates_table', 1, 1),
(91, '2023_11_22_113507_create_gmeets_table', 1, 1),
(92, '2023_11_23_122832_create_notice_boards_table', 1, 1),
(93, '2023_11_27_122348_create_sms_mail_templates_table', 1, 1),
(94, '2023_11_28_123854_create_sms_mail_logs_table', 1, 1),
(95, '2024_02_28_085432_create_student_absent_notifications_table', 1, 1),
(96, '2024_02_28_102602_create_system_notifications_table', 1, 1),
(97, '2024_02_28_110330_create_jobs_table', 1, 1),
(98, '2024_02_29_050637_create_notification_settings_table', 1, 1),
(99, '2024_03_04_064053_create_pages_table', 1, 1),
(100, '2024_03_06_123332_create_slider_translates_table', 1, 1),
(101, '2024_03_07_074949_create_online_admission_settings_table', 1, 1),
(102, '2024_03_07_141027_create_page_translates_table', 1, 1),
(103, '2024_03_07_172038_create_section_translates_table', 1, 1),
(104, '2024_03_08_113402_create_about_translates_table', 1, 1),
(105, '2024_03_08_124638_create_counter_translates_table', 1, 1),
(106, '2024_03_08_145357_create_contact_info_translates_table', 1, 1),
(107, '2024_03_08_153350_create_department_contact_translates_table', 1, 1),
(108, '2024_03_08_155742_create_news_translates_table', 1, 1),
(109, '2024_03_08_163636_create_event_translates_table', 1, 1),
(110, '2024_03_14_061235_create_online_admission_fees_assigns_table', 1, 1),
(111, '2024_03_14_085756_create_online_admission_payments_table', 1, 1),
(112, '2024_03_19_033526_create_gallery_category_translates_table', 1, 1),
(113, '2024_03_19_094031_create_notice_board_translates_table', 1, 1),
(114, '2024_03_19_104803_create_setting_translates_table', 1, 1),
(115, '2024_03_28_070846_create_gender_translates_table', 1, 1),
(116, '2024_03_28_075421_create_religon_translates_table', 1, 1),
(117, '2024_04_01_035342_create_class_translates_table', 1, 1),
(118, '2024_04_01_035412_create_class_section_translates_table', 1, 1),
(119, '2024_04_01_061856_create_session_translates_table', 1, 1),
(120, '2024_04_02_052447_create_shift_translates_table', 1, 1),
(121, '2024_08_30_151926_add_columns_to_students_table', 1, 1),
(122, '2024_08_30_152016_add_columns_to_parent_guardians_table', 1, 1),
(123, '2024_09_03_121530_add_fields_to_users_table', 1, 1),
(124, '2025_01_01_122002_add_branch_id_to_all_tables', 1, 1),
(125, '2025_01_13_054622_add_new_columns_notice_board_table', 1, 1),
(126, '2025_01_14_115157_add_manager_id_column_to_departments_table', 1, 1),
(127, '2025_01_14_124219_add_department_id_column_to_student_table', 1, 1),
(128, '2025_01_14_132943_add_department_id_column_to_notice_board_table', 1, 1),
(129, '2025_01_16_131514_add_marksheet_published_column_to_marks_registers_table', 1, 1),
(130, '2025_01_16_151441_add_health_status_family_rank_siblings_column_to_students_table', 1, 1),
(131, '2025_01_16_160558_add_place_of_work_and_position_column_to_parent_guardians', 1, 1),
(132, '2025_05_12_135041_create_sibling_fees_discounts_table', 1, 1),
(133, '2025_05_14_115833_add_siblings_discount_to_students_table', 1, 1),
(134, '2025_05_14_122836_create_assign_fees_discounts_table', 1, 1),
(135, '2025_05_14_181851_create_early_payment_discounts_table', 1, 1),
(136, '2025_05_22_114443_create_mark_sheet_approvals_table', 1, 1),
(137, '2025_05_23_094821_create_leave_types_table', 1, 1),
(138, '2025_05_23_094837_create_leave_requests_table', 1, 1),
(139, '2025_05_23_113354_create_subject_attendances_table', 1, 1),
(140, '2023_06_21_150627_create_messages_table', 2, 1),
(141, '2024_10_17_070044_create_forum_posts_table', 3, 1),
(142, '2024_10_17_072823_create_forum_post_comments_table', 3, 1),
(143, '2024_10_21_054332_create_memories_table', 3, 1),
(144, '2024_10_21_054342_create_memory_galleries_table', 3, 1),
(145, '2024_12_31_160536_create_branches_table', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `description`, `date`, `publish_date`, `upload_id`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, '20+ Academic Curriculum We Done!0', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-02', '2025-06-03', 19, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, '20+ Academic Curriculum We Done!1', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-01', '2025-06-03', 20, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, '20+ Academic Curriculum We Done!2', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-31', '2025-06-03', 21, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, '20+ Academic Curriculum We Done!3', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-30', '2025-06-03', 22, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, '20+ Academic Curriculum We Done!4', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-29', '2025-06-03', 23, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, '20+ Academic Curriculum We Done!5', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-28', '2025-06-03', 24, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, '20+ Academic Curriculum We Done!6', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-27', '2025-06-03', 25, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, '20+ Academic Curriculum We Done!7', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-26', '2025-06-03', 26, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, '20+ Academic Curriculum We Done!8', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-25', '2025-06-03', 27, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, '20+ Academic Curriculum We Done!9', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-24', '2025-06-03', 28, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, '20+ Academic Curriculum We Done!10', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-23', '2025-06-03', 29, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, '20+ Academic Curriculum We Done!11', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-22', '2025-06-03', 30, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, '20+ Academic Curriculum We Done!12', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-05-21', '2025-06-03', 31, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news_translates`
--

CREATE TABLE `news_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `news_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_translates`
--

INSERT INTO `news_translates` (`id`, `news_id`, `locale`, `title`, `description`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', '20+ Academic Curriculum We Done!0', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', '20+ Academic Curriculum We Done!1', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', '20+ Academic Curriculum We Done!2', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 4, 'en', '20+ Academic Curriculum We Done!3', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 5, 'en', '20+ Academic Curriculum We Done!4', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 6, 'en', '20+ Academic Curriculum We Done!5', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 7, 'en', '20+ Academic Curriculum We Done!6', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 8, 'en', '20+ Academic Curriculum We Done!7', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 9, 'en', '20+ Academic Curriculum We Done!8', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 10, 'en', '20+ Academic Curriculum We Done!9', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 11, 'en', '20+ Academic Curriculum We Done!10', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 12, 'en', '20+ Academic Curriculum We Done!11', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 13, 'en', '20+ Academic Curriculum We Done!12', 'Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 1, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!0', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 2, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!1', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 3, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!2', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 4, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!3', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 5, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!4', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 6, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!5', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 7, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!6', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 8, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!7', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 9, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!8', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 10, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!9', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 11, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!10', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(25, 12, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!11', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(26, 13, 'bn', '20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!12', 'অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notice_boards`
--

CREATE TABLE `notice_boards` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `publish_date` datetime NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` bigint UNSIGNED DEFAULT NULL,
  `is_visible_web` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `visible_to` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notice_board_translates`
--

CREATE TABLE `notice_board_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `notice_board_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE `notification_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e=email, s=SMS, w=web, a=app',
  `reciever` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template` longtext COLLATE utf8mb4_unicode_ci,
  `shortcode` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_settings`
--

INSERT INTO `notification_settings` (`id`, `event`, `host`, `reciever`, `subject`, `template`, `shortcode`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Student_Attendance', '{\"email\":1,\"sms\":1,\"web\":1,\"app\":1}', '{\"Student\":1,\"Parent\":1}', '{\"Student\":\"Student Attendance\",\"Parent\":\"Student Attendance\"}', '{\"Student\":{\"Email\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"SMS\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"Web\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"App\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\"},\"Parent\":{\"Email\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"SMS\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name].\",\"Web\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"App\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\"}}', '{\"Student\":\"[student_name], [class], [section], [admission_no], [roll_no], [attendance_type], [attendance_date], [school_name]\",\"Parent\":\"[guardian_name], [student_name], [class], [section], [admission_no], [roll_no], [attendance_type], [attendance_date], [school_name]\"}', '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(2, 'Online_Admission', '{\"email\":1,\"sms\":1,\"web\":1,\"app\":1}', '{\"Super Admin\":1,\"Student\":1,\"Parent\":1}', '{\"Super Admin\":\"Student Online Admission\",\"Student\":\"Student Online Admission\",\"Parent\":\"Student Online Admission\"}', '{\"Super Admin\":{\"Email\":\"Dear Super Admin,\\n                         [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\",\"SMS\":\"Dear Super Admin,\\n                        [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\",\"Web\":\"Dear Super Admin,\\n                        [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\",\"App\":\"Dear Super Admin,\\n                        [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\"},\"Student\":{\"Email\":\"Dear [student_name],\\n                        You are admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [student_email] , Default Password : 123456 Thank You for choosing [school_name] .\",\"SMS\":\"Dear [student_name],\\n                        You are admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [student_email]  , Default Password : 123456 Thank You for choosing [school_name] .\",\"Web\":\"You are admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name].\",\"App\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\"},\"Parent\":{\"Email\":\"Dear [parent_name],\\n                        Your child [student_name] admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email]  , Default Password : 123456 Thank You for choosing [school_name]\",\"SMS\":\"Dear [parent_name],\\n                        Your child [student_name] admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email]  , Default Password : 123456 Thank You for choosing [school_name]\",\"Web\":\"Dear [parent_name],\\n                        Your child [student_name] admitted on class : [class_name] , section : [section_name] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email]  , Default Password : 123456 Thank You for choosing [school_name]\",\"App\":\" Your child [student_name] admitted on class : [class_name] , section : [section_name] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email] , Default Password : 123456 Thank You for choosing [school_name]\"}}', '{\"Super Admin\":\"[student_name], [class], [section], [admission_no], [admission_date], [school_name]\",\"Student\":\"[parent_name], [student_name], [class], [section], [admission_no], [student_email], [phone] , [school_name]\",\"Parent\":\"[parent_name], [student_name], [class], [section], [admission_no], [parent_email], [phone] , [school_name]\"}', '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `online_admissions`
--

CREATE TABLE `online_admissions` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` tinyint NOT NULL DEFAULT '0' COMMENT '0 = no_need, 2 = need, 1 = done',
  `payslip_image_id` bigint UNSIGNED DEFAULT NULL,
  `fees_assign_id` bigint UNSIGNED DEFAULT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `religion_id` bigint UNSIGNED DEFAULT NULL,
  `gender_id` bigint UNSIGNED DEFAULT NULL,
  `dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_image_id` bigint UNSIGNED DEFAULT NULL,
  `previous_school` tinyint NOT NULL DEFAULT '0',
  `previous_school_info` text COLLATE utf8mb4_unicode_ci,
  `previous_school_image_id` bigint UNSIGNED DEFAULT NULL,
  `guardian_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gurdian_image_id` bigint UNSIGNED DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_image_id` bigint UNSIGNED DEFAULT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_image_id` bigint UNSIGNED DEFAULT NULL,
  `upload_documents` longtext COLLATE utf8mb4_unicode_ci,
  `place_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpr_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spoken_lang_at_home` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residance_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_admission_fees_assigns`
--

CREATE TABLE `online_admission_fees_assigns` (
  `id` bigint UNSIGNED NOT NULL,
  `fees_group_id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_admission_payments`
--

CREATE TABLE `online_admission_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `admission_id` bigint UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `payment_method` tinyint DEFAULT NULL,
  `fees_assign_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_admission_settings`
--

CREATE TABLE `online_admission_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'online_admission',
  `field` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '1',
  `is_required` tinyint(1) DEFAULT '0',
  `is_system_required` tinyint(1) DEFAULT '0',
  `field_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `online_admission_settings`
--

INSERT INTO `online_admission_settings` (`id`, `type`, `field`, `is_show`, `is_required`, `is_system_required`, `field_value`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'online_admission', 'student_first_name', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(2, 'online_admission', 'student_last_name', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(3, 'online_admission', 'student_phone', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(4, 'online_admission', 'student_email', 1, 1, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(5, 'online_admission', 'student_dob', 1, 1, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(6, 'online_admission', 'student_document', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(7, 'online_admission', 'student_photo', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(8, 'online_admission', 'session', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(9, 'online_admission', 'class', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(10, 'online_admission', 'section', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(11, 'online_admission', 'shift', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(12, 'online_admission', 'gender', 1, 1, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(13, 'online_admission', 'religion', 1, 1, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(14, 'online_admission', 'previous_school', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(15, 'online_admission', 'previous_school_info', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(16, 'online_admission', 'previous_school_doc', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(17, 'online_admission', 'admission_payment', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(18, 'online_admission', 'admission_payment_info', 1, 0, 0, 'Enter Payment Information ,Bank Name . Swift Code, Account Number, Account Branch Information Or Any kind of special note you can wrote here ', '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(19, 'online_admission', 'place_of_birth', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(20, 'online_admission', 'nationality', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(21, 'online_admission', 'cpr_no', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(22, 'online_admission', 'spoken_lang_at_home', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(23, 'online_admission', 'residance_address', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(24, 'online_admission', 'father_nationality', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(25, 'online_admission', 'gurdian_name', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(26, 'online_admission', 'gurdian_email', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(27, 'online_admission', 'gurdian_phone', 1, 1, 1, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(28, 'online_admission', 'gurdian_photo', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(29, 'online_admission', 'gurdian_profession', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(30, 'online_admission', 'father_name', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(31, 'online_admission', 'father_phone', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(32, 'online_admission', 'father_photo', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(33, 'online_admission', 'father_profession', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(34, 'online_admission', 'mother_name', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(35, 'online_admission', 'mother_phone', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(36, 'online_admission', 'mother_photo', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1),
(37, 'online_admission', 'mother_profession', 1, 0, 0, NULL, '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `online_exams`
--

CREATE TABLE `online_exams` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_type_id` bigint UNSIGNED DEFAULT NULL,
  `total_mark` double DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `published` datetime DEFAULT NULL,
  `question_group_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_children_questions`
--

CREATE TABLE `online_exam_children_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `online_exam_id` bigint UNSIGNED NOT NULL,
  `question_bank_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_children_students`
--

CREATE TABLE `online_exam_children_students` (
  `id` bigint UNSIGNED NOT NULL,
  `online_exam_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `menu_show` enum('header','footer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `slug`, `content`, `active_status`, `menu_show`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Privacy Policy', 'privacy_policy', '<div class=\"elementor-element elementor-element-790b948d elementor-widget elementor-widget-text-editor\" data-id=\"790b948d\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: relative; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; color: var( --e-global-color-text ); font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; width: 1280px; margin-bottom: 0px; z-index: 3;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); padding: 0px 0px 100px;\"><h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Last updated: 22 November, 2025</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Introduction</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Onest Schooled Management System values your privacy. This policy explains how we collect, use, and safeguard your data when you use our app.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Information We Collect</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">User Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Names, email addresses, contact details, roles (student, teacher, parent, or admin).</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Operational Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Attendance, grades, homework, library usage, and fee transactions.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Device Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Device type, operating system, and logs for app functionality.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. How We Use Your Information</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To facilitate administrative operations, such as admission, fee collection, attendance, and academic management.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To provide personalized dashboards for students, teachers, and parents.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To enhance user experience and app security.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Sharing Your Information</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We only share data with:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">School administrators for operational purposes.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Authorities when required by law.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Data Security</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We implement industry-standard security measures, including encryption and regular audits.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">5. Your Rights</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Access or modify personal data through the user portal.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Request deletion of your data by contacting support (subject to operational constraints).</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">6. Policy Updates</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We may update this policy. Changes will be notified via email or app alerts.</span></p><p style=\"margin-bottom: 0.9rem;\"><span id=\"docs-internal-guid-0452a37e-7fff-0696-3df4-c342ecd0bf24\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Contact Us</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">For questions or concerns, reach us at sales.onesttech.com</span></p></div></div><div class=\"elementor-element elementor-element-4f36e2b7 elementor-widget__width-initial elementor-absolute elementor-widget elementor-widget-spacer\" data-id=\"4f36e2b7\" data-element_type=\"widget\" data-settings=\"{&quot;_position&quot;:&quot;absolute&quot;}\" data-widget_type=\"spacer.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: absolute; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; z-index: 1; width: var( --container-widget-width, 100vw ); margin-bottom: 0px; max-width: 100vw; --spacer-size: 100vh; --container-widget-width: 100vw; --container-widget-flex-grow: 0; top: -0.5px; right: 551px; color: rgb(51, 51, 51); font-family: -apple-system, &quot;system-ui&quot;, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); background-color: transparent; background-image: radial-gradient(rgba(44, 255, 0, 0.06) 0%, rgba(0, 0, 0, 0) 70%);\"><div class=\"elementor-spacer\"></div></div></div>', 1, 'footer', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(2, 'Support Policy', 'support_policy', '<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Scope of Support</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We provide assistance for:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Setup and configuration.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Troubleshooting technical issues.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Feature-related queries.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. Support Channels</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Email: [sales.onesttech.com]</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Whatsapp: [+880 1959-335555]</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Response Time</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">General Queries: Response within 48 business hours.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Critical Issues (e.g., service downtime): Response within 24 hours.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Updates &amp; Maintenance</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Regular updates are provided for feature enhancements and bug fixes. Notification will be sent before scheduled maintenance.</span></p><p><span id=\"docs-internal-guid-500ba9c7-7fff-1baf-821f-835a1517b432\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Let me know if you’d like to further customize these policies or add more details!</span></p>', 1, 'footer', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(3, 'Terms & Conditions', 'terms_conditions', '<p><b>Terms and Conditions of Use for Ischool Management System Management Software\n                        </b></p><p><b><br></b>\n                                                    These Terms and Conditions govern your access to and use of the School Management Software , provided by Ischool Management System . By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.\n                        </p><p><br></p><p><b>\n                                                    Acceptance of Terms: </b>By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to all the terms and conditions of this agreement, you must not use the Software.</p><p><br></p><p>\n                        </p><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Use of the Software:</b><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software is provided solely for the purpose of managing educational institutions, including but not limited to schools, colleges, and universities. You agree not to use the Software for any illegal or unauthorized purpose.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><b>User Accounts: </b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">You may need to create an account to access certain features of the Software. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n\n                                                    Privacy:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We are committed to protecting your privacy. Our Privacy Policy outlines how we collect, use, and disclose your personal information. By using the Software, you consent to the collection, use, and disclosure of your personal information as described in the Privacy Policy.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Intellectual Property:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software and its original content, features, and functionality are owned by Ischool Management System and are protected by international copyright, trademark, patent, trade secret, and other intellectual property or proprietary rights laws.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Limitation of Liability:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> In no event shall\n                                                    Ischool Management System be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or goodwill, arising from the use of or inability to use the Software.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>Changes to Terms:</b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Governing Law: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">These Terms shall be governed by and construed in accordance with the laws of United Stated Of America , without regard to its conflict of law provisions.\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Contact Us: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">If you have any questions about these Terms, please contact us at&nbsp; Ones .\n\n                                                    By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.</span><br></p>', 1, 'footer', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(4, 'Our Missions', 'our_missions', '<p>At Ischool Management System , we are dedicated to providing a nurturing and enriching educational environment that empowers students to reach their full potential. Our mission is to foster academic excellence, character development, and lifelong learning in every student we serve.</p><p><br></p><h3><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Our Core Values</b></h3><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><br></b><br></p><p><b>\n                        1. Excellence:\n                        </b> We are committed to excellence in all aspects of education, striving to provide the highest quality teaching, resources, and support to our students.\n                            </p><p><br></p><p><b>\n                        2. Integrity:\n                        </b> We uphold the highest standards of integrity, honesty, and ethical behavior in our interactions with students, parents, staff, and the community.\n                            </p><p><br></p><p><b>\n                        3. Respect:</b>\n                        We foster a culture of respect, valuing the unique abilities, perspectives, and backgrounds of each individual within our school community.\n                            </p><p><br></p><p><b>\n                        4. Collaboration:\n                            </b>  We believe in the power of collaboration and teamwork, working closely with students, parents, educators, and the community to achieve our shared goals.\n                        </p><p><br></p><p><b>\n                        5. Innovation:</b>\n                        We embrace innovation and creativity, continuously seeking new and effective ways to enhance the learning experience and meet the evolving needs of our students.</p><p><br></p><p>\n                            </p><p style=\"text-align: center;\"><b><u>\n                        Our Goals</u></b></p><p style=\"text-align: center;\"><b><u><br></u></b></p><p style=\"text-align: center;\"><b><u>\n                        </u></b></p><ul><li><b>                            1. Academic Excellence:\n                        </b>  We strive to provide rigorous academic programs that challenge and inspire students to achieve their highest academic potential.</li></ul><p><br></p><ul><li><b>\n                        2. Character Development:</b>\n                        We are committed to fostering the development of strong character traits such as honesty, responsibility, compassion, and resilience in our students.</li></ul><p><br></p><ul><li><b>\n                        3. Lifelong Learning:\n                        </b> We aim to instill a love of learning and a growth mindset in our students, empowering them to become lifelong learners who are curious, adaptable, and eager to explore new ideas and opportunities.</li></ul><p><br></p><ul><li><b>\n                        4 Community Engagement:\n                        </b> We seek to actively engage with parents, families, and the broader community to create a supportive and inclusive learning environment that nurtures the holistic development of our students.\n\n                        Join Us in Our Mission\n                        We invite you to join us in our mission to inspire and empower the next generation of leaders, thinkers, and innovators. </li></ul><p><br></p><p>Together, we can make a difference in the lives of our students and create a brighter future for all.\n\n                        This sample content provides an overview of the schools mission, core values, goals, and an invitation for stakeholders to join in achieving those goals. You can customize it further to align with the specific mission and values of your school management application.</p>', 1, 'footer', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_sections`
--

INSERT INTO `page_sections` (`id`, `key`, `name`, `description`, `upload_id`, `data`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'social_links', '', '', NULL, '[{\"name\":\"Facebook\",\"icon\":\"fab fa-facebook-f\",\"link\":\"http:\\/\\/www.facebook.com\"},{\"name\":\"Twitter\",\"icon\":\"fab fa-twitter\",\"link\":\"http:\\/\\/www.twitter.com\"},{\"name\":\"Pinterest\",\"icon\":\"fab fa-pinterest-p\",\"link\":\"http:\\/\\/www.pinterest.com\"},{\"name\":\"Instagram\",\"icon\":\"fab fa-instagram\",\"link\":\"http:\\/\\/www.instagram.com\"}]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'statement', 'Statement Of Onest Schooleded', '', 5, '[{\"title\":\"Mission Statement\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Read More\"},{\"title\":\"Vision Statement\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet Read More\"}]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'study_at', 'Study at Onest Schooleded', 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet', 6, '[{\"icon\":8,\"title\":\"Out Prospects\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\"},{\"icon\":9,\"title\":\"Out Prospects\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\"},{\"icon\":10,\"title\":\"Out Prospects\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\"}]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'explore', 'Explore Onest Schoooled', '\"We Educate Knowledge & Essential Skills\" is a phrase that emphasizes the importance of both theoretical knowledge', 7, '[{\"tab\":\"Campus Life\",\"title\":\"Campus Life\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"},{\"tab\":\"Academic\",\"title\":\"Academic\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"},{\"tab\":\"Athletics\",\"title\":\"Athletics\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"},{\"tab\":\"School\",\"title\":\"School\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"}]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'why_choose_us', 'Excellence In Teaching And Learning', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will frequently occurs that pleasures. Provide Endless Opportunities', NULL, '[\"A higher education qualification\",\"Better career prospects\",\"Better career prospects\",\"Better career prospects\"]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'academic_curriculum', '20+ Academic Curriculum', 'Onsest Schooled is home to more than 20,000 students and 230,000 alumni with a wide variety of interests, ages and backgrounds, the University reflects the city’s dynamic mix of populations.', NULL, '[\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\"]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'coming_up', 'What’s Coming Up?', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.', NULL, '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'news', 'Latest From Our Blog', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.', NULL, '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 'our_gallery', 'Our Gallery', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.', NULL, '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 'contact_information', 'Find Our <br> Contact Information', '', NULL, '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 'department_contact_information', 'Contact By Department', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will', NULL, '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 'our_teachers', 'Our Featured Teachers', '', NULL, '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `page_translates`
--

CREATE TABLE `page_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `page_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_translates`
--

INSERT INTO `page_translates` (`id`, `page_id`, `locale`, `name`, `content`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Privacy Policy', '<div class=\"elementor-element elementor-element-790b948d elementor-widget elementor-widget-text-editor\" data-id=\"790b948d\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: relative; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; color: var( --e-global-color-text ); font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; width: 1280px; margin-bottom: 0px; z-index: 3;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); padding: 0px 0px 100px;\"><h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Last updated: 22 November, 2025</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Introduction</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Onest Schooled Management System values your privacy. This policy explains how we collect, use, and safeguard your data when you use our app.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Information We Collect</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">User Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Names, email addresses, contact details, roles (student, teacher, parent, or admin).</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Operational Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Attendance, grades, homework, library usage, and fee transactions.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Device Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Device type, operating system, and logs for app functionality.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. How We Use Your Information</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To facilitate administrative operations, such as admission, fee collection, attendance, and academic management.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To provide personalized dashboards for students, teachers, and parents.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To enhance user experience and app security.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Sharing Your Information</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We only share data with:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">School administrators for operational purposes.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Authorities when required by law.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Data Security</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We implement industry-standard security measures, including encryption and regular audits.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">5. Your Rights</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Access or modify personal data through the user portal.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Request deletion of your data by contacting support (subject to operational constraints).</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">6. Policy Updates</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We may update this policy. Changes will be notified via email or app alerts.</span></p><p style=\"margin-bottom: 0.9rem;\"><span id=\"docs-internal-guid-0452a37e-7fff-0696-3df4-c342ecd0bf24\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Contact Us</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">For questions or concerns, reach us at sales.onesttech.com</span></p></div></div><div class=\"elementor-element elementor-element-4f36e2b7 elementor-widget__width-initial elementor-absolute elementor-widget elementor-widget-spacer\" data-id=\"4f36e2b7\" data-element_type=\"widget\" data-settings=\"{&quot;_position&quot;:&quot;absolute&quot;}\" data-widget_type=\"spacer.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: absolute; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; z-index: 1; width: var( --container-widget-width, 100vw ); margin-bottom: 0px; max-width: 100vw; --spacer-size: 100vh; --container-widget-width: 100vw; --container-widget-flex-grow: 0; top: -0.5px; right: 551px; color: rgb(51, 51, 51); font-family: -apple-system, &quot;system-ui&quot;, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); background-color: transparent; background-image: radial-gradient(rgba(44, 255, 0, 0.06) 0%, rgba(0, 0, 0, 0) 70%);\"><div class=\"elementor-spacer\"></div></div></div>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(2, 2, 'en', 'Support Policy', '<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Scope of Support</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We provide assistance for:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Setup and configuration.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Troubleshooting technical issues.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Feature-related queries.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. Support Channels</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Email: [sales.onesttech.com]</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Whatsapp: [+880 1959-335555]</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Response Time</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">General Queries: Response within 48 business hours.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Critical Issues (e.g., service downtime): Response within 24 hours.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Updates &amp; Maintenance</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Regular updates are provided for feature enhancements and bug fixes. Notification will be sent before scheduled maintenance.</span></p><p><span id=\"docs-internal-guid-500ba9c7-7fff-1baf-821f-835a1517b432\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Let me know if you’d like to further customize these policies or add more details!</span></p>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(3, 3, 'en', 'Terms & Conditions', '<p><b>Terms and Conditions of Use for Ischool Management System Management Software\n                        </b></p><p><b><br></b>\n                                                    These Terms and Conditions govern your access to and use of the School Management Software , provided by Ischool Management System . By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.\n                        </p><p><br></p><p><b>\n                                                    Acceptance of Terms: </b>By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to all the terms and conditions of this agreement, you must not use the Software.</p><p><br></p><p>\n                        </p><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Use of the Software:</b><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software is provided solely for the purpose of managing educational institutions, including but not limited to schools, colleges, and universities. You agree not to use the Software for any illegal or unauthorized purpose.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><b>User Accounts: </b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">You may need to create an account to access certain features of the Software. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n\n                                                    Privacy:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We are committed to protecting your privacy. Our Privacy Policy outlines how we collect, use, and disclose your personal information. By using the Software, you consent to the collection, use, and disclosure of your personal information as described in the Privacy Policy.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Intellectual Property:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software and its original content, features, and functionality are owned by Ischool Management System and are protected by international copyright, trademark, patent, trade secret, and other intellectual property or proprietary rights laws.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Limitation of Liability:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> In no event shall\n                                                    Ischool Management System be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or goodwill, arising from the use of or inability to use the Software.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>Changes to Terms:</b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Governing Law: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">These Terms shall be governed by and construed in accordance with the laws of United Stated Of America , without regard to its conflict of law provisions.\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Contact Us: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">If you have any questions about these Terms, please contact us at&nbsp; Ones .\n\n                                                    By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.</span><br></p>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(4, 4, 'en', 'Our Missions', '<p>At Ischool Management System , we are dedicated to providing a nurturing and enriching educational environment that empowers students to reach their full potential. Our mission is to foster academic excellence, character development, and lifelong learning in every student we serve.</p><p><br></p><h3><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Our Core Values</b></h3><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><br></b><br></p><p><b>\n                        1. Excellence:\n                        </b> We are committed to excellence in all aspects of education, striving to provide the highest quality teaching, resources, and support to our students.\n                            </p><p><br></p><p><b>\n                        2. Integrity:\n                        </b> We uphold the highest standards of integrity, honesty, and ethical behavior in our interactions with students, parents, staff, and the community.\n                            </p><p><br></p><p><b>\n                        3. Respect:</b>\n                        We foster a culture of respect, valuing the unique abilities, perspectives, and backgrounds of each individual within our school community.\n                            </p><p><br></p><p><b>\n                        4. Collaboration:\n                            </b>  We believe in the power of collaboration and teamwork, working closely with students, parents, educators, and the community to achieve our shared goals.\n                        </p><p><br></p><p><b>\n                        5. Innovation:</b>\n                        We embrace innovation and creativity, continuously seeking new and effective ways to enhance the learning experience and meet the evolving needs of our students.</p><p><br></p><p>\n                            </p><p style=\"text-align: center;\"><b><u>\n                        Our Goals</u></b></p><p style=\"text-align: center;\"><b><u><br></u></b></p><p style=\"text-align: center;\"><b><u>\n                        </u></b></p><ul><li><b>                            1. Academic Excellence:\n                        </b>  We strive to provide rigorous academic programs that challenge and inspire students to achieve their highest academic potential.</li></ul><p><br></p><ul><li><b>\n                        2. Character Development:</b>\n                        We are committed to fostering the development of strong character traits such as honesty, responsibility, compassion, and resilience in our students.</li></ul><p><br></p><ul><li><b>\n                        3. Lifelong Learning:\n                        </b> We aim to instill a love of learning and a growth mindset in our students, empowering them to become lifelong learners who are curious, adaptable, and eager to explore new ideas and opportunities.</li></ul><p><br></p><ul><li><b>\n                        4 Community Engagement:\n                        </b> We seek to actively engage with parents, families, and the broader community to create a supportive and inclusive learning environment that nurtures the holistic development of our students.\n\n                        Join Us in Our Mission\n                        We invite you to join us in our mission to inspire and empower the next generation of leaders, thinkers, and innovators. </li></ul><p><br></p><p>Together, we can make a difference in the lives of our students and create a brighter future for all.\n\n                        This sample content provides an overview of the schools mission, core values, goals, and an invitation for stakeholders to join in achieving those goals. You can customize it further to align with the specific mission and values of your school management application.</p>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1);
INSERT INTO `page_translates` (`id`, `page_id`, `locale`, `name`, `content`, `created_at`, `updated_at`, `branch_id`) VALUES
(5, 1, 'bn', 'গোপনীয়তা নীতি', '<div class=\"elementor-element elementor-element-790b948d elementor-widget elementor-widget-text-editor\" data-id=\"790b948d\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\" শৈলী =\"--ফ্লেক্স-নির্দেশ: প্রাথমিক; --ফ্লেক্স-র্যাপ: প্রাথমিক; --জাস্টিফাই-সামগ্রী: প্রাথমিক; --অ্যালাইন-আইটেম: প্রাথমিক; --অ্যালাইন-সামগ্রী: প্রাথমিক; --গ্যাপ: প্রাথমিক; -- ফ্লেক্স-বেসিস: প্রাথমিক; --ফ্লেক্স-গ্রো: প্রাথমিক; --ফ্লেক্স-সঙ্কুচিত: প্রাথমিক; --অর্ডার: প্রাথমিক; --অ্যালাইন-স্ব: প্রাথমিক; ফ্লেক্স-বেসিস: var(--ফ্লেক্স-বেসিস); ফ্লেক্স -গ্রো: var(--ফ্লেক্স-গ্রো); ফ্লেক্স-সঙ্কুচিত: var(--ফ্লেক্স-সঙ্কুচিত); অর্ডার: var(--অর্ডার); align-self: var(--align-self); flex-direction : var(--ফ্লেক্স-ডাইরেকশন); ফ্লেক্স-র্যাপ: var(--ফ্লেক্স-র্যাপ); ন্যায্যতা-সামগ্রী: var(--জাস্টিফাই-কন্টেন্ট); সারিবদ্ধ-আইটেম: var(--সারিবদ্ধ-আইটেম); সারিবদ্ধ -সামগ্রী: var(--align-content); gap: var(--gap); অবস্থান: আপেক্ষিক; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper- পেজিনেশন-বুলেট-আকার: 6px; --swiper-পৃষ্ঠা-পৃষ্ঠা-বুলেট-অনুভূমিক-ব্যবধান: 6px; --উইজেট-স্পেসিং: 20px; রঙ: var( --e-global-color-text); ফন্ট-পরিবার: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; প্রস্থ: 1280px; মার্জিন-নিচ: 0px; z-index: 3;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e -রূপান্তর-পরিবর্তন-সময়কাল,.4s); প্যাডিং: 0px 0px 100px;\"><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">এই গোপনীয়তা নীতি নথিতে এমন ধরনের তথ্য রয়েছে যা সংগৃহীত এবং রেকর্ড করা হয় Ischool Management System এবং আমরা কিভাবে এটি ব্যবহার করি। এ Ischool Management System, থেকে অ্যাক্সেসযোগ্যhttp://127.0.0.1:8000 , আমাদের প্রধান অগ্রাধিকারগুলির মধ্যে একটি হল আমাদের দর্শকদের গোপনীয়তা। এই গোপনীয়তা নীতি নথিতে তথ্যের প্রকার রয়েছে যা দ্বারা সংগৃহীত এবং রেকর্ড করা হয় Ischool Management System&nbsp;এবং আমরা এটি কীভাবে ব্যবহার করি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আপনার যদি অতিরিক্ত প্রশ্ন থাকে বা আমাদের গোপনীয়তা নীতি সম্পর্কে আরও তথ্যের প্রয়োজন হয়, তাহলে আমাদের সাথে যোগাযোগ করতে দ্বিধা করবেন না।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">এই গোপনীয়তা নীতি শুধুমাত্র আমাদের অনলাইন ক্রিয়াকলাপের ক্ষেত্রে প্রযোজ্য এবং আমাদের ওয়েবসাইটের দর্শকদের জন্য তারা যে তথ্য শেয়ার করেছেন এবং/অথবা সংগ্রহ করেছেন তাদের জন্য বৈধ Ischool Management System. এই নীতিটি এই ওয়েবসাইট ছাড়া অফলাইনে বা চ্যানেলের মাধ্যমে সংগ্রহ করা কোনো তথ্যের ক্ষেত্রে প্রযোজ্য নয়।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit; \"><span data-preserver-spaces=\"true\">সম্মতি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">এর দ্বারা আমাদের ওয়েবসাইট ব্যবহার করে, আপনি এতদ্বারা আমাদের গোপনীয়তা নীতিতে সম্মত হন এবং এর শর্তাবলীতে সম্মত হন।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\" <span data-preserver-spaces=\"true\">আমরা যে তথ্য সংগ্রহ করি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\"> আপনাকে যে ব্যক্তিগত তথ্য প্রদান করতে বলা হয়েছে এবং কেন আপনাকে এটি প্রদান করতে বলা হয়েছে, আমরা যখন আপনাকে আপনার ব্যক্তিগত তথ্য প্রদান করতে বলব তখনই আপনাকে স্পষ্ট করে দেওয়া হবে।</span></p><p style =\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আপনি যদি আমাদের সাথে সরাসরি যোগাযোগ করেন, তাহলে আমরা আপনার সম্পর্কে অতিরিক্ত তথ্য পেতে পারি যেমন আপনার নাম, ইমেল ঠিকানা, ফোন নম্বর, এর বিষয়বস্তু আপনি যে বার্তা এবং/অথবা সংযুক্তিগুলি আমাদের পাঠাতে পারেন এবং অন্য যেকোন তথ্য প্রদান করতে পারেন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces \"সত্য \"মার্জিন-টপ: 0.5rem; মার্জিন-নিচ: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">আমরা কীভাবে আপনার তথ্য ব্যবহার করি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span ডেটা -preserver-spaces=\"true\">আমরা বিভিন্ন উপায়ে সংগ্রহ করা তথ্য ব্যবহার করি, যার মধ্যে রয়েছে:</span></p><ul style=\"margin-bottom: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; ব্যাকগ্রাউন্ড: স্বচ্ছ;\"><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আমাদের ওয়েবসাইট প্রদান, পরিচালনা এবং রক্ষণাবেক্ষণ করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আমাদের ওয়েবসাইট উন্নত করুন, ব্যক্তিগতকৃত করুন এবং প্রসারিত করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আপনি আমাদের ওয়েবসাইট কীভাবে ব্যবহার করেন তা বুঝুন এবং বিশ্লেষণ করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">নতুন পণ্য, পরিষেবা, বৈশিষ্ট্য এবং কার্যকারিতা বিকাশ করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আপনার সাথে যোগাযোগ করুন, সরাসরি বা আমাদের অংশীদারদের একজনের মাধ্যমে, গ্রাহক পরিষেবা সহ, আপনাকে ওয়েবসাইট সম্পর্কিত আপডেট এবং অন্যান্য তথ্য প্রদান করতে, এবং বিপণন এবং প্রচারমূলক উদ্দেশ্যে</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আপনাকে ইমেল পাঠান</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">জালিয়াতি খুঁজুন এবং প্রতিরোধ করুন</span></li></ul><h3 style=\"margin-top: 0.5rem; মার্জিন-নিচ: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">লগ ফাইল</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver- spaces=\"true\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-সাইজ: var(--BS-বডি-ফন্ট-সাইজ); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); text-align: var(--bs-body-text-align);\"> Ischool Management System</span>&nbsp;লগ ফাইল ব্যবহার করার একটি আদর্শ পদ্ধতি অনুসরণ করে৷ এই ফাইল ভিজিটর লগ লগ যখন তারা ওয়েবসাইট পরিদর্শন. সমস্ত হোস্টিং কোম্পানি এটি করে এবং হোস্টিং পরিষেবার বিশ্লেষণের একটি অংশ। লগ ফাইলের মাধ্যমে সংগৃহীত তথ্যের মধ্যে রয়েছে ইন্টারনেট প্রোটোকল (IP) ঠিকানা, ব্রাউজারের ধরন, ইন্টারনেট পরিষেবা প্রদানকারী (ISP), তারিখ এবং সময় স্ট্যাম্প, উল্লেখ/প্রস্থান পৃষ্ঠা এবং সম্ভবত ক্লিকের সংখ্যা। এগুলো কোনো ব্যক্তিগতভাবে শনাক্তযোগ্য তথ্যের সাথে যুক্ত নয়। তথ্যের উদ্দেশ্য হল প্রবণতা বিশ্লেষণ করা, সাইট পরিচালনা করা, ওয়েবসাইটে ব্যবহারকারীদের গতিবিধি ট্র্যাক করা এবং জনসংখ্যা সংক্রান্ত তথ্য সংগ্রহ করা।</span></p><p style=\"margin-bottom: 0.9rem;\">&nbsp;</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span ডেটা -preserver-spaces=\"true\">কুকিজ এবং ওয়েব বীকন</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">অন্য যেকোনও মত ওয়েবসাইট, Ischool Management System &nbsp;কুকিজ ব্যবহার করে।. এই কুকিগুলি ভিজিটরদের পছন্দ, এবং ভিজিটর অ্যাক্সেস বা ভিজিট করা ওয়েবসাইটের পৃষ্ঠাগুলি সহ তথ্য সংরক্ষণ করতে ব্যবহার করা হয়। ভিজিটরদের ব্রাউজারের ধরন এবং/অথবা অন্যান্য তথ্যের উপর ভিত্তি করে আমাদের ওয়েব পৃষ্ঠার বিষয়বস্তু কাস্টমাইজ করে ব্যবহারকারীদের অভিজ্ঞতা অপ্টিমাইজ করতে তথ্য ব্যবহার করা হয়।</span></p><h3 style=\"margin-top: 0.5rem; margin- নীচে: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">বিজ্ঞাপন অংশীদারদের গোপনীয়তা নীতি</span></h3><p style=\"margin-bottom: 0.9rem;\"> <span data-preserver-spaces=\"true\">এর প্রতিটি বিজ্ঞাপন অংশীদারের জন্য গোপনীয়তা নীতি খুঁজে পেতে আপনি এই তালিকাটি দেখতে পারেন Ischool Management System .</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">তৃতীয় পক্ষের বিজ্ঞাপন সার্ভার বা বিজ্ঞাপন নেটওয়ার্ক কুকিজ, জাভাস্ক্রিপ্ট, এর মতো প্রযুক্তি ব্যবহার করে অথবা ওয়েব বীকন যা তাদের নিজ নিজ বিজ্ঞাপনে ব্যবহৃত হয় এবং লিঙ্কে প্রদর্শিত হয় Ischool Management System , যা সরাসরি ব্যবহারকারীদের ব্রাউজারে পাঠানো হয়। যখন এটি ঘটে তখন তারা স্বয়ংক্রিয়ভাবে আপনার আইপি ঠিকানা গ্রহণ করে। এই প্রযুক্তিগুলি তাদের বিজ্ঞাপন প্রচারাভিযানের কার্যকারিতা পরিমাপ করতে এবং/অথবা আপনি যে ওয়েবসাইটগুলিতে যান সেই বিজ্ঞাপন সামগ্রীগুলিকে ব্যক্তিগতকৃত করতে ব্যবহার করা হয়৷</span></p><p style=\"margin-bottom: 0.9rem;\" ><span data-preserver-spaces=\"true\">মনে রাখবেন যে Ischool Management System &nbsp;থার্ড-পার্টি বিজ্ঞাপনদাতাদের দ্বারা ব্যবহৃত এই কুকিগুলিতে কোনও অ্যাক্সেস বা নিয়ন্ত্রণ নেই৷</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">তৃতীয় পক্ষের গোপনীয়তা নীতি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces =\"true\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> Ischool Management System </span>&nbsp;গোপনীয়তা নীতি অন্যান্য বিজ্ঞাপনদাতা বা ওয়েবসাইটে প্রযোজ্য নয়৷ সুতরাং, আমরা আপনাকে আরও বিস্তারিত তথ্যের জন্য এই তৃতীয় পক্ষের বিজ্ঞাপন সার্ভারগুলির সংশ্লিষ্ট গোপনীয়তা নীতিগুলির সাথে পরামর্শ করার পরামর্শ দিচ্ছি। এটিতে তাদের অনুশীলন এবং নির্দিষ্ট বিকল্পগুলি কীভাবে অপ্ট আউট করতে হয় সে সম্পর্কে নির্দেশাবলী অন্তর্ভুক্ত থাকতে পারে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\"> আপনি আপনার ব্রাউজার বিকল্পগুলির মাধ্যমে কুকিজ নিষ্ক্রিয় করতে বেছে নিতে পারেন। নির্দিষ্ট ওয়েব ব্রাউজারগুলির সাথে কুকি পরিচালনা সম্পর্কে আরও বিশদ তথ্য জানতে, এটি ব্রাউজারের নিজ নিজ ওয়েবসাইটে পাওয়া যেতে পারে৷</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">পেমেন্ট তথ্য গোপনীয়তা নীতি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span style= \"ফন্ট-ফ্যামিলি: var( --e-global-typography-text-font-family), &quot;Roboto&quot;, Sans-serif; ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; হরফ-আকার: var(--BS-বডি-ফন্ট -আকার); font-weight: var(-bs-body-font-weight); text-align: var(--bs-body-text-align);\">Ischool Management System</span>&nbsp;তদনুসারে আপনার সমস্ত গোপনীয় তথ্য রক্ষা করার গুরুত্বকে দৃঢ়ভাবে স্বীকার করে৷ Ischool Management System&nbsp;এর ওয়েবসাইটে সংগৃহীত ব্যবহারকারীর তথ্যের ভাল সুরক্ষা বজায় রাখে৷Ischool Management System . Ischool Management System&nbsp;ক্লায়েন্টদের ব্যক্তিগত তথ্য কখনও অন্য কোনও বহিরাগতের সাথে শেয়ার করে না। এই গোপনীয়তা নীতি Eduman-এর বর্তমান এবং প্রাক্তন উভয় ক্লায়েন্টের জন্য প্রযোজ্য। গোপনীয়তা নীতির সাথে একমত হওয়ার পরে, আপনি একজন ক্লায়েন্ট হিসাবে আমাদের সাইটে অ্যাক্সেস পাবেন। অন্যথায়, আপনি আমাদের ওয়েবসাইট ব্রাউজার হওয়ার জন্য উপযুক্ত নন। আমরা লগ ফাইলগুলিও বজায় রাখি এবং ফাইলগুলি আপডেট করি। আমাদের সমস্ত কার্যকলাপ সম্পূর্ণরূপে সুরক্ষিত যা কখনও বাইরের তৃতীয় ব্যক্তির সাথে ভাগ করে নেওয়া এবং ঘোষণা করা হবে না। এই গোপনীয়তা নীতিটিকে আরও শক্তিশালী করার জন্য পরিবর্তনযোগ্য তবে Eduman সতর্ক থাকে যাতে এটি কারও ক্ষতি না করে।</p><p style=\"margin-bottom: 0.9rem;\">&nbsp;</p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">রিফান্ড নীতিমালা</span></h3><p style=\"margin-bottom: 0.9rem;\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">Ischool Management System</span>&nbsp;কোনও লেনদেনের জন্য কোনও ফেরত বা চার্জব্যাক গ্রহণ করা হবে না।</p><p style=\"margin-bottom: 0.9rem;\">কিন্তু যদি কোনও লেনদেন নিয়ে কোনও বিরোধ দেখা দেয় তবে আমরা যথাযথ বৈধতা এবং লেনদেনের প্রমাণ সহ লেনদেনের স্থিতি আপডেট করব যার জন্য ১৪-২১ কার্যদিবসের প্রয়োজন।&nbsp;</p><p style=\"margin-bottom: 0.9rem;\">&nbsp;</p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">CCPA গোপনীয়তা অধিকার (আমার ব্যক্তিগত তথ্য বিক্রি করবেন না)</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">CCPA-এর অধীনে, অন্যান্য অধিকারের মধ্যে, ক্যালিফোর্নিয়ার ভোক্তাদের অধিকার রয়েছে:</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">গ্রাহকের তথ্য সংগ্রহকারী একটি ব্যবসাকে গ্রাহকদের সম্পর্কে সংগৃহীত বিভাগ এবং নির্দিষ্ট ব্যক্তিগত তথ্য প্রকাশ করার জন্য অনুরোধ করুন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">কোনও ব্যবসা প্রতিষ্ঠানকে অনুরোধ করুন যে তারা গ্রাহকের সম্পর্কে যে কোনও ব্যক্তিগত তথ্য সংগ্রহ করেছে তা মুছে ফেলুক।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">যে ব্যবসা প্রতিষ্ঠান গ্রাহকের তথ্য বিক্রি করে, তাদের যেন গ্রাহকের তথ্য বিক্রি না করা হয়, সেই প্রতিষ্ঠানকে অনুরোধ করুন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">যদি আপনি কোন অনুরোধ করেন, তাহলে আপনার সাড়া দেওয়ার জন্য আমাদের কাছে এক মাস সময় আছে। আপনি যদি এই অধিকারগুলির কোনটি প্রয়োগ করতে চান, তাহলে অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">জিডিপিআর ডেটা সুরক্ষা অধিকার</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আমরা নিশ্চিত করতে চাই যে আপনি আপনার সমস্ত ডেটা সুরক্ষা অধিকার সম্পর্কে সম্পূর্ণরূপে সচেতন। প্রতিটি ব্যবহারকারীর নিম্নলিখিত অধিকার রয়েছে:</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">অ্যাক্সেসের অধিকার - আপনার তথ্যের কপি অনুরোধ করার অধিকার আপনার আছে। এই পরিষেবার জন্য আমরা আপনার কাছ থেকে সামান্য ফি নিতে পারি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">সংশোধনের অধিকার - আপনার কাছে এমন কোনও তথ্য সংশোধন করার অনুরোধ করার অধিকার আছে যা আপনি ভুল বলে মনে করেন। আপনার কাছে এমন তথ্য সম্পূর্ণ করার অনুরোধ করার অধিকারও আছে যা আপনি অসম্পূর্ণ বলে মনে করেন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">মুছে ফেলার অধিকার - কিছু শর্তের অধীনে, আপনার ডেটা মুছে ফেলার অনুরোধ করার অধিকার আপনার আছে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">প্রক্রিয়াকরণ সীমাবদ্ধ করার অধিকার - আপনার কাছে কিছু শর্তের অধীনে আপনার ডেটা প্রক্রিয়াকরণ সীমাবদ্ধ করার অনুরোধ করার অধিকার রয়েছে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">প্রক্রিয়াকরণের বিরুদ্ধে আপত্তি জানানোর অধিকার – কিছু শর্তের অধীনে, আপনার ডেটা প্রক্রিয়াকরণের বিরুদ্ধে আপত্তি জানানোর অধিকার আপনার আছে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">ডেটা পোর্টেবিলিটির অধিকার – আপনার কাছে অনুরোধ করার অধিকার আছে যে আমরা যে ডেটা সংগ্রহ করেছি তা অন্য কোনও সংস্থায়, অথবা সরাসরি আপনার কাছে, কিছু শর্তের অধীনে স্থানান্তর করি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">যদি আপনি কোন অনুরোধ করেন, তাহলে আপনার সাড়া দেওয়ার জন্য আমাদের কাছে এক মাস সময় আছে। আপনি যদি এই অধিকারগুলির কোনটি প্রয়োগ করতে চান, তাহলে অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">শিশুদের তথ্য</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আমাদের অগ্রাধিকারের আরেকটি অংশ হল ইন্টারনেট ব্যবহারের সময় শিশুদের সুরক্ষা প্রদান করা। আমরা বাবা-মা এবং অভিভাবকদের তাদের অনলাইন কার্যকলাপ পর্যবেক্ষণ, অংশগ্রহণ এবং/অথবা পর্যবেক্ষণ এবং নির্দেশনা দেওয়ার জন্য উৎসাহিত করি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> Ischool Management System</span>&nbsp; ১৩ বছরের কম বয়সী শিশুদের কাছ থেকে জেনেশুনে কোনও ব্যক্তিগত শনাক্তযোগ্য তথ্য সংগ্রহ করে না। যদি আপনি মনে করেন যে আপনার সন্তান আমাদের ওয়েবসাইটে এই ধরণের তথ্য সরবরাহ করেছে, তাহলে আমরা আপনাকে অবিলম্বে আমাদের সাথে যোগাযোগ করার জন্য জোরালোভাবে উৎসাহিত করছি এবং আমরা আমাদের রেকর্ড থেকে এই ধরণের তথ্য দ্রুত অপসারণের জন্য যথাসাধ্য চেষ্টা করব।</span></p><div><span data-preserver-spaces=\"true\"><br></span></div></div></div><div class=\"elementor-element elementor-element-4f36e2b7 elementor-widget__width-initial elementor-absolute elementor-widget elementor-widget-spacer\" data-id=\"4f36e2b7\" data-element_type=\"widget\" data-settings=\"{&quot;_position&quot;:&quot;absolute&quot;}\" data-widget_type=\"spacer.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: absolute; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; z-index: 1; width: var( --container-widget-width, 100vw ); margin-bottom: 0px; max-width: 100vw; --spacer-size: 100vh; --container-widget-width: 100vw; --container-widget-flex-grow: 0; top: -0.5px; right: 551px; color: rgb(51, 51, 51); font-family: -apple-system, &quot;system-ui&quot;, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); background-color: transparent; background-image: radial-gradient(rgba(44, 255, 0, 0.06) 0%, rgba(0, 0, 0, 0) 70%);\"><div class=\"elementor-spacer\"></div></div></div>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(6, 2, 'bn', 'সহায়তা নীতি', '<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">ওনেস্ট স্কুলড ম্যানেজমেন্ট সিস্টেমের জন্য সহায়তা নীতি সহায়তা নীতি</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">সহায়তা নীতি</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">১. সহায়তার পরিধি</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">আমরা নিম্নলিখিত ক্ষেত্রে সহায়তা প্রদান করি:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">সেটআপ এবং কনফিগারেশন।</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">প্রযুক্তিগত সমস্যা সমাধান।</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">বৈশিষ্ট্য-সম্পর্কিত প্রশ্ন।</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. সাপোর্ট চ্যানেল</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">ইমেইল: [sales.onesttech.com]</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">হোয়াটসঅ্যাপ: [+৮৮০ ১৯৫৯-৩৩৫৫৫৫]</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">৩. প্রতিক্রিয়া সময়</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">সাধারণ প্রশ্ন: ৪৮ কর্মঘণ্টার মধ্যে উত্তর।</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">গুরুত্বপূর্ণ সমস্যা (যেমন, পরিষেবা বন্ধ থাকার সময়): ২৪ ঘন্টার মধ্যে প্রতিক্রিয়া।</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">৪. আপডেট এবং রক্ষণাবেক্ষণ</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">বৈশিষ্ট্য বৃদ্ধি এবং বাগ সংশোধনের জন্য নিয়মিত আপডেট প্রদান করা হয়। নির্ধারিত রক্ষণাবেক্ষণের আগে বিজ্ঞপ্তি পাঠানো হবে।</span></p><p><span id=\"docs-internal-guid-500ba9c7-7fff-1baf-821f-835a1517b432\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">আপনি যদি এই নীতিগুলি আরও কাস্টমাইজ করতে চান বা আরও বিশদ যোগ করতে চান তবে আমাকে জানান!</span></p>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(7, 3, 'bn', 'শর্তাবলী', '<p><b>ব্যবহারের শর্তাবলী Ischool Management System\n                </b></p><p><b><br></b>\n                                            এই নিয়ম ও শর্তাবলী স্কুল ম্যানেজমেন্ট সফ্টওয়্যার দ্বারা প্রদত্ত আপনার অ্যাক্সেস এবং ব্যবহার নিয়ন্ত্রণ করে Ischool Management System . সফ্টওয়্যার অ্যাক্সেস বা ব্যবহার করে, আপনি এই শর্তাবলী দ্বারা আবদ্ধ হতে সম্মত হন। আপনি যদি এই শর্তাবলীতে সম্মত না হন তবে অনুগ্রহ করে সফটওয়্যারটি অ্যাক্সেস করা বা ব্যবহার করা থেকে বিরত থাকুন।\n                        </p><p><br></p><p><b>\n                        শর্তাদি গ্রহণ: </b>সফ্টওয়্যার অ্যাক্সেস বা ব্যবহার করে, আপনি এই শর্তাবলী দ্বারা আবদ্ধ হতে সম্মত হন। আপনি যদি এই চুক্তির সমস্ত শর্তাবলীর সাথে সম্মত না হন তবে আপনি অবশ্যই সফ্টওয়্যারটি ব্যবহার করবেন না৷</p><p><br></p><p>\n                        </p><p><b style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(--bs-body-font-size); text- align: var(--bs-body-text-align);\">সফ্টওয়্যারের ব্যবহার:</b><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; হরফ-আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ) ;\"> সফ্টওয়্যারটি শুধুমাত্র স্কুল, কলেজ এবং বিশ্ববিদ্যালয় সহ কিন্তু সীমাবদ্ধ নয় শিক্ষা প্রতিষ্ঠান পরিচালনার উদ্দেশ্যে প্রদান করা হয়৷ আপনি কোনো অবৈধ বা অননুমোদিত উদ্দেশ্যে সফ্টওয়্যার ব্যবহার না করতে সম্মত হন৷</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font -আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ); \"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-সাইজ: var(--bs- body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); ফন্ট-ওজন: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><b>ব্যবহারকারীর অ্যাকাউন্ট: </b></ span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(-bs-body-font-size); font-weight: var(- -bs-body-font-weight); text-align: var(--bs-body-text-align);\">সফ্টওয়্যারের কিছু বৈশিষ্ট্য অ্যাক্সেস করার জন্য আপনাকে একটি অ্যাকাউন্ট তৈরি করতে হতে পারে৷ আপনার অ্যাকাউন্টের শংসাপত্রের গোপনীয়তা বজায় রাখার জন্য এবং আপনার অ্যাকাউন্টের অধীনে হওয়া সমস্ত কার্যকলাপের জন্য আপনি দায়ী৷</span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var(--bs- body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-সাইজ : var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> <br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body- font-size); text-align: var(--bs-body-text-align);\"><b>\n\n                                                    গোপনীয়তা:</b></span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size) ; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> আমরা আপনার গোপনীয়তা রক্ষা করতে প্রতিশ্রুতিবদ্ধ। আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং প্রকাশ করি তা আমাদের গোপনীয়তা নীতি রূপরেখা দেয়। সফ্টওয়্যার ব্যবহার করে, আপনি গোপনীয়তা নীতিতে বর্ণিত আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং প্রকাশে সম্মত হন।</span></p><p><span style=\"color: var(--ot- টেক্সট-সাবটাইটেল; ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-সাইজ); হরফ-ওজন: var(--bs-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var (--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-আকার); হরফ-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট- সারিবদ্ধ);\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); পাঠ্য-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                    বুদ্ধিবৃত্তিক সম্পত্তি:</b></span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> সফটওয়্যার এবং এর মূল বিষয়বস্তু, বৈশিষ্ট্য এবং কার্যকারিতা হল মালিক Ischool Management System এবং আন্তর্জাতিক কপিরাইট, ট্রেডমার্ক, পেটেন্ট, ট্রেড সিক্রেট এবং অন্যান্য মেধা সম্পত্তি বা মালিকানা অধিকার আইন দ্বারা সুরক্ষিত।</span></p><p><span style=\"color: var(--ot-text-subtitle) ); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-সাইজ); ফন্ট-ওজন: var(--bs-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var(-- bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট -আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ); \">\n                                                    </span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); পাঠ্য-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                                                দায়বদ্ধতার সীমাবদ্ধতা:</b></span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font- আকার); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> কোন ঘটনাতেই হবে না\n                                                    Ischool Management System যেকোন পরোক্ষ, আনুষঙ্গিক, বিশেষ, আনুষঙ্গিক, বা শাস্তিমূলক ক্ষতির জন্য দায়ী হতে হবে, যার মধ্যে সফ্টওয়্যার ব্যবহার বা ব্যবহারে অক্ষমতা থেকে উদ্ভূত লাভ, ডেটা বা সদিচ্ছার ক্ষতি সহ কিন্তু সীমাবদ্ধ নয়৷</span></p> <p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(--bs-body-font-size); font-weight: var( --bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color : var(--ot-টেক্সট-সাবটাইটেল); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; হরফ-আকার: var(--BS-বডি-ফন্ট-সাইজ); হরফ-ওজন: var (--BS-বডি-ফন্ট-ওজন ); text-align: var(--bs-body-text-align);\">\n                                                    </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); text-align: var(--bs-body-text-align);\"><b>শর্তাবলীতে পরিবর্তন:</b></span><span style=\"background-color: transparent; color: var (--ot-টেক্সট-সাবটাইটেল); ফন্ট-সাইজ: var(--bs-বডি-ফন্ট-সাইজ); হরফ-ওজন: var(--BS-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var( --bs-body-text-align);\"> আমরা যে কোনো সময় এই শর্তাবলী পরিবর্তন বা প্রতিস্থাপন করার অধিকার সংরক্ষণ করি৷ যদি একটি সংশোধন বস্তুগত হয়, আমরা যেকোনো নতুন শর্ত কার্যকর হওয়ার অন্তত 30 দিনের নোটিশ প্রদান করব। কোন বস্তুগত পরিবর্তন গঠন করে তা আমাদের নিজস্ব বিবেচনার ভিত্তিতে নির্ধারণ করা হবে।</span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font- আকার: var(--BS-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ);\" ><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body -font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                                                    </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); পাঠ্য-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                                                পরিচালনা আইন: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">এই শর্তাবলী দ্বারা নিয়ন্ত্রিত হবে এবং এর সাথে সঙ্গতিপূর্ণ হবে মার্কিন যুক্তরাষ্ট্রের আইন, তার আইনের বিধানের বিরোধ বিবেচনা ছাড়াই।\n                                                    </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); ফন্ট-ওজন: var(--bs-body-font-weight); টেক্সট-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><br></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(-bs-body-font-size); text-align: var(- -বিএস-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                                                আমাদের সাথে যোগাযোগ করুন: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">এই শর্তাবলী সম্পর্কে আপনার কোন প্রশ্ন থাকলে, অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন এ&nbsp; ওগুলো\n\n                                                                                সফ্টওয়্যার অ্যাক্সেস বা ব্যবহার করে, আপনি এই শর্তাবলী দ্বারা আবদ্ধ হতে সম্মত হন। আপনি যদি এই শর্তাবলীতে সম্মত না হন তবে অনুগ্রহ করে সফটওয়্যার অ্যাক্সেস বা ব্যবহার করা থেকে বিরত থাকুন৷</span><br></p>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1),
(8, 4, 'bn', 'আমাদের মিশন', '<p>At Ischool Management System , আমরা একটি লালনশীল এবং সমৃদ্ধ শিক্ষামূলক পরিবেশ প্রদানের জন্য নিবেদিত যা শিক্ষার্থীদের তাদের পূর্ণ সম্ভাবনায় পৌঁছানোর ক্ষমতা দেয়। আমাদের লক্ষ্য হল শিক্ষাগত উৎকর্ষতা, চরিত্রের বিকাশ, এবং আমরা যে সকল শিক্ষার্থীকে সেবা করি তাদের আজীবন শিক্ষা লাভ করা।</p><p><br></p><h3><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">আমাদের মূল মূল্যবোধ</b></h3><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><br></b><br></p><p><b>\n                ১. শ্রেষ্ঠত্ব : </b>আমরা শিক্ষার সমস্ত দিকগুলিতে শ্রেষ্ঠত্বের জন্য প্রতিশ্রুতিবদ্ধ, আমাদের শিক্ষার্থীদের সর্বোচ্চ মানের শিক্ষাদান, সংস্থান এবং সহায়তা প্রদানের জন্য সচেষ্ট।\n                    </p><p><br></p><p><b>\n                ২. সততা : </b>ছাত্র, পিতামাতা, কর্মচারী এবং সম্প্রদায়ের সাথে আমাদের মিথস্ক্রিয়াতে আমরা সততা, সততা এবং নৈতিক আচরণের সর্বোচ্চ মান বজায় রাখি।\n                    </p><p><br></p><p><b>\n                ৩. সম্মান :</b>আমরা আমাদের স্কুল সম্প্রদায়ের মধ্যে প্রতিটি ব্যক্তির অনন্য ক্ষমতা, দৃষ্টিভঙ্গি এবং পটভূমিকে মূল্যায়ন করে শ্রদ্ধার সংস্কৃতি গড়ে তুলি।\n                    </p><p><br></p><p><b>\n                ৪. সহযোগিতা : </b>আমরা আমাদের ভাগ করা লক্ষ্য অর্জনের জন্য ছাত্র, পিতামাতা, শিক্ষাবিদ এবং সম্প্রদায়ের সাথে ঘনিষ্ঠভাবে কাজ করে সহযোগিতা এবং দলবদ্ধতার শক্তিতে বিশ্বাস করি।\n                </p><p><br></p><p><b>\n                ৫. উদ্ভাবন :</b>\n                আমরা উদ্ভাবন এবং সৃজনশীলতাকে আলিঙ্গন করি, শেখার অভিজ্ঞতা বাড়াতে এবং আমাদের শিক্ষার্থীদের ক্রমবর্ধমান চাহিদা মেটাতে ক্রমাগত নতুন এবং কার্যকর উপায় খুঁজি।</p><p><br></p><p>\n                    </p><p style=\"text-align: center;\"><b><u>\n                আমাদের লক্ষ্য</u></b></p><p style=\"text-align: center;\"><b><u><br></u></b></p><p style=\"text-align: center;\"><b><u>\n                </u></b></p>\n\n\n                <ul>\n                    <li>\n                        <b>একাডেমিক শ্রেষ্ঠত্ব : </b>\n                        আমরা কঠোর একাডেমিক প্রোগ্রাম প্রদান করার চেষ্টা করি যা শিক্ষার্থীদের তাদের সর্বোচ্চ একাডেমিক সম্ভাবনা অর্জনের জন্য চ্যালেঞ্জ ও অনুপ্রাণিত করে।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b> চরিত্র বিকাশ :</b>\n                        আমরা আমাদের শিক্ষার্থীদের মধ্যে সততা, দায়িত্বশীলতা, সহানুভূতি এবং স্থিতিস্থাপকতার মতো দৃঢ় চরিত্রের বৈশিষ্ট্যের বিকাশে প্রতিশ্রুতিবদ্ধ।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b>আজীবন শিক্ষা : </b>\n                        আমাদের লক্ষ্য আমাদের শিক্ষার্থীদের মধ্যে শেখার প্রতি ভালবাসা এবং একটি বৃদ্ধির মানসিকতা জাগিয়ে তোলা, তাদের আজীবন শিক্ষার্থী হয়ে উঠতে ক্ষমতায়ন করা যারা কৌতূহলী, মানিয়ে নিতে পারে এবং নতুন ধারণা এবং সুযোগগুলি অন্বেষণ করতে আগ্রহী।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b> সম্প্রদায় জড়িত :</b>\n                        আমরা একটি সহায়ক এবং অন্তর্ভুক্তিমূলক শিক্ষার পরিবেশ তৈরি করতে পিতামাতা, পরিবার এবং বৃহত্তর সম্প্রদায়ের সাথে সক্রিয়ভাবে জড়িত থাকার চেষ্টা করি যা আমাদের শিক্ষার্থীদের সামগ্রিক বিকাশকে লালন করে। আমাদের মিশনে আমাদের সাথে যোগ দিন\n                        আমরা আপনাকে পরবর্তী প্রজন্মের নেতা, চিন্তাবিদ এবং উদ্ভাবকদের অনুপ্রাণিত ও ক্ষমতায়িত করতে আমাদের মিশনে আমাদের সাথে যোগ দেওয়ার জন্য আমন্ত্রণ জানাচ্ছি।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b> উজ্জ্বল ভবিষ্যত গঠনে :</b>\n                        একসাথে, আমরা আমাদের শিক্ষার্থীদের জীবনে একটি পরিবর্তন আনতে পারি এবং সবার জন্য একটি উজ্জ্বল ভবিষ্যত তৈরি করতে পারি।\n                        এই নমুনা বিষয়বস্তু স্কুলের মিশন, মূল মান, লক্ষ্য এবং সেই লক্ষ্যগুলি অর্জনে অংশীদারদের যোগদানের জন্য একটি আমন্ত্রণ প্রদান করে। আপনার স্কুল পরিচালনার আবেদনের নির্দিষ্ট মিশন এবং মানগুলির সাথে সারিবদ্ধ করার জন্য আপনি এটিকে আরও কাস্টমাইজ করতে পারেন৷\n                    </li>\n                </ul>', '2025-06-03 07:04:09', '2025-06-03 07:04:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `parent_guardians`
--

CREATE TABLE `parent_guardians` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_relation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_place_of_work` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `attribute` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `attribute`, `keywords`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'dashboard', '{\"read\":\"calendar_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'student', '{\"read\":\"student_read\",\"create\":\"student_create\",\"update\":\"student_update\",\"delete\":\"student_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'student_category', '{\"read\":\"student_category_read\",\"create\":\"student_category_create\",\"update\":\"student_category_update\",\"delete\":\"student_category_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'promote_students', '{\"read\":\"promote_students_read\",\"create\":\"promote_students_create\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'disabled_students', '{\"read\":\"disabled_students_read\",\"create\":\"disabled_students_create\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'parent', '{\"read\":\"parent_read\",\"create\":\"parent_create\",\"update\":\"parent_update\",\"delete\":\"parent_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'admission', '{\"read\":\"admission_read\",\"create\":\"admission_create\",\"update\":\"admission_update\",\"delete\":\"admission_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'classes', '{\"read\":\"classes_read\",\"create\":\"classes_create\",\"update\":\"classes_update\",\"delete\":\"classes_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 'section', '{\"read\":\"section_read\",\"create\":\"section_create\",\"update\":\"section_update\",\"delete\":\"section_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 'shift', '{\"read\":\"shift_read\",\"create\":\"shift_create\",\"update\":\"shift_update\",\"delete\":\"shift_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 'class_setup', '{\"read\":\"class_setup_read\",\"create\":\"class_setup_create\",\"update\":\"class_setup_update\",\"delete\":\"class_setup_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 'subject', '{\"read\":\"subject_read\",\"create\":\"subject_create\",\"update\":\"subject_update\",\"delete\":\"subject_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 'subject_assign', '{\"read\":\"subject_assign_read\",\"create\":\"subject_assign_create\",\"update\":\"subject_assign_update\",\"delete\":\"subject_assign_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 'class_routine', '{\"read\":\"report_class_routine_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 'time_schedule', '{\"read\":\"time_schedule_read\",\"create\":\"time_schedule_create\",\"update\":\"time_schedule_update\",\"delete\":\"time_schedule_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 'class_room', '{\"read\":\"class_room_read\",\"create\":\"class_room_create\",\"update\":\"class_room_update\",\"delete\":\"class_room_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 'fees_group', '{\"read\":\"fees_group_read\",\"create\":\"fees_group_create\",\"update\":\"fees_group_update\",\"delete\":\"fees_group_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 'fees_type', '{\"read\":\"fees_type_read\",\"create\":\"fees_type_create\",\"update\":\"fees_type_update\",\"delete\":\"fees_type_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 'fees_master', '{\"read\":\"fees_master_read\",\"create\":\"fees_master_create\",\"update\":\"fees_master_update\",\"delete\":\"fees_master_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 'fees_assign', '{\"read\":\"fees_assign_read\",\"create\":\"fees_assign_create\",\"update\":\"fees_assign_update\",\"delete\":\"fees_assign_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 'fees_collect', '{\"read\":\"fees_collect_read\",\"create\":\"fees_collect_create\",\"update\":\"fees_collect_update\",\"delete\":\"fees_collect_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 'discount_setup', '{\"siblings_discount\":\"siblings_discount\",\"early_payment_discount\":\"early_payment_discount\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 'exam_type', '{\"read\":\"exam_type_read\",\"create\":\"exam_type_create\",\"update\":\"exam_type_update\",\"delete\":\"exam_type_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 'marks_grade', '{\"read\":\"marks_grade_read\",\"create\":\"marks_grade_create\",\"update\":\"marks_grade_update\",\"delete\":\"marks_grade_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(25, 'exam_assign', '{\"read\":\"exam_assign_read\",\"create\":\"exam_assign_create\",\"update\":\"exam_assign_update\",\"delete\":\"exam_assign_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(26, 'exam_routine', '{\"read\":\"report_exam_routine_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(27, 'marks_register', '{\"read\":\"marks_register_read\",\"create\":\"marks_register_create\",\"update\":\"marks_register_update\",\"delete\":\"marks_register_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(28, 'homework', '{\"read\":\"homework_read\",\"create\":\"homework_create\",\"update\":\"homework_update\",\"delete\":\"homework_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(29, 'exam_setting', '{\"read\":\"exam_setting_read\",\"update\":\"exam_setting_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(30, 'account_head', '{\"read\":\"account_head_read\",\"create\":\"account_head_create\",\"update\":\"account_head_update\",\"delete\":\"account_head_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(31, 'income', '{\"read\":\"income_read\",\"create\":\"income_create\",\"update\":\"income_update\",\"delete\":\"income_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(32, 'expense', '{\"read\":\"expense_read\",\"create\":\"expense_create\",\"update\":\"expense_update\",\"delete\":\"expense_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(33, 'attendance', '{\"read\":\"attendance_read\",\"create\":\"attendance_create\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(34, 'attendance_report', '{\"read\":\"report_attendance_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(35, 'marksheet', '{\"read\":\"report_marksheet_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(36, 'merit_list', '{\"read\":\"report_merit_list_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(37, 'progress_card', '{\"read\":\"report_progress_card_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(38, 'due_fees', '{\"read\":\"report_due_fees_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(39, 'fees_collection', '{\"read\":\"report_fees_collection_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(40, 'account', '{\"read\":\"report_account_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(41, 'language', '{\"read\":\"language_read\",\"create\":\"language_create\",\"update\":\"language_update\",\"update terms\":\"language_update_terms\",\"delete\":\"language_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(42, 'roles', '{\"read\":\"role_read\",\"create\":\"role_create\",\"update\":\"role_update\",\"delete\":\"role_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(43, 'users', '{\"read\":\"user_read\",\"create\":\"user_create\",\"update\":\"user_update\",\"delete\":\"user_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(44, 'department', '{\"read\":\"department_read\",\"create\":\"department_create\",\"update\":\"department_update\",\"delete\":\"department_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(45, 'designation', '{\"read\":\"designation_read\",\"create\":\"designation_create\",\"update\":\"designation_update\",\"delete\":\"designation_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(46, 'sections', '{\"read\":\"page_sections_read\",\"update\":\"page_sections_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(47, 'slider', '{\"read\":\"slider_read\",\"create\":\"slider_create\",\"update\":\"slider_update\",\"delete\":\"slider_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(48, 'about', '{\"read\":\"about_read\",\"create\":\"about_create\",\"update\":\"about_update\",\"delete\":\"about_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(49, 'counter', '{\"read\":\"counter_read\",\"create\":\"counter_create\",\"update\":\"counter_update\",\"delete\":\"counter_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(50, 'contact_info', '{\"read\":\"contact_info_read\",\"create\":\"contact_info_create\",\"update\":\"contact_info_update\",\"delete\":\"contact_info_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(51, 'dep_contact', '{\"read\":\"dep_contact_read\",\"create\":\"dep_contact_create\",\"update\":\"dep_contact_update\",\"delete\":\"dep_contact_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(52, 'news', '{\"read\":\"news_read\",\"create\":\"news_create\",\"update\":\"news_update\",\"delete\":\"news_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(53, 'event', '{\"read\":\"event_read\",\"create\":\"event_create\",\"update\":\"event_update\",\"delete\":\"event_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(54, 'gallery_category', '{\"read\":\"gallery_category_read\",\"create\":\"gallery_category_create\",\"update\":\"gallery_category_update\",\"delete\":\"gallery_category_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(55, 'gallery', '{\"read\":\"gallery_read\",\"create\":\"gallery_create\",\"update\":\"gallery_update\",\"delete\":\"gallery_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(56, 'subscribe', '{\"read\":\"subscribe_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(57, 'contact_message', '{\"read\":\"contact_message_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(58, 'general_settings', '{\"read\":\"general_settings_read\",\"update\":\"general_settings_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(59, 'storage_settings', '{\"read\":\"storage_settings_read\",\"update\":\"storage_settings_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(60, 'task_schedules', '{\"read\":\"task_schedules_read\",\"update\":\"task_schedules_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(61, 'software_update', '{\"read\":\"software_update_read\",\"update\":\"software_update_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(62, 'recaptcha_settings', '{\"read\":\"recaptcha_settings_read\",\"update\":\"recaptcha_settings_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(63, 'payment_gateway_settings', '{\"read\":\"payment_gateway_settings_read\",\"update\":\"payment_gateway_settings_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(64, 'email_settings', '{\"read\":\"email_settings_read\",\"update\":\"email_settings_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(65, 'sms_settings', '{\"read\":\"sms_settings_read\",\"update\":\"sms_settings_update\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(66, 'genders', '{\"read\":\"gender_read\",\"create\":\"gender_create\",\"update\":\"gender_update\",\"delete\":\"gender_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(67, 'religions', '{\"read\":\"religion_read\",\"create\":\"religion_create\",\"update\":\"religion_update\",\"delete\":\"religion_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(68, 'blood_groups', '{\"read\":\"blood_group_read\",\"create\":\"blood_group_create\",\"update\":\"blood_group_update\",\"delete\":\"blood_group_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(69, 'sessions', '{\"read\":\"session_read\",\"create\":\"session_create\",\"update\":\"session_update\",\"delete\":\"session_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(70, 'tax_setup', '{\"update\":\"tax_setup\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(71, 'book_category', '{\"read\":\"book_category_read\",\"create\":\"book_category_create\",\"update\":\"book_category_update\",\"delete\":\"book_category_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(72, 'book', '{\"read\":\"book_read\",\"create\":\"book_create\",\"update\":\"book_update\",\"delete\":\"book_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(73, 'member', '{\"read\":\"member_read\",\"create\":\"member_create\",\"update\":\"member_update\",\"delete\":\"member_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(74, 'member_category', '{\"read\":\"member_category_read\",\"create\":\"member_category_create\",\"update\":\"member_category_update\",\"delete\":\"member_category_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(75, 'issue_book', '{\"read\":\"issue_book_read\",\"create\":\"issue_book_create\",\"update\":\"issue_book_update\",\"delete\":\"issue_book_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(76, 'online_exam_type', '{\"read\":\"online_exam_type_read\",\"create\":\"online_exam_type_create\",\"update\":\"online_exam_type_update\",\"delete\":\"online_exam_type_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(77, 'question_group', '{\"read\":\"question_group_read\",\"create\":\"question_group_create\",\"update\":\"question_group_update\",\"delete\":\"question_group_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(78, 'question_bank', '{\"read\":\"question_bank_read\",\"create\":\"question_bank_create\",\"update\":\"question_bank_update\",\"delete\":\"question_bank_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(79, 'online_exam', '{\"read\":\"online_exam_read\",\"create\":\"online_exam_create\",\"update\":\"online_exam_update\",\"delete\":\"online_exam_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(80, 'id_card', '{\"read\":\"id_card_read\",\"create\":\"id_card_create\",\"update\":\"id_card_update\",\"delete\":\"id_card_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(81, 'id_card_generate', '{\"read\":\"id_card_generate_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(82, 'certificate', '{\"read\":\"certificate_read\",\"create\":\"certificate_create\",\"update\":\"certificate_update\",\"delete\":\"certificate_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(83, 'certificate_generate', '{\"read\":\"certificate_generate_read\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(84, 'gmeet', '{\"read\":\"gmeet_read\",\"create\":\"gmeet_create\",\"update\":\"gmeet_update\",\"delete\":\"gmeet_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(85, 'notice_board', '{\"read\":\"notice_board_read\",\"create\":\"notice_board_create\",\"update\":\"notice_board_update\",\"delete\":\"notice_board_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(86, 'sms_mail_template', '{\"read\":\"sms_mail_template_read\",\"create\":\"sms_mail_template_create\",\"update\":\"nsms_mail_templateupdate\",\"delete\":\"sms_mail_template_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(87, 'sms_mail', '{\"read\":\"sms_mail_read\",\"create\":\"sms_mail_send\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(88, 'forums', '{\"read\":\"forum_list\",\"create\":\"forum_create\",\"update\":\"forum_update\",\"delete\":\"forum_delete\",\"forum_feeds\":\"forum_feeds\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(89, 'forum_comment', '{\"read\":\"forum_comment_list\",\"create\":\"forum_comment_create\",\"update\":\"forum_comment_update\",\"delete\":\"forum_comment_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(90, 'memories', '{\"read\":\"memory_list\",\"create\":\"memory_create\",\"update\":\"memory_update\",\"delete\":\"memory_delete\"}', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promote_students`
--

CREATE TABLE `promote_students` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_banks`
--

CREATE TABLE `question_banks` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `question_group_id` bigint UNSIGNED NOT NULL,
  `type` tinyint DEFAULT NULL,
  `question` text COLLATE utf8mb4_unicode_ci,
  `total_option` int DEFAULT NULL,
  `mark` int DEFAULT NULL,
  `answer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_bank_childrens`
--

CREATE TABLE `question_bank_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `question_bank_id` bigint UNSIGNED NOT NULL,
  `option` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_groups`
--

CREATE TABLE `question_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `religions`
--

CREATE TABLE `religions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `religions`
--

INSERT INTO `religions` (`id`, `name`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Islam', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Hindu', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Christian', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `religon_translates`
--

CREATE TABLE `religon_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `religion_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `status`, `permissions`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Super Admin', 'super-admin', '1', '[\"counter_read\",\"fees_collesction_read\",\"revenue_read\",\"fees_collection_this_month_read\",\"income_expense_read\",\"upcoming_events_read\",\"attendance_chart_read\",\"calendar_read\",\"student_read\",\"student_create\",\"student_update\",\"student_delete\",\"student_category_read\",\"student_category_create\",\"student_category_update\",\"student_category_delete\",\"promote_students_read\",\"promote_students_create\",\"disabled_students_read\",\"disabled_students_create\",\"parent_read\",\"parent_create\",\"parent_update\",\"parent_delete\",\"admission_read\",\"admission_create\",\"admission_update\",\"admission_delete\",\"classes_read\",\"classes_create\",\"classes_update\",\"classes_delete\",\"section_read\",\"section_create\",\"section_update\",\"section_delete\",\"shift_read\",\"shift_create\",\"shift_update\",\"shift_delete\",\"class_setup_read\",\"class_setup_create\",\"class_setup_update\",\"class_setup_delete\",\"subject_read\",\"subject_create\",\"subject_update\",\"subject_delete\",\"subject_assign_read\",\"subject_assign_create\",\"subject_assign_update\",\"subject_assign_delete\",\"class_routine_read\",\"class_routine_create\",\"class_routine_update\",\"class_routine_delete\",\"time_schedule_read\",\"time_schedule_create\",\"time_schedule_update\",\"time_schedule_delete\",\"class_room_read\",\"class_room_create\",\"class_room_update\",\"class_room_delete\",\"fees_group_read\",\"fees_group_create\",\"fees_group_update\",\"fees_group_delete\",\"fees_type_read\",\"fees_type_create\",\"fees_type_update\",\"fees_type_delete\",\"fees_master_read\",\"fees_master_create\",\"fees_master_update\",\"fees_master_delete\",\"fees_assign_read\",\"fees_assign_create\",\"fees_assign_update\",\"fees_assign_delete\",\"fees_collect_read\",\"fees_collect_create\",\"fees_collect_update\",\"fees_collect_delete\",\"exam_type_read\",\"exam_type_create\",\"exam_type_update\",\"exam_type_delete\",\"marks_grade_read\",\"marks_grade_create\",\"marks_grade_update\",\"marks_grade_delete\",\"exam_assign_read\",\"exam_assign_create\",\"exam_assign_update\",\"exam_assign_delete\",\"exam_routine_read\",\"exam_routine_create\",\"exam_routine_update\",\"exam_routine_delete\",\"marks_register_read\",\"marks_register_create\",\"marks_register_update\",\"marks_register_delete\",\"homework_read\",\"homework_create\",\"homework_update\",\"homework_delete\",\"exam_setting_read\",\"exam_setting_update\",\"account_head_read\",\"account_head_create\",\"account_head_update\",\"account_head_delete\",\"income_read\",\"income_create\",\"income_update\",\"income_delete\",\"expense_read\",\"expense_create\",\"expense_update\",\"expense_delete\",\"attendance_read\",\"attendance_create\",\"report_attendance_read\",\"report_marksheet_read\",\"report_merit_list_read\",\"report_progress_card_read\",\"report_due_fees_read\",\"report_fees_collection_read\",\"report_account_read\",\"report_class_routine_read\",\"report_exam_routine_read\",\"report_attendance_read\",\"language_read\",\"language_create\",\"language_update\",\"language_update_terms\",\"language_delete\",\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"department_read\",\"department_create\",\"department_update\",\"department_delete\",\"designation_read\",\"designation_create\",\"designation_update\",\"designation_delete\",\"page_sections_read\",\"page_sections_update\",\"slider_read\",\"slider_create\",\"slider_update\",\"slider_delete\",\"about_read\",\"about_create\",\"about_update\",\"about_delete\",\"counter_read\",\"counter_create\",\"counter_update\",\"counter_delete\",\"contact_info_read\",\"contact_info_create\",\"contact_info_update\",\"contact_info_delete\",\"dep_contact_read\",\"dep_contact_create\",\"dep_contact_update\",\"dep_contact_delete\",\"news_read\",\"news_create\",\"news_update\",\"news_delete\",\"event_read\",\"event_create\",\"event_update\",\"event_delete\",\"gallery_category_read\",\"gallery_category_create\",\"gallery_category_update\",\"gallery_category_delete\",\"gallery_read\",\"gallery_create\",\"gallery_update\",\"gallery_delete\",\"subscribe_read\",\"contact_message_read\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_update\",\"task_schedules_read\",\"task_schedules_update\",\"software_update_read\",\"software_update_update\",\"recaptcha_settings_read\",\"recaptcha_settings_update\",\"payment_gateway_settings_read\",\"payment_gateway_settings_update\",\"email_settings_read\",\"email_settings_update\",\"gender_read\",\"gender_create\",\"gender_update\",\"gender_delete\",\"religion_read\",\"religion_create\",\"religion_update\",\"religion_delete\",\"blood_group_read\",\"blood_group_create\",\"blood_group_update\",\"blood_group_delete\",\"session_read\",\"session_create\",\"session_update\",\"session_delete\",\"book_category_read\",\"book_category_create\",\"book_category_update\",\"book_category_delete\",\"book_read\",\"book_create\",\"book_update\",\"book_delete\",\"member_read\",\"member_create\",\"member_update\",\"member_delete\",\"member_category_read\",\"member_category_create\",\"member_category_update\",\"member_category_delete\",\"issue_book_read\",\"issue_book_create\",\"issue_book_update\",\"issue_book_delete\",\"online_exam_type_read\",\"online_exam_type_create\",\"online_exam_type_update\",\"online_exam_type_delete\",\"question_group_read\",\"question_group_create\",\"question_group_update\",\"question_group_delete\",\"question_bank_read\",\"question_bank_create\",\"question_bank_update\",\"question_bank_delete\",\"online_exam_read\",\"online_exam_create\",\"online_exam_update\",\"online_exam_delete\",\"forum_list\",\"forum_create\",\"forum_update\",\"forum_delete\",\"forum_feeds\",\"forum_comment_list\",\"forum_comment_create\",\"forum_comment_update\",\"forum_comment_delete\",\"memory_list\",\"memory_create\",\"memory_update\",\"memory_delete\"]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Admin', 'admin', '1', '[\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"language_read\",\"language_create\",\"language_update_terms\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_read\",\"recaptcha_settings_update\",\"email_settings_read\"]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Staff', 'staff', '1', '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'Accounting', 'accounting', '1', '[\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"language_read\",\"language_create\",\"language_update_terms\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_read\",\"recaptcha_settings_update\",\"email_settings_read\"]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'Teacher', 'teacher', '1', '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'Student', 'student', '1', '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'Gurdian', 'gurdian', '1', '[]', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `searches`
--

CREATE TABLE `searches` (
  `id` bigint UNSIGNED NOT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin, Student, Parent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `searches`
--

INSERT INTO `searches` (`id`, `route_name`, `title`, `user_type`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'dashboard', 'Dashboard', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'roles.index', 'Roles', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'genders.index', 'Genders', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'religions.index', 'Religions', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'blood-groups.index', 'Blood Groups', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'sessions.index', 'Sessions', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'users.index', 'Users', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'my.profile', 'Profile', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 'languages.index', 'Languages', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 'settings.general-settings', 'General Settings', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 'department.index', 'Department', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 'designation.index', 'Designation', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 'student.index', 'Student', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 'student_category.index', 'Student Category', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 'promote_students.index', 'Promote Students', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 'disabled_students.index', 'Disabled Student', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 'parent.index', 'Parent', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 'online-admissions.index', 'Online Admissions', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 'book-category.index', 'Book Category', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 'book.index', 'Book', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 'member.index', 'Member', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 'issue-book.index', 'Issue Book', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 'member-category.index', 'Member Category', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 'fees-group.index', 'Fees Group', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(25, 'fees-type.index', 'Fees Type', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(26, 'fees-master.index', 'Fees Master', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(27, 'fees-assign.index', 'Fees Assign', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(28, 'fees-collect.index', 'Fees Collect', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(29, 'exam-type.index', 'Exam Type', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(30, 'marks-grade.index', 'Marks Grade', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(31, 'marks-register.index', 'Marks Register', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(32, 'exam-routine.index', 'Exam Routine', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(33, 'exam-assign.index', 'Exam Assign', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(34, 'examination-settings.index', 'Examination Settings', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(35, 'attendance.index', 'Attendance', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(36, 'account_head.index', 'Account Head', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(37, 'income.index', 'Income', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(38, 'expense.index', 'Expense', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(39, 'classes.index', 'Classes', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(40, 'section.index', 'Sections', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(41, 'subject.index', 'Subjects', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(42, 'shift.index', 'Shifts', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(43, 'class-room.index', 'Class Room', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(44, 'class-setup.index', 'Class Setup', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(45, 'assign-subject.index', 'Assign Subject', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(46, 'class-routine.index', 'Class Routine', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(47, 'time_schedule.index', 'Time Schedule', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(48, 'report-marksheet.index', 'Marksheet Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(49, 'report-merit-list.index', 'Merit list Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(50, 'report-progress-card.index', 'Progress Card Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(51, 'report-due-fees.index', 'Due Fees Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(52, 'report-fees-collection.index', 'Fees Collection Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(53, 'report-account.index', 'Account Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(54, 'report-attendance.report', 'Attendance Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(55, 'report-class-routine.index', 'Class Routine Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(56, 'report-exam-routine.index', 'Exam Routine Report', 'Admin', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(57, 'student-panel-dashboard.index', 'Dashboard', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(58, 'student-panel.profile', 'Profile', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(59, 'student-panel-subject-list.index', 'Subject List', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(60, 'student-panel-class-routine.index', 'Class Routine', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(61, 'student-panel-exam-routine.index', 'Exam Routine', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(62, 'student-panel-marksheet.index', 'Marksheet', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(63, 'student-panel-attendance.index', 'Attendance', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(64, 'student-panel-fees.index', 'Fees', 'Student', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(65, 'parent-panel-dashboard.index.index', 'Dashboard', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(66, 'parent-panel.profile', 'Profile', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(67, 'parent-panel-subject-list.index', 'Subject List', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(68, 'parent-panel-class-routine.index', 'Class Routine', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(69, 'parent-panel-exam-routine.index', 'Exam Routine', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(70, 'parent-panel-marksheet.index', 'Marksheet', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(71, 'parent-panel-fees.index', 'Fees', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(72, 'parent-panel-attendance.index', 'Attendance', 'Parent', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_translates`
--

CREATE TABLE `section_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_translates`
--

INSERT INTO `section_translates` (`id`, `section_id`, `locale`, `name`, `description`, `data`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', '', '', '\"[{\\\"name\\\":\\\"Facebook\\\",\\\"icon\\\":\\\"fab fa-facebook-f\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.facebook.com\\\"},{\\\"name\\\":\\\"Twitter\\\",\\\"icon\\\":\\\"fab fa-twitter\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.twitter.com\\\"},{\\\"name\\\":\\\"Pinterest\\\",\\\"icon\\\":\\\"fab fa-pinterest-p\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.pinterest.com\\\"},{\\\"name\\\":\\\"Instagram\\\",\\\"icon\\\":\\\"fab fa-instagram\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.instagram.com\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Statement Of Onest Schooleded', '', '\"[{\\\"title\\\":\\\"Mission Statement\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Read More\\\"},{\\\"title\\\":\\\"Vision Statement\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet Read More\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Study at Onest Schooleded', 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet', '\"[{\\\"icon\\\":8,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\\\"},{\\\"icon\\\":9,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\\\"},{\\\"icon\\\":10,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 4, 'en', 'Explore Onest Schoooled', '\"We Educate Knowledge & Essential Skills\" is a phrase that emphasizes the importance of both theoretical knowledge', '\"[{\\\"tab\\\":\\\"Campus Life\\\",\\\"title\\\":\\\"Campus Life\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"},{\\\"tab\\\":\\\"Academic\\\",\\\"title\\\":\\\"Academic\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"},{\\\"tab\\\":\\\"Athletics\\\",\\\"title\\\":\\\"Athletics\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"},{\\\"tab\\\":\\\"School\\\",\\\"title\\\":\\\"School\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 5, 'en', 'Excellence In Teaching And Learning', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will frequently occurs that pleasures. Provide Endless Opportunities', '\"[\\\"A higher education qualification\\\",\\\"Better career prospects\\\",\\\"Better career prospects\\\",\\\"Better career prospects\\\"]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 6, 'en', '20+ Academic Curriculum', 'Onsest Schooled is home to more than 20,000 students and 230,000 alumni with a wide variety of interests, ages and backgrounds, the University reflects the city’s dynamic mix of populations.', '\"[\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\"]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 7, 'en', 'What’s Coming Up?', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 8, 'en', 'Latest From Our Blog', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 9, 'en', 'Our Gallery', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 10, 'en', 'Find Our <br> Contact Information', '', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 11, 'en', 'Contact By Department', 'Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 12, 'en', 'Our Featured Teachers', '', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 1, 'bn', '', '', '\"[{\\\"name\\\":\\\"\\\\u09ab\\\\u09c7\\\\u09b8\\\\u09ac\\\\u09c1\\\\u0995\\\",\\\"icon\\\":\\\"fab fa-facebook-f\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.facebook.com\\\"},{\\\"name\\\":\\\"\\\\u099f\\\\u09c1\\\\u0987\\\\u099f\\\\u09be\\\\u09b0\\\",\\\"icon\\\":\\\"fab fa-twitter\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.twitter.com\\\"},{\\\"name\\\":\\\"Pinterest\\\",\\\"icon\\\":\\\"fab fa-pinterest-p\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.pinterest.com\\\"},{\\\"name\\\":\\\"\\\\u0987\\\\u09a8\\\\u09b8\\\\u09cd\\\\u099f\\\\u09be\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u09ae\\\",\\\"icon\\\":\\\"fab fa-instagram\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.instagram.com\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 2, 'bn', 'Onest Schooled এর স্টেটমেন্ট', '', '\"[{\\\"title\\\":\\\"\\\\u09ae\\\\u09bf\\\\u09b6\\\\u09a8 \\\\u09ac\\\\u09bf\\\\u09ac\\\\u09c3\\\\u09a4\\\\u09bf\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09aa\\\\u09a1\\\\u09bc\\\\u09c1\\\\u09a8\\\"},{\\\"title\\\":\\\"\\\\u09a6\\\\u09c3\\\\u09b7\\\\u09cd\\\\u099f\\\\u09bf \\\\u09ac\\\\u09bf\\\\u09ac\\\\u09c3\\\\u09a4\\\\u09bf\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u09af\\\\u09bc\\\\u09be\\\\u09ae \\\\u0986\\\\u09aa\\\\u09a8\\\\u09be\\\\u0995\\\\u09c7 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09aa\\\\u09a1\\\\u09bc\\\\u09a4\\\\u09c7 \\\\u09b6\\\\u09bf\\\\u0996\\\\u09a4\\\\u09c7 \\\\u09b8\\\\u09be\\\\u09b9\\\\u09be\\\\u09af\\\\u09cd\\\\u09af \\\\u0995\\\\u09b0\\\\u09ac\\\\u09c7\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 3, 'bn', 'শিক্ষাদান এবং শেখার ক্ষেত্রে শ্রেষ্ঠত্ব', 'Onsest Schooled হল 20,000 টিরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রদের বিভিন্ন ধরনের আগ্রহ, বয়স এবং ব্যাকগ্রাউন্ড সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '\"[{\\\"icon\\\":8,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Onsest Schooled \\\\u09b9\\\\u09b2 20,000 \\\\u099f\\\\u09bf\\\\u09b0\\\\u0993 \\\\u09ac\\\\u09c7\\\\u09b6\\\\u09bf \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0 \\\\u098f\\\\u09ac\\\\u0982 230,000 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09be\\\\u0995\\\\u09cd\\\\u09a4\\\\u09a8 \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0\\\\u09a6\\\\u09c7\\\\u09b0 \\\\u09ac\\\\u09bf\\\\u09ad\\\\u09bf\\\\u09a8\\\\u09cd\\\\u09a8 \\\\u09a7\\\\u09b0\\\\u09a8\\\\u09c7\\\\u09b0 \\\\u0986\\\\u0997\\\\u09cd\\\\u09b0\\\\u09b9, \\\\u09ac\\\\u09af\\\\u09bc\\\\u09b8 \\\\u098f\\\\u09ac\\\\u0982 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u0995\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u0989\\\\u09a8\\\\u09cd\\\\u09a1 \\\\u09b8\\\\u09b9, \\\\u09ac\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09ac\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\\u099f\\\\u09bf \\\\u09b6\\\\u09b9\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u099c\\\\u09a8\\\\u09b8\\\\u0982\\\\u0996\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0 \\\\u0997\\\\u09a4\\\\u09bf\\\\u09b6\\\\u09c0\\\\u09b2 \\\\u09ae\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09b0\\\\u09a3\\\\u0995\\\\u09c7 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09a4\\\\u09bf\\\\u09ab\\\\u09b2\\\\u09bf\\\\u09a4 \\\\u0995\\\\u09b0\\\\u09c7\\\\u0964\\\"},{\\\"icon\\\":9,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Onsest Schooled \\\\u09b9\\\\u09b2 20,000 \\\\u099f\\\\u09bf\\\\u09b0\\\\u0993 \\\\u09ac\\\\u09c7\\\\u09b6\\\\u09bf \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0 \\\\u098f\\\\u09ac\\\\u0982 230,000 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09be\\\\u0995\\\\u09cd\\\\u09a4\\\\u09a8 \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0\\\\u09a6\\\\u09c7\\\\u09b0 \\\\u09ac\\\\u09bf\\\\u09ad\\\\u09bf\\\\u09a8\\\\u09cd\\\\u09a8 \\\\u09a7\\\\u09b0\\\\u09a8\\\\u09c7\\\\u09b0 \\\\u0986\\\\u0997\\\\u09cd\\\\u09b0\\\\u09b9, \\\\u09ac\\\\u09af\\\\u09bc\\\\u09b8 \\\\u098f\\\\u09ac\\\\u0982 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u0995\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u0989\\\\u09a8\\\\u09cd\\\\u09a1 \\\\u09b8\\\\u09b9, \\\\u09ac\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09ac\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\\u099f\\\\u09bf \\\\u09b6\\\\u09b9\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u099c\\\\u09a8\\\\u09b8\\\\u0982\\\\u0996\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0 \\\\u0997\\\\u09a4\\\\u09bf\\\\u09b6\\\\u09c0\\\\u09b2 \\\\u09ae\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09b0\\\\u09a3\\\\u0995\\\\u09c7 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09a4\\\\u09bf\\\\u09ab\\\\u09b2\\\\u09bf\\\\u09a4 \\\\u0995\\\\u09b0\\\\u09c7\\\\u0964\\\"},{\\\"icon\\\":10,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Onsest Schooled \\\\u09b9\\\\u09b2 20,000 \\\\u099f\\\\u09bf\\\\u09b0\\\\u0993 \\\\u09ac\\\\u09c7\\\\u09b6\\\\u09bf \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0 \\\\u098f\\\\u09ac\\\\u0982 230,000 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09be\\\\u0995\\\\u09cd\\\\u09a4\\\\u09a8 \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0\\\\u09a6\\\\u09c7\\\\u09b0 \\\\u09ac\\\\u09bf\\\\u09ad\\\\u09bf\\\\u09a8\\\\u09cd\\\\u09a8 \\\\u09a7\\\\u09b0\\\\u09a8\\\\u09c7\\\\u09b0 \\\\u0986\\\\u0997\\\\u09cd\\\\u09b0\\\\u09b9, \\\\u09ac\\\\u09af\\\\u09bc\\\\u09b8 \\\\u098f\\\\u09ac\\\\u0982 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u0995\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u0989\\\\u09a8\\\\u09cd\\\\u09a1 \\\\u09b8\\\\u09b9, \\\\u09ac\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09ac\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\\u099f\\\\u09bf \\\\u09b6\\\\u09b9\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u099c\\\\u09a8\\\\u09b8\\\\u0982\\\\u0996\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0 \\\\u0997\\\\u09a4\\\\u09bf\\\\u09b6\\\\u09c0\\\\u09b2 \\\\u09ae\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09b0\\\\u09a3\\\\u0995\\\\u09c7 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09a4\\\\u09bf\\\\u09ab\\\\u09b2\\\\u09bf\\\\u09a4 \\\\u0995\\\\u09b0\\\\u09c7\\\\u0964\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 4, 'bn', 'অনেস্ট স্কুলড এক্সপ্লোর করুন', '\"আমরা জ্ঞান এবং অপরিহার্য দক্ষতা শিক্ষা করি\" একটি বাক্যাংশ যা উভয় তাত্ত্বিক জ্ঞানের গুরুত্বের উপর জোর দেয়', '\"[{\\\"tab\\\":\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09ae\\\\u09cd\\\\u09aa\\\\u09be\\\\u09b8 \\\\u099c\\\\u09c0\\\\u09ac\\\\u09a8\\\",\\\"title\\\":\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09ae\\\\u09cd\\\\u09aa\\\\u09be\\\\u09b8 \\\\u099c\\\\u09c0\\\\u09ac\\\\u09a8\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"},{\\\"tab\\\":\\\"\\\\u098f\\\\u0995\\\\u09be\\\\u09a1\\\\u09c7\\\\u09ae\\\\u09bf\\\\u0995\\\",\\\"title\\\":\\\"\\\\u098f\\\\u0995\\\\u09be\\\\u09a1\\\\u09c7\\\\u09ae\\\\u09bf\\\\u0995\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"},{\\\"tab\\\":\\\"\\\\u0985\\\\u09cd\\\\u09af\\\\u09be\\\\u09a5\\\\u09b2\\\\u09c7\\\\u099f\\\\u09bf\\\\u0995\\\\u09cd\\\\u09b8\\\",\\\"title\\\":\\\"\\\\u0985\\\\u09cd\\\\u09af\\\\u09be\\\\u09a5\\\\u09b2\\\\u09c7\\\\u099f\\\\u09bf\\\\u0995\\\\u09cd\\\\u09b8\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"},{\\\"tab\\\":\\\"\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\",\\\"title\\\":\\\"\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"}]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 5, 'bn', 'শিক্ষাদান এবং শেখার ক্ষেত্রে শ্রেষ্ঠত্ব', 'স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া কিন্তু নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি দায়িত্বের দাবি বা ব্যবসার বাধ্যবাধকতাগুলির জন্য এটি প্রায়শই ঘটবে যে আনন্দ। অফুরন্ত সুযোগ প্রদান', '\"[\\\"\\\\u0989\\\\u099a\\\\u09cd\\\\u099a \\\\u09b6\\\\u09bf\\\\u0995\\\\u09cd\\\\u09b7\\\\u09be\\\\u0997\\\\u09a4 \\\\u09af\\\\u09cb\\\\u0997\\\\u09cd\\\\u09af\\\\u09a4\\\\u09be\\\",\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0\\\\u09bf\\\\u09af\\\\u09bc\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09ad\\\\u09be\\\\u09b2\\\\u09cb \\\\u09b8\\\\u09ae\\\\u09cd\\\\u09ad\\\\u09be\\\\u09ac\\\\u09a8\\\\u09be\\\",\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0\\\\u09bf\\\\u09af\\\\u09bc\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09ad\\\\u09be\\\\u09b2\\\\u09cb \\\\u09b8\\\\u09ae\\\\u09cd\\\\u09ad\\\\u09be\\\\u09ac\\\\u09a8\\\\u09be\\\",\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0\\\\u09bf\\\\u09af\\\\u09bc\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09ad\\\\u09be\\\\u09b2\\\\u09cb \\\\u09b8\\\\u09ae\\\\u09cd\\\\u09ad\\\\u09be\\\\u09ac\\\\u09a8\\\\u09be\\\"]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 6, 'bn', '20+ একাডেমিক পাঠ্যক্রম', 'Onsest Schooled হল 20,000 টিরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রদের বিভিন্ন ধরনের আগ্রহ, বয়স এবং ব্যাকগ্রাউন্ড সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।', '\"[\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\"]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 7, 'bn', 'কি আসছে?', 'স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া তবে নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি কর্তব্য বা বাধ্যবাধকতার দাবিতে হবে।', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 8, 'bn', 'আমাদের ব্লগ থেকে সর্বশেষ', 'স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া তবে নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি কর্তব্য বা বাধ্যবাধকতার দাবিতে হবে।', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 9, 'bn', 'আমাদের গ্যালারি', 'স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া তবে নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি কর্তব্য বা বাধ্যবাধকতার দাবিতে হবে।', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 10, 'bn', 'আমাদের যোগাযোগের তথ্য খুঁজুন', '', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 11, 'bn', 'বিভাগ দ্বারা যোগাযোগ', 'স্বাগত জানাই প্রতিটি কষ্টকে এড়িয়ে যাওয়া কিন্তু কিছু নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি দায়িত্বের দাবি বা ব্যবসার বাধ্যবাধকতার জন্য এটি করবে', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 12, 'bn', 'আমাদের বৈশিষ্ট্যযুক্ত শিক্ষক', '', '\"[]\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `name`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, '2025', '2025-01-01', '2025-12-31', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, '2026', '2026-01-01', '2026-12-31', 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `session_class_students`
--

CREATE TABLE `session_class_students` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `classes_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `result` tinyint NOT NULL DEFAULT '1' COMMENT '0 = fail, 1 = pass',
  `roll` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_translates`
--

CREATE TABLE `session_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'application_name', '\"Ischool Management System\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'address', '\"Resemont Tower, House 148, Road 13\\/B, Block E Banani Dhaka 1213.\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'phone', '\"+62 8787 8787\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'email', '\"info@school.test\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'school_about', '\"School Management Software (SMS) is a digital solution designed to simplify and automate administrative, academic, and operational tasks in educational institutions. It serves as a centralized platform to manage activities such as student records, attendance, fee collection, staff management, academic scheduling, and communication with parents.\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'footer_text', '\"\\u00a9 2025 Ischool. All rights reserved.\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'file_system', '\"local\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'aws_access_key_id', '\"AKIA3OGN2RWSJOR5UOTK\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 'aws_secret_key', '\"Vz18p5ELHI6BP9K7iZAzduu+sQCD\\/KkvbAwElmfX\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 'aws_region', '\"ap-southeast-1\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 'aws_bucket', '\"Ischool\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 'aws_endpoint', '\"https:\\/\\/s3.ap-southeast-1.amazonaws.com\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 'twilio_account_sid', '\"AC246311d660594a872734080bbb03a18b\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 'twilio_auth_token', '\"9e64cc0f85970ab0d0f055f541861742\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 'twilio_phone_number', '\"+14422426457\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 'recaptcha_sitekey', '\"6Lfn6nQhAAAAAKYauxvLddLtcqSn1yqn-HRn_CbN\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 'recaptcha_secret', '\"6Lfn6nQhAAAAABOzRtEjhZYB49Dd4orv41thfh02\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 'recaptcha_status', '\"0\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 'mail_drive', '\"smtp\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 'mail_host', '\"smtp.gmail.com\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 'mail_address', '\"info@school.test\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 'from_name', '\"Ischool - School Management System\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 'mail_username', '\"onestdev103@gmail.com\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 'mail_password', '\"eyJpdiI6IjNwZzc3OU13YWVuamtWUUErdTVyMGc9PSIsInZhbHVlIjoieDh5T3dhUEs2cCsydENiS2NiWHYxQ3lUTks2aThlSldJTmFMMnM2L1dtbz0iLCJtYWMiOiJjYWFjYmU0YjE2MmRlNzIxNDU2ZTA4YjMwOGI3OWI5Yzc4NzA5NWY2Mzc5YmM5MWRiNjk0MmE1NGIwMWVjZjFlIiwidGFnIjoiIn0=\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(25, 'mail_port', '\"587\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(26, 'encryption', '\"tls\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(27, 'default_langauge', '\"en\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(28, 'light_logo', '\"backend\\/uploads\\/settings\\/light.png\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(29, 'dark_logo', '\"backend\\/uploads\\/settings\\/dark.png\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(30, 'favicon', '\"backend\\/uploads\\/settings\\/favicon.png\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(31, 'session', '1', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(32, 'currency_code', '\"USD\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(33, 'map_key', '\"!1m18!1m12!1m3!1d3650.776241229233!2d90.40412657620105!3d23.790981078642808!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c72b14773d9d%3A0x21df6643cbfa879f!2sSookh!5e0!3m2!1sen!2sbd!4v1711600654298!5m2!1sen!2sbd\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(34, 'country', '\"United States of America\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(35, 'timezone', '\"America\\/New_York\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(36, 'tax_percentage', '5', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(37, 'tax_income_head', '\"Income Tax\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(38, 'tax_min_amount', '\"10000\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(39, 'tax_max_amount', '\"1000000\"', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(40, 'early_payment_discount_applicable', '0', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(41, 'siblings_discount_applicable', '0', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `setting_translates`
--

CREATE TABLE `setting_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `setting_id` bigint UNSIGNED DEFAULT NULL,
  `from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_settings',
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_translates`
--

INSERT INTO `setting_translates` (`id`, `setting_id`, `from`, `locale`, `name`, `value`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'general_settings', 'en', 'application_name', 'Ischool Management System', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 1, 'general_settings', 'bn', 'application_name', 'ওনেস্ট স্কুলড - স্কুল ম্যানেজমেন্ট সিস্টেম', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 6, 'general_settings', 'en', 'footer_text', '© 2025 Ischool. All rights reserved.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 6, 'general_settings', 'bn', 'footer_text', '© 2025 Ischool', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 2, 'general_settings', 'en', 'address', 'Resemont Tower, House 148, Road 13/B, Block E Banani Dhaka 1213.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 2, 'general_settings', 'bn', 'address', 'রেসিমন্ট টাওয়ার, হাউজ 148, রোড 13/বি, ব্লক ই বনানী ঢাকা 1213।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 35, 'general_settings', 'en', 'timezone', 'America/New_York', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 35, 'general_settings', 'bn', 'timezone', '+৬২ ৮৭৮৭ ৮৭৮৭', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 5, 'general_settings', 'en', 'school_about', 'School Management Software (SMS) is a digital solution designed to simplify and automate administrative, academic, and operational tasks in educational institutions. It serves as a centralized platform to manage activities such as student records, attendance, fee collection, staff management, academic scheduling, and communication with parents.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 5, 'general_settings', 'bn', 'school_about', 'Ischool Management System স্কুল ম্যানেজমেন্ট সফটওয়্যার (এসএমএস) হল একটি ডিজিটাল সমাধান যা শিক্ষা প্রতিষ্ঠানগুলিতে প্রশাসনিক, একাডেমিক এবং পরিচালনামূলক কাজগুলিকে সহজ এবং স্বয়ংক্রিয় করার জন্য ডিজাইন করা হয়েছে। এটি শিক্ষার্থীদের রেকর্ড, উপস্থিতি, ফি সংগ্রহ, কর্মী ব্যবস্থাপনা, একাডেমিক সময়সূচী এবং অভিভাবকদের সাথে যোগাযোগের মতো কার্যকলাপ পরিচালনা করার জন্য একটি কেন্দ্রীভূত প্ল্যাটফর্ম হিসেবে কাজ করে।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift_translates`
--

CREATE TABLE `shift_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sibling_fees_discounts`
--

CREATE TABLE `sibling_fees_discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `siblings_number` int DEFAULT NULL,
  `discount_percentage` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `serial` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `name`, `upload_id`, `description`, `serial`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'Let’s Build Your Future With Onest Shooled 1', 11, 'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 1.', 0, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'Let’s Build Your Future With Onest Shooled 2', 12, 'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 2.', 1, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'Let’s Build Your Future With Onest Shooled 3', 13, 'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 3.', 2, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `slider_translates`
--

CREATE TABLE `slider_translates` (
  `id` bigint UNSIGNED NOT NULL,
  `slider_id` bigint UNSIGNED DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slider_translates`
--

INSERT INTO `slider_translates` (`id`, `slider_id`, `locale`, `name`, `description`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 1, 'en', 'Let’s Build Your Future With Onest Shooled 1', 'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 1.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 2, 'en', 'Let’s Build Your Future With Onest Shooled 2', 'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 2.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 3, 'en', 'Let’s Build Your Future With Onest Shooled 3', 'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 3.', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 1, 'bn', 'আসুন Oneest Shooled 1 দিয়ে আপনার ভবিষ্যত গড়ে তুলি', 'চমৎকার পরিবেশ যেখানে শিশুরা শ্রমসাধ্য শারীরিক শিক্ষা গ্রহণ করে এবং বড় হয়। আমেট নরম, তারা কোথাও ছেড়ে যায় না, কিছু ব্যথা হতে দিন।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 2, 'bn', 'আসুন Oneest Shooled 2 দিয়ে আপনার ভবিষ্যত গড়ে তুলি', 'চমৎকার পরিবেশ যেখানে শিশুরা শ্রমসাধ্য শারীরিক শিক্ষা গ্রহণ করে এবং বড় হয়। আমেত একটুও হাল ছাড়ে না, তারা কোথাও ছাড়ে না, কিছু ব্যথা থাকুক।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 3, 'bn', 'আসুন Oneest Shooled 3 দিয়ে আপনার ভবিষ্যত গড়ে তুলি', 'চমৎকার পরিবেশ যেখানে শিশুরা শ্রমসাধ্য শারীরিক শিক্ষা গ্রহণ করে এবং বড় হয়। তারা আমাকে একা ছেড়ে যায় না।', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sms_mail_logs`
--

CREATE TABLE `sms_mail_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('mail','sms') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_description` longtext COLLATE utf8mb4_unicode_ci,
  `sms_description` text COLLATE utf8mb4_unicode_ci,
  `user_type` enum('role','individual','class') COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_ids` longtext COLLATE utf8mb4_unicode_ci,
  `role_id` int DEFAULT NULL,
  `individual_user_ids` longtext COLLATE utf8mb4_unicode_ci,
  `class_id` int DEFAULT NULL,
  `section_ids` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_mail_templates`
--

CREATE TABLE `sms_mail_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('mail','sms') COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` bigint UNSIGNED DEFAULT NULL,
  `mail_description` longtext COLLATE utf8mb4_unicode_ci,
  `sms_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `staff_id` int DEFAULT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `designation_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender_id` bigint UNSIGNED NOT NULL,
  `dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `current_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_salary` int DEFAULT NULL,
  `upload_documents` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint UNSIGNED NOT NULL,
  `admission_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roll_no` int DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `student_category_id` bigint UNSIGNED DEFAULT NULL,
  `religion_id` bigint UNSIGNED DEFAULT NULL,
  `blood_group_id` bigint UNSIGNED DEFAULT NULL,
  `gender_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `image_id` bigint UNSIGNED DEFAULT NULL,
  `parent_guardian_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `upload_documents` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `siblings_discount` tinyint NOT NULL DEFAULT '0',
  `previous_school` tinyint NOT NULL DEFAULT '0',
  `previous_school_info` text COLLATE utf8mb4_unicode_ci,
  `previous_school_image_id` bigint UNSIGNED DEFAULT NULL,
  `health_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_in_family` int NOT NULL DEFAULT '1',
  `siblings` int NOT NULL DEFAULT '0',
  `place_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpr_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spoken_lang_at_home` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residance_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_ar_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id_certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_absent_notifications`
--

CREATE TABLE `student_absent_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `notify_student` tinyint(1) NOT NULL DEFAULT '0',
  `notify_gurdian` tinyint(1) NOT NULL DEFAULT '1',
  `sending_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `notification_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_absent_notifications`
--

INSERT INTO `student_absent_notifications` (`id`, `notify_student`, `notify_gurdian`, `sending_time`, `active_status`, `notification_message`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 0, 1, '10:00', 1, 'Hi [guardian_name] , your child [student_name] on class [class] - ([section]) Admission [admission_no] is [attendance_type] on [attendance_date]  . For more contact [school_name]', '2025-06-03 07:04:07', '2025-06-03 07:04:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_categories`
--

CREATE TABLE `student_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_assigns`
--

CREATE TABLE `subject_assigns` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `classes_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_assign_childrens`
--

CREATE TABLE `subject_assign_childrens` (
  `id` bigint UNSIGNED NOT NULL,
  `subject_assign_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_attendances`
--

CREATE TABLE `subject_attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `classes_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `roll` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `attendance` tinyint DEFAULT '3' COMMENT '1=present, 2=late, 3=absent, 4=half_day, 5=Leave',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribes`
--

CREATE TABLE `subscribes` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_type` enum('prepaid','postpaid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prepaid',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int DEFAULT NULL,
  `student_limit` int DEFAULT NULL,
  `staff_limit` int DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `trx_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features_name` longtext COLLATE utf8mb4_unicode_ci,
  `features` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0 = inactive, 1 = active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `payment_type`, `name`, `price`, `student_limit`, `staff_limit`, `expiry_date`, `trx_id`, `method`, `features_name`, `features`, `status`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'prepaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reciver_id` int UNSIGNED NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_schedules`
--

CREATE TABLE `time_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `type` tinyint NOT NULL COMMENT 'Class = 1, Exam = 2',
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `path`, `created_at`, `updated_at`, `branch_id`) VALUES
(1, 'backend/uploads/users/user-icon-1.png', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(2, 'backend/uploads/users/user-icon-2.png', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(3, 'backend/uploads/users/user-icon-3.png', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(4, 'backend/uploads/users/user-icon-4.png', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(5, 'frontend/img/accreditation/accreditation.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(6, 'frontend/img/banner/cta_bg.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(7, 'frontend/img/explore/1.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(8, 'frontend/img/icon/1.svg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(9, 'frontend/img/icon/2.svg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(10, 'frontend/img/icon/3.svg', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(11, 'frontend/img/sliders/03.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(12, 'frontend/img/sliders/02.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(13, 'frontend/img/sliders/01.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(14, 'frontend/img/counters/01.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(15, 'frontend/img/counters/02.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(16, 'frontend/img/counters/03.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(17, 'frontend/img/counters/04.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(18, 'frontend/img/counters/05.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(19, 'frontend/img/blog/01.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(20, 'frontend/img/blog/02.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(21, 'frontend/img/blog/03.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(22, 'frontend/img/blog/04.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(23, 'frontend/img/blog/05.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(24, 'frontend/img/blog/06.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(25, 'frontend/img/blog/07.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(26, 'frontend/img/blog/08.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(27, 'frontend/img/blog/09.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(28, 'frontend/img/blog/10.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(29, 'frontend/img/blog/11.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(30, 'frontend/img/blog/12.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(31, 'frontend/img/blog/13.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(32, 'frontend/img/gallery/1.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(33, 'frontend/img/gallery/2.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(34, 'frontend/img/gallery/3.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(35, 'frontend/img/gallery/4.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(36, 'frontend/img/gallery/5.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(37, 'frontend/img/gallery/6.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(38, 'frontend/img/gallery/7.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(39, 'frontend/img/gallery/8.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(40, 'frontend/img/gallery/9.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(41, 'frontend/img/gallery/10.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(42, 'frontend/img/gallery/11.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(43, 'frontend/img/gallery/12.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(44, 'frontend/img/gallery/13.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(45, 'frontend/img/gallery/14.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(46, 'frontend/img/gallery/15.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(47, 'frontend/img/gallery/16.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(48, 'frontend/img/gallery/17.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(49, 'frontend/img/gallery/18.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(50, 'frontend/img/gallery/19.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(51, 'frontend/img/gallery/20.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(52, 'frontend/img/gallery/21.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(53, 'frontend/img/gallery/22.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(54, 'frontend/img/gallery/23.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(55, 'frontend/img/gallery/24.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(56, 'frontend/img/contact/contact_1.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(57, 'frontend/img/contact/contact_2.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(58, 'frontend/img/contact/contact_3.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(59, 'frontend/img/contact/contact_4.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(60, 'frontend/img/contact/admission_1.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(61, 'frontend/img/contact/admission_2.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(62, 'frontend/img/contact/admission_3.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(63, 'frontend/img/contact/admission_4.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(64, 'frontend/img/about-gallery/1.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(65, 'frontend/img/about-gallery/icon_1.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(66, 'frontend/img/about-gallery/2.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(67, 'frontend/img/about-gallery/icon_2.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(68, 'frontend/img/about-gallery/3.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1),
(69, 'frontend/img/about-gallery/icon_3.webp', '2025-06-03 07:04:08', '2025-06-03 07:04:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'For student login',
  `date_of_birth` date DEFAULT NULL,
  `gender` tinyint NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'if null then verifield, not null then not verified',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token for email/phone verification, if null then verifield, not null then not verified',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `branch_id` bigint UNSIGNED NOT NULL DEFAULT '1',
  `upload_id` bigint UNSIGNED DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `designation_id` bigint UNSIGNED DEFAULT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` longtext COLLATE utf8mb4_unicode_ci COMMENT 'device_token from firebase',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_password_otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `admission_no`, `date_of_birth`, `gender`, `email_verified_at`, `token`, `phone`, `password`, `permissions`, `last_login`, `status`, `branch_id`, `upload_id`, `role_id`, `designation_id`, `uuid`, `device_token`, `remember_token`, `reset_password_otp`, `created_at`, `updated_at`, `username`) VALUES
(1, 'Super Admin', 'admin@telesom.com', NULL, '2022-09-07', 1, '2025-06-03 07:04:09', NULL, '01811000000', '$2y$10$gEKYaIEroCIdSWucXI5s2umxwBI18h5uhwsB/.bI4JWds/mHv9vDq', '[\"counter_read\",\"fees_collesction_read\",\"revenue_read\",\"fees_collection_this_month_read\",\"income_expense_read\",\"upcoming_events_read\",\"attendance_chart_read\",\"calendar_read\",\"student_read\",\"student_create\",\"student_update\",\"student_delete\",\"student_category_read\",\"student_category_create\",\"student_category_update\",\"student_category_delete\",\"promote_students_read\",\"promote_students_create\",\"disabled_students_read\",\"disabled_students_create\",\"parent_read\",\"parent_create\",\"parent_update\",\"parent_delete\",\"admission_read\",\"admission_create\",\"admission_update\",\"admission_delete\",\"classes_read\",\"classes_create\",\"classes_update\",\"classes_delete\",\"section_read\",\"section_create\",\"section_update\",\"section_delete\",\"shift_read\",\"shift_create\",\"shift_update\",\"shift_delete\",\"class_setup_read\",\"class_setup_create\",\"class_setup_update\",\"class_setup_delete\",\"subject_read\",\"subject_create\",\"subject_update\",\"subject_delete\",\"subject_assign_read\",\"subject_assign_create\",\"subject_assign_update\",\"subject_assign_delete\",\"class_routine_read\",\"class_routine_create\",\"class_routine_update\",\"class_routine_delete\",\"time_schedule_read\",\"time_schedule_create\",\"time_schedule_update\",\"time_schedule_delete\",\"class_room_read\",\"class_room_create\",\"class_room_update\",\"class_room_delete\",\"fees_group_read\",\"fees_group_create\",\"fees_group_update\",\"fees_group_delete\",\"fees_type_read\",\"fees_type_create\",\"fees_type_update\",\"fees_type_delete\",\"fees_master_read\",\"fees_master_create\",\"fees_master_update\",\"fees_master_delete\",\"fees_assign_read\",\"fees_assign_create\",\"fees_assign_update\",\"fees_assign_delete\",\"fees_collect_read\",\"fees_collect_create\",\"fees_collect_update\",\"fees_collect_delete\",\"exam_type_read\",\"exam_type_create\",\"exam_type_update\",\"exam_type_delete\",\"marks_grade_read\",\"marks_grade_create\",\"marks_grade_update\",\"marks_grade_delete\",\"exam_assign_read\",\"exam_assign_create\",\"exam_assign_update\",\"exam_assign_delete\",\"exam_routine_read\",\"exam_routine_create\",\"exam_routine_update\",\"exam_routine_delete\",\"marks_register_read\",\"marks_register_create\",\"marks_register_update\",\"marks_register_delete\",\"homework_read\",\"homework_create\",\"homework_update\",\"homework_delete\",\"exam_setting_read\",\"exam_setting_update\",\"account_head_read\",\"account_head_create\",\"account_head_update\",\"account_head_delete\",\"income_read\",\"income_create\",\"income_update\",\"income_delete\",\"expense_read\",\"expense_create\",\"expense_update\",\"expense_delete\",\"attendance_read\",\"attendance_create\",\"report_marksheet_read\",\"report_merit_list_read\",\"report_progress_card_read\",\"report_due_fees_read\",\"report_fees_collection_read\",\"report_account_read\",\"report_class_routine_read\",\"report_exam_routine_read\",\"report_attendance_read\",\"language_read\",\"language_create\",\"language_update\",\"language_update_terms\",\"language_delete\",\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"department_read\",\"department_create\",\"department_update\",\"department_delete\",\"designation_read\",\"designation_create\",\"designation_update\",\"designation_delete\",\"page_sections_read\",\"page_sections_update\",\"slider_read\",\"slider_create\",\"slider_update\",\"slider_delete\",\"about_read\",\"about_create\",\"about_update\",\"about_delete\",\"counter_read\",\"counter_create\",\"counter_update\",\"counter_delete\",\"contact_info_read\",\"contact_info_create\",\"contact_info_update\",\"contact_info_delete\",\"dep_contact_read\",\"dep_contact_create\",\"dep_contact_update\",\"dep_contact_delete\",\"news_read\",\"news_create\",\"news_update\",\"news_delete\",\"event_read\",\"event_create\",\"event_update\",\"event_delete\",\"gallery_category_read\",\"gallery_category_create\",\"gallery_category_update\",\"gallery_category_delete\",\"gallery_read\",\"gallery_create\",\"gallery_update\",\"gallery_delete\",\"subscribe_read\",\"contact_message_read\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_update\",\"task_schedules_read\",\"task_schedules_update\",\"software_update_read\",\"software_update_update\",\"recaptcha_settings_read\",\"recaptcha_settings_update\",\"payment_gateway_settings_read\",\"payment_gateway_settings_update\",\"email_settings_read\",\"email_settings_update\",\"sms_settings_read\",\"sms_settings_update\",\"gender_read\",\"gender_create\",\"gender_update\",\"gender_delete\",\"religion_read\",\"religion_create\",\"religion_update\",\"religion_delete\",\"blood_group_read\",\"blood_group_create\",\"blood_group_update\",\"blood_group_delete\",\"session_read\",\"session_create\",\"session_update\",\"session_delete\",\"book_category_read\",\"book_category_create\",\"book_category_update\",\"book_category_delete\",\"book_read\",\"book_create\",\"book_update\",\"book_delete\",\"member_read\",\"member_create\",\"member_update\",\"member_delete\",\"member_category_read\",\"member_category_create\",\"member_category_update\",\"member_category_delete\",\"issue_book_read\",\"issue_book_create\",\"issue_book_update\",\"issue_book_delete\",\"online_exam_type_read\",\"online_exam_type_create\",\"online_exam_type_update\",\"online_exam_type_delete\",\"question_group_read\",\"question_group_create\",\"question_group_update\",\"question_group_delete\",\"question_bank_read\",\"question_bank_create\",\"question_bank_update\",\"question_bank_delete\",\"online_exam_read\",\"online_exam_create\",\"online_exam_update\",\"online_exam_delete\",\"id_card_read\",\"id_card_create\",\"id_card_update\",\"id_card_delete\",\"id_card_generate_read\",\"certificate_read\",\"certificate_create\",\"certificate_update\",\"certificate_delete\",\"certificate_generate_read\",\"homework_read\",\"homework_create\",\"homework_update\",\"homework_delete\",\"gmeet_read\",\"gmeet_create\",\"gmeet_update\",\"gmeet_delete\",\"notice_board_read\",\"notice_board_create\",\"notice_board_update\",\"notice_board_delete\",\"sms_mail_template_read\",\"sms_mail_template_create\",\"nsms_mail_templateupdate\",\"sms_mail_template_delete\",\"sms_mail_read\",\"sms_mail_send\"]', NULL, 1, 1, 1, 1, 5, '331e4c52-9fba-4389-a12f-ce9f179b14b9', NULL, 'RYspN8vPRmfcREVkPEt4Jvq8fU2Mm1Ww7p4zpJtdl7mCkUzISPVneCVh3W8B', NULL, '2025-06-03 07:04:08', '2025-06-03 07:04:09', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abouts_upload_id_foreign` (`upload_id`),
  ADD KEY `abouts_icon_upload_id_foreign` (`icon_upload_id`);

--
-- Indexes for table `about_translates`
--
ALTER TABLE `about_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `about_translates_about_id_foreign` (`about_id`);

--
-- Indexes for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answers_online_exam_id_foreign` (`online_exam_id`),
  ADD KEY `answers_student_id_foreign` (`student_id`);

--
-- Indexes for table `answer_childrens`
--
ALTER TABLE `answer_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answer_childrens_answer_id_foreign` (`answer_id`),
  ADD KEY `answer_childrens_question_bank_id_foreign` (`question_bank_id`);

--
-- Indexes for table `assign_fees_discounts`
--
ALTER TABLE `assign_fees_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assign_fees_discounts_fees_assign_children_id_foreign` (`fees_assign_children_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_session_id_foreign` (`session_id`),
  ADD KEY `attendances_student_id_foreign` (`student_id`),
  ADD KEY `attendances_classes_id_foreign` (`classes_id`),
  ADD KEY `attendances_section_id_foreign` (`section_id`);

--
-- Indexes for table `blood_groups`
--
ALTER TABLE `blood_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `books_category_id_foreign` (`category_id`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificates_bg_image_foreign` (`bg_image`),
  ADD KEY `certificates_bottom_left_signature_foreign` (`bottom_left_signature`),
  ADD KEY `certificates_bottom_right_signature_foreign` (`bottom_right_signature`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_rooms`
--
ALTER TABLE `class_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_routines`
--
ALTER TABLE `class_routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_routines_session_id_foreign` (`session_id`),
  ADD KEY `class_routines_classes_id_foreign` (`classes_id`),
  ADD KEY `class_routines_section_id_foreign` (`section_id`),
  ADD KEY `class_routines_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `class_routine_childrens`
--
ALTER TABLE `class_routine_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_routine_childrens_class_routine_id_foreign` (`class_routine_id`),
  ADD KEY `class_routine_childrens_subject_id_foreign` (`subject_id`),
  ADD KEY `class_routine_childrens_time_schedule_id_foreign` (`time_schedule_id`),
  ADD KEY `class_routine_childrens_class_room_id_foreign` (`class_room_id`);

--
-- Indexes for table `class_section_translates`
--
ALTER TABLE `class_section_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_translates_section_id_foreign` (`section_id`);

--
-- Indexes for table `class_setups`
--
ALTER TABLE `class_setups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_setups_session_id_foreign` (`session_id`),
  ADD KEY `class_setups_classes_id_foreign` (`classes_id`);

--
-- Indexes for table `class_setup_childrens`
--
ALTER TABLE `class_setup_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_setup_childrens_class_setup_id_foreign` (`class_setup_id`),
  ADD KEY `class_setup_childrens_section_id_foreign` (`section_id`);

--
-- Indexes for table `class_translates`
--
ALTER TABLE `class_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_translates_class_id_foreign` (`class_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_infos`
--
ALTER TABLE `contact_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_infos_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `contact_info_translates`
--
ALTER TABLE `contact_info_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_info_translates_contact_info_id_foreign` (`contact_info_id`);

--
-- Indexes for table `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `counters_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `counter_translates`
--
ALTER TABLE `counter_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `counter_translates_counter_id_foreign` (`counter_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_code_unique` (`code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_staff_user_id_foreign` (`staff_user_id`);

--
-- Indexes for table `department_contacts`
--
ALTER TABLE `department_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_contacts_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `department_contact_translates`
--
ALTER TABLE `department_contact_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_contact_translates_department_contact_id_foreign` (`department_contact_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `early_payment_discounts`
--
ALTER TABLE `early_payment_discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_session_id_foreign` (`session_id`),
  ADD KEY `events_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `event_translates`
--
ALTER TABLE `event_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_translates_event_id_foreign` (`event_id`);

--
-- Indexes for table `examination_results`
--
ALTER TABLE `examination_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `examination_results_session_id_foreign` (`session_id`),
  ADD KEY `examination_results_exam_type_id_foreign` (`exam_type_id`),
  ADD KEY `examination_results_classes_id_foreign` (`classes_id`),
  ADD KEY `examination_results_section_id_foreign` (`section_id`),
  ADD KEY `examination_results_student_id_foreign` (`student_id`);

--
-- Indexes for table `examination_settings`
--
ALTER TABLE `examination_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `examination_settings_session_id_foreign` (`session_id`);

--
-- Indexes for table `exam_assigns`
--
ALTER TABLE `exam_assigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_assigns_session_id_foreign` (`session_id`),
  ADD KEY `exam_assigns_classes_id_foreign` (`classes_id`),
  ADD KEY `exam_assigns_section_id_foreign` (`section_id`),
  ADD KEY `exam_assigns_exam_type_id_foreign` (`exam_type_id`),
  ADD KEY `exam_assigns_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `exam_assign_childrens`
--
ALTER TABLE `exam_assign_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_assign_childrens_exam_assign_id_foreign` (`exam_assign_id`);

--
-- Indexes for table `exam_routines`
--
ALTER TABLE `exam_routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_routines_session_id_foreign` (`session_id`),
  ADD KEY `exam_routines_classes_id_foreign` (`classes_id`),
  ADD KEY `exam_routines_section_id_foreign` (`section_id`),
  ADD KEY `exam_routines_type_id_foreign` (`type_id`);

--
-- Indexes for table `exam_routine_childrens`
--
ALTER TABLE `exam_routine_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_routine_childrens_exam_routine_id_foreign` (`exam_routine_id`),
  ADD KEY `exam_routine_childrens_subject_id_foreign` (`subject_id`),
  ADD KEY `exam_routine_childrens_time_schedule_id_foreign` (`time_schedule_id`),
  ADD KEY `exam_routine_childrens_class_room_id_foreign` (`class_room_id`);

--
-- Indexes for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_session_id_foreign` (`session_id`),
  ADD KEY `expenses_expense_head_foreign` (`expense_head`),
  ADD KEY `expenses_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fees_assigns`
--
ALTER TABLE `fees_assigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_assigns_session_id_foreign` (`session_id`),
  ADD KEY `fees_assigns_classes_id_foreign` (`classes_id`),
  ADD KEY `fees_assigns_section_id_foreign` (`section_id`),
  ADD KEY `fees_assigns_category_id_foreign` (`category_id`),
  ADD KEY `fees_assigns_gender_id_foreign` (`gender_id`),
  ADD KEY `fees_assigns_fees_group_id_foreign` (`fees_group_id`);

--
-- Indexes for table `fees_assign_childrens`
--
ALTER TABLE `fees_assign_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_assign_childrens_fees_assign_id_foreign` (`fees_assign_id`),
  ADD KEY `fees_assign_childrens_fees_master_id_foreign` (`fees_master_id`),
  ADD KEY `fees_assign_childrens_student_id_foreign` (`student_id`);

--
-- Indexes for table `fees_collects`
--
ALTER TABLE `fees_collects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_collects_fees_assign_children_id_foreign` (`fees_assign_children_id`),
  ADD KEY `fees_collects_fees_collect_by_foreign` (`fees_collect_by`),
  ADD KEY `fees_collects_student_id_foreign` (`student_id`),
  ADD KEY `fees_collects_session_id_foreign` (`session_id`);

--
-- Indexes for table `fees_groups`
--
ALTER TABLE `fees_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_masters`
--
ALTER TABLE `fees_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_masters_session_id_foreign` (`session_id`),
  ADD KEY `fees_masters_fees_group_id_foreign` (`fees_group_id`),
  ADD KEY `fees_masters_fees_type_id_foreign` (`fees_type_id`);

--
-- Indexes for table `fees_master_childrens`
--
ALTER TABLE `fees_master_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_master_childrens_fees_master_id_foreign` (`fees_master_id`),
  ADD KEY `fees_master_childrens_fees_type_id_foreign` (`fees_type_id`);

--
-- Indexes for table `fees_types`
--
ALTER TABLE `fees_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flag_icons`
--
ALTER TABLE `flag_icons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_posts_upload_id_foreign` (`upload_id`),
  ADD KEY `forum_posts_created_by_foreign` (`created_by`);

--
-- Indexes for table `forum_post_comments`
--
ALTER TABLE `forum_post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_post_comments_parent_id_foreign` (`parent_id`),
  ADD KEY `forum_post_comments_forum_post_id_foreign` (`forum_post_id`),
  ADD KEY `forum_post_comments_published_by_foreign` (`published_by`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `galleries_gallery_category_id_foreign` (`gallery_category_id`),
  ADD KEY `galleries_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_category_translates`
--
ALTER TABLE `gallery_category_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gallery_category_translates_gallery_category_id_foreign` (`gallery_category_id`);

--
-- Indexes for table `genders`
--
ALTER TABLE `genders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gender_translates`
--
ALTER TABLE `gender_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gender_translates_gender_id_foreign` (`gender_id`);

--
-- Indexes for table `gmeets`
--
ALTER TABLE `gmeets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gmeets_session_id_foreign` (`session_id`),
  ADD KEY `gmeets_classes_id_foreign` (`classes_id`),
  ADD KEY `gmeets_section_id_foreign` (`section_id`),
  ADD KEY `gmeets_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`),
  ADD KEY `homework_session_id_foreign` (`session_id`),
  ADD KEY `homework_classes_id_foreign` (`classes_id`),
  ADD KEY `homework_section_id_foreign` (`section_id`),
  ADD KEY `homework_subject_id_foreign` (`subject_id`),
  ADD KEY `homework_document_id_foreign` (`document_id`),
  ADD KEY `homework_created_by_foreign` (`created_by`);

--
-- Indexes for table `homework_students`
--
ALTER TABLE `homework_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `homework_students_student_id_foreign` (`student_id`),
  ADD KEY `homework_students_homework_id_foreign` (`homework_id`),
  ADD KEY `homework_students_homework_foreign` (`homework`);

--
-- Indexes for table `id_cards`
--
ALTER TABLE `id_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cards_frontside_bg_image_foreign` (`frontside_bg_image`),
  ADD KEY `id_cards_backside_bg_image_foreign` (`backside_bg_image`),
  ADD KEY `id_cards_signature_foreign` (`signature`),
  ADD KEY `id_cards_qr_code_foreign` (`qr_code`);

--
-- Indexes for table `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incomes_session_id_foreign` (`session_id`),
  ADD KEY `incomes_income_head_foreign` (`income_head`),
  ADD KEY `incomes_upload_id_foreign` (`upload_id`),
  ADD KEY `incomes_fees_collect_id_foreign` (`fees_collect_id`);

--
-- Indexes for table `issue_books`
--
ALTER TABLE `issue_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_books_user_id_foreign` (`user_id`),
  ADD KEY `issue_books_book_id_foreign` (`book_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `leave_requests_user_id_foreign` (`user_id`),
  ADD KEY `leave_requests_role_id_foreign` (`role_id`),
  ADD KEY `leave_requests_request_by_foreign` (`request_by`),
  ADD KEY `leave_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `leave_requests_session_id_foreign` (`session_id`),
  ADD KEY `leave_requests_attachment_id_foreign` (`attachment_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_types_role_id_foreign` (`role_id`);

--
-- Indexes for table `marks_grades`
--
ALTER TABLE `marks_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marks_grades_session_id_foreign` (`session_id`);

--
-- Indexes for table `marks_registers`
--
ALTER TABLE `marks_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marks_registers_session_id_foreign` (`session_id`),
  ADD KEY `marks_registers_classes_id_foreign` (`classes_id`),
  ADD KEY `marks_registers_section_id_foreign` (`section_id`),
  ADD KEY `marks_registers_exam_type_id_foreign` (`exam_type_id`),
  ADD KEY `marks_registers_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `marks_register_childrens`
--
ALTER TABLE `marks_register_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marks_register_childrens_marks_register_id_foreign` (`marks_register_id`),
  ADD KEY `marks_register_childrens_student_id_foreign` (`student_id`);

--
-- Indexes for table `mark_sheet_approvals`
--
ALTER TABLE `mark_sheet_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mark_sheet_approvals_session_id_foreign` (`session_id`),
  ADD KEY `mark_sheet_approvals_classes_id_foreign` (`classes_id`),
  ADD KEY `mark_sheet_approvals_section_id_foreign` (`section_id`),
  ADD KEY `mark_sheet_approvals_exam_type_id_foreign` (`exam_type_id`),
  ADD KEY `mark_sheet_approvals_student_id_foreign` (`student_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `members_user_id_foreign` (`user_id`),
  ADD KEY `members_category_id_foreign` (`category_id`);

--
-- Indexes for table `member_categories`
--
ALTER TABLE `member_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memories`
--
ALTER TABLE `memories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `memories_feature_image_id_foreign` (`feature_image_id`),
  ADD KEY `memories_created_by_foreign` (`created_by`);

--
-- Indexes for table `memory_galleries`
--
ALTER TABLE `memory_galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `memory_galleries_memory_id_foreign` (`memory_id`),
  ADD KEY `memory_galleries_gallery_image_id_foreign` (`gallery_image_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `news_translates`
--
ALTER TABLE `news_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_translates_news_id_foreign` (`news_id`);

--
-- Indexes for table `notice_boards`
--
ALTER TABLE `notice_boards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notice_boards_session_id_foreign` (`session_id`),
  ADD KEY `notice_boards_class_id_foreign` (`class_id`),
  ADD KEY `notice_boards_section_id_foreign` (`section_id`),
  ADD KEY `notice_boards_student_id_foreign` (`student_id`),
  ADD KEY `notice_boards_department_id_foreign` (`department_id`),
  ADD KEY `notice_boards_attachment_foreign` (`attachment`);

--
-- Indexes for table `notice_board_translates`
--
ALTER TABLE `notice_board_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notice_board_translates_notice_board_id_foreign` (`notice_board_id`);

--
-- Indexes for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_admissions`
--
ALTER TABLE `online_admissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_admissions_payslip_image_id_foreign` (`payslip_image_id`),
  ADD KEY `online_admissions_shift_id_foreign` (`shift_id`),
  ADD KEY `online_admissions_session_id_foreign` (`session_id`),
  ADD KEY `online_admissions_classes_id_foreign` (`classes_id`),
  ADD KEY `online_admissions_section_id_foreign` (`section_id`),
  ADD KEY `online_admissions_religion_id_foreign` (`religion_id`),
  ADD KEY `online_admissions_gender_id_foreign` (`gender_id`),
  ADD KEY `online_admissions_student_image_id_foreign` (`student_image_id`),
  ADD KEY `online_admissions_previous_school_image_id_foreign` (`previous_school_image_id`),
  ADD KEY `online_admissions_gurdian_image_id_foreign` (`gurdian_image_id`),
  ADD KEY `online_admissions_father_image_id_foreign` (`father_image_id`),
  ADD KEY `online_admissions_mother_image_id_foreign` (`mother_image_id`);

--
-- Indexes for table `online_admission_fees_assigns`
--
ALTER TABLE `online_admission_fees_assigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_admission_fees_assigns_fees_group_id_foreign` (`fees_group_id`),
  ADD KEY `online_admission_fees_assigns_session_id_foreign` (`session_id`),
  ADD KEY `online_admission_fees_assigns_class_id_foreign` (`class_id`),
  ADD KEY `online_admission_fees_assigns_section_id_foreign` (`section_id`);

--
-- Indexes for table `online_admission_payments`
--
ALTER TABLE `online_admission_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_admission_settings`
--
ALTER TABLE `online_admission_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_exams`
--
ALTER TABLE `online_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exams_session_id_foreign` (`session_id`),
  ADD KEY `online_exams_classes_id_foreign` (`classes_id`),
  ADD KEY `online_exams_section_id_foreign` (`section_id`),
  ADD KEY `online_exams_subject_id_foreign` (`subject_id`),
  ADD KEY `online_exams_exam_type_id_foreign` (`exam_type_id`),
  ADD KEY `online_exams_question_group_id_foreign` (`question_group_id`);

--
-- Indexes for table `online_exam_children_questions`
--
ALTER TABLE `online_exam_children_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_children_questions_online_exam_id_foreign` (`online_exam_id`),
  ADD KEY `online_exam_children_questions_question_bank_id_foreign` (`question_bank_id`);

--
-- Indexes for table `online_exam_children_students`
--
ALTER TABLE `online_exam_children_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_children_students_online_exam_id_foreign` (`online_exam_id`),
  ADD KEY `online_exam_children_students_student_id_foreign` (`student_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_sections_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `page_translates`
--
ALTER TABLE `page_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_translates_page_id_foreign` (`page_id`);

--
-- Indexes for table `parent_guardians`
--
ALTER TABLE `parent_guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_guardians_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `promote_students`
--
ALTER TABLE `promote_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_banks`
--
ALTER TABLE `question_banks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_banks_session_id_foreign` (`session_id`),
  ADD KEY `question_banks_question_group_id_foreign` (`question_group_id`);

--
-- Indexes for table `question_bank_childrens`
--
ALTER TABLE `question_bank_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_bank_childrens_question_bank_id_foreign` (`question_bank_id`);

--
-- Indexes for table `question_groups`
--
ALTER TABLE `question_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_groups_session_id_foreign` (`session_id`);

--
-- Indexes for table `religions`
--
ALTER TABLE `religions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `religon_translates`
--
ALTER TABLE `religon_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `religon_translates_religion_id_foreign` (`religion_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `searches`
--
ALTER TABLE `searches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section_translates`
--
ALTER TABLE `section_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_translates_section_id_foreign` (`section_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session_class_students`
--
ALTER TABLE `session_class_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_class_students_session_id_foreign` (`session_id`),
  ADD KEY `session_class_students_student_id_foreign` (`student_id`),
  ADD KEY `session_class_students_classes_id_foreign` (`classes_id`),
  ADD KEY `session_class_students_section_id_foreign` (`section_id`),
  ADD KEY `session_class_students_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `session_translates`
--
ALTER TABLE `session_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_translates_session_id_foreign` (`session_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_translates`
--
ALTER TABLE `setting_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setting_translates_setting_id_foreign` (`setting_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_translates`
--
ALTER TABLE `shift_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_translates_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `sibling_fees_discounts`
--
ALTER TABLE `sibling_fees_discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sliders_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `slider_translates`
--
ALTER TABLE `slider_translates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slider_translates_slider_id_foreign` (`slider_id`);

--
-- Indexes for table `sms_mail_logs`
--
ALTER TABLE `sms_mail_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_mail_templates`
--
ALTER TABLE `sms_mail_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sms_mail_templates_attachment_foreign` (`attachment`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_user_id_foreign` (`user_id`),
  ADD KEY `staff_role_id_foreign` (`role_id`),
  ADD KEY `staff_designation_id_foreign` (`designation_id`),
  ADD KEY `staff_department_id_foreign` (`department_id`),
  ADD KEY `staff_gender_id_foreign` (`gender_id`),
  ADD KEY `staff_upload_id_foreign` (`upload_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_religion_id_foreign` (`religion_id`),
  ADD KEY `students_blood_group_id_foreign` (`blood_group_id`),
  ADD KEY `students_gender_id_foreign` (`gender_id`),
  ADD KEY `students_category_id_foreign` (`category_id`),
  ADD KEY `students_image_id_foreign` (`image_id`),
  ADD KEY `students_parent_guardian_id_foreign` (`parent_guardian_id`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_department_id_foreign` (`department_id`),
  ADD KEY `students_previous_school_image_id_foreign` (`previous_school_image_id`);

--
-- Indexes for table `student_absent_notifications`
--
ALTER TABLE `student_absent_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_categories`
--
ALTER TABLE `student_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_assigns`
--
ALTER TABLE `subject_assigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_assigns_session_id_foreign` (`session_id`),
  ADD KEY `subject_assigns_classes_id_foreign` (`classes_id`),
  ADD KEY `subject_assigns_section_id_foreign` (`section_id`);

--
-- Indexes for table `subject_assign_childrens`
--
ALTER TABLE `subject_assign_childrens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_assign_childrens_subject_assign_id_foreign` (`subject_assign_id`),
  ADD KEY `subject_assign_childrens_subject_id_foreign` (`subject_id`),
  ADD KEY `subject_assign_childrens_staff_id_foreign` (`staff_id`);

--
-- Indexes for table `subject_attendances`
--
ALTER TABLE `subject_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_attendances_session_id_foreign` (`session_id`),
  ADD KEY `subject_attendances_student_id_foreign` (`student_id`),
  ADD KEY `subject_attendances_classes_id_foreign` (`classes_id`),
  ADD KEY `subject_attendances_section_id_foreign` (`section_id`),
  ADD KEY `subject_attendances_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `subscribes`
--
ALTER TABLE `subscribes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscribes_email_unique` (`email`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_schedules`
--
ALTER TABLE `time_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_upload_id_foreign` (`upload_id`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `about_translates`
--
ALTER TABLE `about_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `account_heads`
--
ALTER TABLE `account_heads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `answer_childrens`
--
ALTER TABLE `answer_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assign_fees_discounts`
--
ALTER TABLE `assign_fees_discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_rooms`
--
ALTER TABLE `class_rooms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_routines`
--
ALTER TABLE `class_routines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_routine_childrens`
--
ALTER TABLE `class_routine_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_section_translates`
--
ALTER TABLE `class_section_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_setups`
--
ALTER TABLE `class_setups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_setup_childrens`
--
ALTER TABLE `class_setup_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_translates`
--
ALTER TABLE `class_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_infos`
--
ALTER TABLE `contact_infos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_info_translates`
--
ALTER TABLE `contact_info_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `counters`
--
ALTER TABLE `counters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `counter_translates`
--
ALTER TABLE `counter_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `department_contacts`
--
ALTER TABLE `department_contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `department_contact_translates`
--
ALTER TABLE `department_contact_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `early_payment_discounts`
--
ALTER TABLE `early_payment_discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_translates`
--
ALTER TABLE `event_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `examination_results`
--
ALTER TABLE `examination_results`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `examination_settings`
--
ALTER TABLE `examination_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_assigns`
--
ALTER TABLE `exam_assigns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_assign_childrens`
--
ALTER TABLE `exam_assign_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_routines`
--
ALTER TABLE `exam_routines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_routine_childrens`
--
ALTER TABLE `exam_routine_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_types`
--
ALTER TABLE `exam_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_assigns`
--
ALTER TABLE `fees_assigns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_assign_childrens`
--
ALTER TABLE `fees_assign_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_collects`
--
ALTER TABLE `fees_collects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_groups`
--
ALTER TABLE `fees_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_masters`
--
ALTER TABLE `fees_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_master_childrens`
--
ALTER TABLE `fees_master_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_types`
--
ALTER TABLE `fees_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flag_icons`
--
ALTER TABLE `flag_icons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_post_comments`
--
ALTER TABLE `forum_post_comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gallery_category_translates`
--
ALTER TABLE `gallery_category_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `genders`
--
ALTER TABLE `genders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gender_translates`
--
ALTER TABLE `gender_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmeets`
--
ALTER TABLE `gmeets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homework_students`
--
ALTER TABLE `homework_students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `id_cards`
--
ALTER TABLE `id_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_books`
--
ALTER TABLE `issue_books`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marks_grades`
--
ALTER TABLE `marks_grades`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marks_registers`
--
ALTER TABLE `marks_registers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marks_register_childrens`
--
ALTER TABLE `marks_register_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mark_sheet_approvals`
--
ALTER TABLE `mark_sheet_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_categories`
--
ALTER TABLE `member_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memories`
--
ALTER TABLE `memories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memory_galleries`
--
ALTER TABLE `memory_galleries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `news_translates`
--
ALTER TABLE `news_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notice_boards`
--
ALTER TABLE `notice_boards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_board_translates`
--
ALTER TABLE `notice_board_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_settings`
--
ALTER TABLE `notification_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `online_admissions`
--
ALTER TABLE `online_admissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_admission_fees_assigns`
--
ALTER TABLE `online_admission_fees_assigns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_admission_payments`
--
ALTER TABLE `online_admission_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_admission_settings`
--
ALTER TABLE `online_admission_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `online_exams`
--
ALTER TABLE `online_exams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_children_questions`
--
ALTER TABLE `online_exam_children_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_children_students`
--
ALTER TABLE `online_exam_children_students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `page_translates`
--
ALTER TABLE `page_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parent_guardians`
--
ALTER TABLE `parent_guardians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promote_students`
--
ALTER TABLE `promote_students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_banks`
--
ALTER TABLE `question_banks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_bank_childrens`
--
ALTER TABLE `question_bank_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_groups`
--
ALTER TABLE `question_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `religions`
--
ALTER TABLE `religions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `religon_translates`
--
ALTER TABLE `religon_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `searches`
--
ALTER TABLE `searches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section_translates`
--
ALTER TABLE `section_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `session_class_students`
--
ALTER TABLE `session_class_students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_translates`
--
ALTER TABLE `session_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `setting_translates`
--
ALTER TABLE `setting_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_translates`
--
ALTER TABLE `shift_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sibling_fees_discounts`
--
ALTER TABLE `sibling_fees_discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `slider_translates`
--
ALTER TABLE `slider_translates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sms_mail_logs`
--
ALTER TABLE `sms_mail_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_mail_templates`
--
ALTER TABLE `sms_mail_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_absent_notifications`
--
ALTER TABLE `student_absent_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_categories`
--
ALTER TABLE `student_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject_assigns`
--
ALTER TABLE `subject_assigns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject_assign_childrens`
--
ALTER TABLE `subject_assign_childrens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject_attendances`
--
ALTER TABLE `subject_attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribes`
--
ALTER TABLE `subscribes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_schedules`
--
ALTER TABLE `time_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abouts`
--
ALTER TABLE `abouts`
  ADD CONSTRAINT `abouts_icon_upload_id_foreign` FOREIGN KEY (`icon_upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `abouts_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `about_translates`
--
ALTER TABLE `about_translates`
  ADD CONSTRAINT `about_translates_about_id_foreign` FOREIGN KEY (`about_id`) REFERENCES `abouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `answer_childrens`
--
ALTER TABLE `answer_childrens`
  ADD CONSTRAINT `answer_childrens_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answer_childrens_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assign_fees_discounts`
--
ALTER TABLE `assign_fees_discounts`
  ADD CONSTRAINT `assign_fees_discounts_fees_assign_children_id_foreign` FOREIGN KEY (`fees_assign_children_id`) REFERENCES `fees_assign_childrens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `book_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_bg_image_foreign` FOREIGN KEY (`bg_image`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_bottom_left_signature_foreign` FOREIGN KEY (`bottom_left_signature`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_bottom_right_signature_foreign` FOREIGN KEY (`bottom_right_signature`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_routines`
--
ALTER TABLE `class_routines`
  ADD CONSTRAINT `class_routines_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_routines_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_routines_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_routines_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_routine_childrens`
--
ALTER TABLE `class_routine_childrens`
  ADD CONSTRAINT `class_routine_childrens_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_routine_childrens_class_routine_id_foreign` FOREIGN KEY (`class_routine_id`) REFERENCES `class_routines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_routine_childrens_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_routine_childrens_time_schedule_id_foreign` FOREIGN KEY (`time_schedule_id`) REFERENCES `time_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_section_translates`
--
ALTER TABLE `class_section_translates`
  ADD CONSTRAINT `class_section_translates_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_setups`
--
ALTER TABLE `class_setups`
  ADD CONSTRAINT `class_setups_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_setups_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_setup_childrens`
--
ALTER TABLE `class_setup_childrens`
  ADD CONSTRAINT `class_setup_childrens_class_setup_id_foreign` FOREIGN KEY (`class_setup_id`) REFERENCES `class_setups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_setup_childrens_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_translates`
--
ALTER TABLE `class_translates`
  ADD CONSTRAINT `class_translates_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_infos`
--
ALTER TABLE `contact_infos`
  ADD CONSTRAINT `contact_infos_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_info_translates`
--
ALTER TABLE `contact_info_translates`
  ADD CONSTRAINT `contact_info_translates_contact_info_id_foreign` FOREIGN KEY (`contact_info_id`) REFERENCES `contact_infos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `counters`
--
ALTER TABLE `counters`
  ADD CONSTRAINT `counters_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `counter_translates`
--
ALTER TABLE `counter_translates`
  ADD CONSTRAINT `counter_translates_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_staff_user_id_foreign` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `department_contacts`
--
ALTER TABLE `department_contacts`
  ADD CONSTRAINT `department_contacts_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `department_contact_translates`
--
ALTER TABLE `department_contact_translates`
  ADD CONSTRAINT `department_contact_translates_department_contact_id_foreign` FOREIGN KEY (`department_contact_id`) REFERENCES `department_contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_translates`
--
ALTER TABLE `event_translates`
  ADD CONSTRAINT `event_translates_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `examination_results`
--
ALTER TABLE `examination_results`
  ADD CONSTRAINT `examination_results_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `examination_results_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `examination_results_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `examination_results_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `examination_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `examination_settings`
--
ALTER TABLE `examination_settings`
  ADD CONSTRAINT `examination_settings_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_assigns`
--
ALTER TABLE `exam_assigns`
  ADD CONSTRAINT `exam_assigns_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_assigns_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_assigns_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_assign_childrens`
--
ALTER TABLE `exam_assign_childrens`
  ADD CONSTRAINT `exam_assign_childrens_exam_assign_id_foreign` FOREIGN KEY (`exam_assign_id`) REFERENCES `exam_assigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_routines`
--
ALTER TABLE `exam_routines`
  ADD CONSTRAINT `exam_routines_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_routines_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_routines_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_routines_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_routine_childrens`
--
ALTER TABLE `exam_routine_childrens`
  ADD CONSTRAINT `exam_routine_childrens_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_routine_childrens_exam_routine_id_foreign` FOREIGN KEY (`exam_routine_id`) REFERENCES `exam_routines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_routine_childrens_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_routine_childrens_time_schedule_id_foreign` FOREIGN KEY (`time_schedule_id`) REFERENCES `time_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_expense_head_foreign` FOREIGN KEY (`expense_head`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees_assigns`
--
ALTER TABLE `fees_assigns`
  ADD CONSTRAINT `fees_assigns_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assigns_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assigns_fees_group_id_foreign` FOREIGN KEY (`fees_group_id`) REFERENCES `fees_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assigns_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees_assign_childrens`
--
ALTER TABLE `fees_assign_childrens`
  ADD CONSTRAINT `fees_assign_childrens_fees_assign_id_foreign` FOREIGN KEY (`fees_assign_id`) REFERENCES `fees_assigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assign_childrens_fees_master_id_foreign` FOREIGN KEY (`fees_master_id`) REFERENCES `fees_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_assign_childrens_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees_collects`
--
ALTER TABLE `fees_collects`
  ADD CONSTRAINT `fees_collects_fees_assign_children_id_foreign` FOREIGN KEY (`fees_assign_children_id`) REFERENCES `fees_assign_childrens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_collects_fees_collect_by_foreign` FOREIGN KEY (`fees_collect_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_collects_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_collects_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees_masters`
--
ALTER TABLE `fees_masters`
  ADD CONSTRAINT `fees_masters_fees_group_id_foreign` FOREIGN KEY (`fees_group_id`) REFERENCES `fees_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_masters_fees_type_id_foreign` FOREIGN KEY (`fees_type_id`) REFERENCES `fees_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_masters_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees_master_childrens`
--
ALTER TABLE `fees_master_childrens`
  ADD CONSTRAINT `fees_master_childrens_fees_master_id_foreign` FOREIGN KEY (`fees_master_id`) REFERENCES `fees_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_master_childrens_fees_type_id_foreign` FOREIGN KEY (`fees_type_id`) REFERENCES `fees_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_post_comments`
--
ALTER TABLE `forum_post_comments`
  ADD CONSTRAINT `forum_post_comments_forum_post_id_foreign` FOREIGN KEY (`forum_post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_post_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forum_post_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_post_comments_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_gallery_category_id_foreign` FOREIGN KEY (`gallery_category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `galleries_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery_category_translates`
--
ALTER TABLE `gallery_category_translates`
  ADD CONSTRAINT `gallery_category_translates_gallery_category_id_foreign` FOREIGN KEY (`gallery_category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gender_translates`
--
ALTER TABLE `gender_translates`
  ADD CONSTRAINT `gender_translates_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeets`
--
ALTER TABLE `gmeets`
  ADD CONSTRAINT `gmeets_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeets_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeets_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeets_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homework`
--
ALTER TABLE `homework`
  ADD CONSTRAINT `homework_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homework_students`
--
ALTER TABLE `homework_students`
  ADD CONSTRAINT `homework_students_homework_foreign` FOREIGN KEY (`homework`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_students_homework_id_foreign` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `id_cards`
--
ALTER TABLE `id_cards`
  ADD CONSTRAINT `id_cards_backside_bg_image_foreign` FOREIGN KEY (`backside_bg_image`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_cards_frontside_bg_image_foreign` FOREIGN KEY (`frontside_bg_image`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_cards_qr_code_foreign` FOREIGN KEY (`qr_code`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_cards_signature_foreign` FOREIGN KEY (`signature`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `incomes`
--
ALTER TABLE `incomes`
  ADD CONSTRAINT `incomes_fees_collect_id_foreign` FOREIGN KEY (`fees_collect_id`) REFERENCES `fees_collects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incomes_income_head_foreign` FOREIGN KEY (`income_head`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incomes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incomes_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_books`
--
ALTER TABLE `issue_books`
  ADD CONSTRAINT `issue_books_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_books_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_attachment_id_foreign` FOREIGN KEY (`attachment_id`) REFERENCES `uploads` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_request_by_foreign` FOREIGN KEY (`request_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD CONSTRAINT `leave_types_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks_grades`
--
ALTER TABLE `marks_grades`
  ADD CONSTRAINT `marks_grades_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks_registers`
--
ALTER TABLE `marks_registers`
  ADD CONSTRAINT `marks_registers_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_registers_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_registers_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_registers_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_registers_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks_register_childrens`
--
ALTER TABLE `marks_register_childrens`
  ADD CONSTRAINT `marks_register_childrens_marks_register_id_foreign` FOREIGN KEY (`marks_register_id`) REFERENCES `marks_registers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_register_childrens_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mark_sheet_approvals`
--
ALTER TABLE `mark_sheet_approvals`
  ADD CONSTRAINT `mark_sheet_approvals_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mark_sheet_approvals_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mark_sheet_approvals_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mark_sheet_approvals_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mark_sheet_approvals_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `member_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `memories`
--
ALTER TABLE `memories`
  ADD CONSTRAINT `memories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memories_feature_image_id_foreign` FOREIGN KEY (`feature_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `memory_galleries`
--
ALTER TABLE `memory_galleries`
  ADD CONSTRAINT `memory_galleries_gallery_image_id_foreign` FOREIGN KEY (`gallery_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memory_galleries_memory_id_foreign` FOREIGN KEY (`memory_id`) REFERENCES `memories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news_translates`
--
ALTER TABLE `news_translates`
  ADD CONSTRAINT `news_translates_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_boards`
--
ALTER TABLE `notice_boards`
  ADD CONSTRAINT `notice_boards_attachment_foreign` FOREIGN KEY (`attachment`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_boards_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_boards_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `notice_boards_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_boards_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_boards_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_board_translates`
--
ALTER TABLE `notice_board_translates`
  ADD CONSTRAINT `notice_board_translates_notice_board_id_foreign` FOREIGN KEY (`notice_board_id`) REFERENCES `notice_boards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_admissions`
--
ALTER TABLE `online_admissions`
  ADD CONSTRAINT `online_admissions_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_father_image_id_foreign` FOREIGN KEY (`father_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_gurdian_image_id_foreign` FOREIGN KEY (`gurdian_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_mother_image_id_foreign` FOREIGN KEY (`mother_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_payslip_image_id_foreign` FOREIGN KEY (`payslip_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_previous_school_image_id_foreign` FOREIGN KEY (`previous_school_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_student_image_id_foreign` FOREIGN KEY (`student_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_admission_fees_assigns`
--
ALTER TABLE `online_admission_fees_assigns`
  ADD CONSTRAINT `online_admission_fees_assigns_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admission_fees_assigns_fees_group_id_foreign` FOREIGN KEY (`fees_group_id`) REFERENCES `fees_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admission_fees_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admission_fees_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exams`
--
ALTER TABLE `online_exams`
  ADD CONSTRAINT `online_exams_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exams_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exams_question_group_id_foreign` FOREIGN KEY (`question_group_id`) REFERENCES `question_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exams_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exams_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_children_questions`
--
ALTER TABLE `online_exam_children_questions`
  ADD CONSTRAINT `online_exam_children_questions_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_children_questions_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_children_students`
--
ALTER TABLE `online_exam_children_students`
  ADD CONSTRAINT `online_exam_children_students_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_children_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD CONSTRAINT `page_sections_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `page_translates`
--
ALTER TABLE `page_translates`
  ADD CONSTRAINT `page_translates_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parent_guardians`
--
ALTER TABLE `parent_guardians`
  ADD CONSTRAINT `parent_guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_banks`
--
ALTER TABLE `question_banks`
  ADD CONSTRAINT `question_banks_question_group_id_foreign` FOREIGN KEY (`question_group_id`) REFERENCES `question_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_banks_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_bank_childrens`
--
ALTER TABLE `question_bank_childrens`
  ADD CONSTRAINT `question_bank_childrens_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_groups`
--
ALTER TABLE `question_groups`
  ADD CONSTRAINT `question_groups_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `religon_translates`
--
ALTER TABLE `religon_translates`
  ADD CONSTRAINT `religon_translates_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_translates`
--
ALTER TABLE `section_translates`
  ADD CONSTRAINT `section_translates_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `session_class_students`
--
ALTER TABLE `session_class_students`
  ADD CONSTRAINT `session_class_students_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_class_students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_class_students_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_class_students_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_class_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `session_translates`
--
ALTER TABLE `session_translates`
  ADD CONSTRAINT `session_translates_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_translates`
--
ALTER TABLE `setting_translates`
  ADD CONSTRAINT `setting_translates_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_translates`
--
ALTER TABLE `shift_translates`
  ADD CONSTRAINT `shift_translates_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sliders`
--
ALTER TABLE `sliders`
  ADD CONSTRAINT `sliders_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slider_translates`
--
ALTER TABLE `slider_translates`
  ADD CONSTRAINT `slider_translates_slider_id_foreign` FOREIGN KEY (`slider_id`) REFERENCES `sliders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sms_mail_templates`
--
ALTER TABLE `sms_mail_templates`
  ADD CONSTRAINT `sms_mail_templates_attachment_foreign` FOREIGN KEY (`attachment`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_blood_group_id_foreign` FOREIGN KEY (`blood_group_id`) REFERENCES `blood_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `students_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_parent_guardian_id_foreign` FOREIGN KEY (`parent_guardian_id`) REFERENCES `parent_guardians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_previous_school_image_id_foreign` FOREIGN KEY (`previous_school_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_assigns`
--
ALTER TABLE `subject_assigns`
  ADD CONSTRAINT `subject_assigns_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_assign_childrens`
--
ALTER TABLE `subject_assign_childrens`
  ADD CONSTRAINT `subject_assign_childrens_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_assign_childrens_subject_assign_id_foreign` FOREIGN KEY (`subject_assign_id`) REFERENCES `subject_assigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_assign_childrens_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_attendances`
--
ALTER TABLE `subject_attendances`
  ADD CONSTRAINT `subject_attendances_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_attendances_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
