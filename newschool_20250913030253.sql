-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: newschool
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `about_translates`
--

DROP TABLE IF EXISTS `about_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `about_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `about_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `about_translates_about_id_foreign` (`about_id`),
  CONSTRAINT `about_translates_about_id_foreign` FOREIGN KEY (`about_id`) REFERENCES `abouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_translates`
--

LOCK TABLES `about_translates` WRITE;
/*!40000 ALTER TABLE `about_translates` DISABLE KEYS */;
INSERT INTO `about_translates` VALUES (1,1,'en','Special Campus Tour','Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Graduation','Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Powerful Alumni','Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,1,'bn','বিশেষ ক্যাম্পাস সফর','তারা খুব নরম এবং কোথাও কোন ব্যথা আছে ছেড়ে না. তিনি তার পরিবারের যত্ন নিতে পছন্দ করেন। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,2,'bn','স্নাতক','তারা খুব নরম এবং কোথাও কোন ব্যথা আছে ছেড়ে না. তিনি তার পরিবারের যত্ন নিতে পছন্দ করেন। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,3,'bn','শক্তিশালী প্রাক্তন ছাত্র','তারা খুব নরম এবং কোথাও কোন ব্যথা আছে ছেড়ে না. তিনি তার পরিবারের যত্ন নিতে পছন্দ করেন। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে। অনুশীলন ফলপ্রসূ হবে। কারণ সে নরম হতে চায়। অনুশীলন ফলপ্রসূ হবে।','2025-06-03 07:04:09','2025-06-03 07:04:09',1);
/*!40000 ALTER TABLE `about_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `abouts`
--

DROP TABLE IF EXISTS `abouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `icon_upload_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `serial` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `abouts_upload_id_foreign` (`upload_id`),
  KEY `abouts_icon_upload_id_foreign` (`icon_upload_id`),
  CONSTRAINT `abouts_icon_upload_id_foreign` FOREIGN KEY (`icon_upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `abouts_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abouts`
--

LOCK TABLES `abouts` WRITE;
/*!40000 ALTER TABLE `abouts` DISABLE KEYS */;
INSERT INTO `abouts` VALUES (1,'Special Campus Tour',64,65,'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.',0,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Graduation',66,67,'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.',1,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Powerful Alumni',68,69,'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Exercitation veniam consequat sunt nostrud amet. enim velit mollit. Exercitation veniam consequat sunt nostrud amet.',2,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `abouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_level_configs`
--

DROP TABLE IF EXISTS `academic_level_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_level_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_level` enum('primary','secondary','high_school','kg') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Academic level being configured',
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Human-readable name for this academic level (e.g., "Elementary School", "Middle School")',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Optional description of what this academic level represents',
  `class_identifiers` json NOT NULL COMMENT 'JSON array of class names/numbers that belong to this academic level',
  `numeric_range` json DEFAULT NULL COMMENT 'JSON object with min/max numeric class values for easy range checking',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Display order for academic levels in interfaces',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether this academic level configuration is currently active',
  `auto_assign_mandatory_services` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Automatically assign mandatory services when students are assigned to this level',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_level_configs_created_by_foreign` (`created_by`),
  KEY `academic_level_configs_updated_by_foreign` (`updated_by`),
  KEY `idx_academic_level_configs_level` (`academic_level`),
  KEY `idx_academic_level_configs_active_sort` (`is_active`,`sort_order`),
  CONSTRAINT `academic_level_configs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `academic_level_configs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_level_configs`
--

LOCK TABLES `academic_level_configs` WRITE;
/*!40000 ALTER TABLE `academic_level_configs` DISABLE KEYS */;
INSERT INTO `academic_level_configs` VALUES (1,'kg','Kindergarten','Kindergarten students (KG-1 to KG-3)','[\"KG\", \"KG-1\", \"KG-2\", \"KG-3\", \"PreK\", \"Pre-K\", \"Nursery\", \"Pre-School\"]','{\"max\": 0, \"min\": -3}',1,1,1,NULL,NULL,'2025-09-09 08:34:25','2025-09-11 04:19:01'),(2,'primary','Primary School','Primary education levels (Grade 1 to Grade 8)','[\"1\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"Class 1\", \"Class 2\", \"Class 3\", \"Class 4\", \"Class 5\", \"Class 6\", \"Class 7\", \"Class 8\", \"Grade 1\", \"Grade 2\", \"Grade 3\", \"Grade 4\", \"Grade 5\", \"Grade 6\", \"Grade 7\", \"Grade 8\"]','{\"max\": 8, \"min\": 1}',2,1,1,NULL,NULL,'2025-09-09 08:34:25','2025-09-11 04:19:01'),(3,'secondary','Secondary School','Secondary education levels (Form 1 to Form 4)','[\"Form 1\", \"Form 2\", \"Form 3\", \"Form 4\", \"F1\", \"F2\", \"F3\", \"F4\"]','{\"max\": 104, \"min\": 101}',3,1,1,NULL,NULL,'2025-09-09 08:34:25','2025-09-11 04:19:01'),(4,'high_school','High School','High school levels (if applicable)','[\"11\", \"12\", \"Class 11\", \"Class 12\", \"Grade 11\", \"Grade 12\"]','{\"max\": 12, \"min\": 11}',4,1,1,NULL,NULL,'2025-09-09 08:34:25','2025-09-11 04:19:01');
/*!40000 ALTER TABLE `academic_level_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_heads`
--

DROP TABLE IF EXISTS `account_heads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_heads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_heads`
--

LOCK TABLES `account_heads` WRITE;
/*!40000 ALTER TABLE `account_heads` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_heads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `answer_childrens`
--

DROP TABLE IF EXISTS `answer_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `answer_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `answer_id` bigint unsigned NOT NULL,
  `question_bank_id` bigint unsigned NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `evaluation_mark` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `answer_childrens_answer_id_foreign` (`answer_id`),
  KEY `answer_childrens_question_bank_id_foreign` (`question_bank_id`),
  CONSTRAINT `answer_childrens_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `answer_childrens_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer_childrens`
--

LOCK TABLES `answer_childrens` WRITE;
/*!40000 ALTER TABLE `answer_childrens` DISABLE KEYS */;
/*!40000 ALTER TABLE `answer_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `result` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `answers_online_exam_id_foreign` (`online_exam_id`),
  KEY `answers_student_id_foreign` (`student_id`),
  CONSTRAINT `answers_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `answers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assign_fees_discounts`
--

DROP TABLE IF EXISTS `assign_fees_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assign_fees_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fees_assign_children_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_amount` double NOT NULL,
  `discount_percentage` double NOT NULL,
  `discount_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assign_fees_discounts_fees_assign_children_id_foreign` (`fees_assign_children_id`),
  CONSTRAINT `assign_fees_discounts_fees_assign_children_id_foreign` FOREIGN KEY (`fees_assign_children_id`) REFERENCES `fees_assign_childrens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assign_fees_discounts`
--

LOCK TABLES `assign_fees_discounts` WRITE;
/*!40000 ALTER TABLE `assign_fees_discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `assign_fees_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `classes_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `roll` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `attendance` tinyint DEFAULT '3' COMMENT '1=present, 2=late, 3=absent, 4=half_day',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `attendances_session_id_foreign` (`session_id`),
  KEY `attendances_student_id_foreign` (`student_id`),
  KEY `attendances_classes_id_foreign` (`classes_id`),
  KEY `attendances_section_id_foreign` (`section_id`),
  CONSTRAINT `attendances_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (1,1,31,1,1,'1','2025-09-03',1,'','2025-09-02 22:50:18','2025-09-02 22:58:31',1),(2,1,57,1,1,'27','2025-09-03',1,'','2025-09-02 22:50:18','2025-09-02 22:58:31',1),(3,1,61,1,1,'31','2025-09-03',1,'','2025-09-02 22:50:18','2025-09-02 22:58:31',1),(4,1,32,2,1,'2','2025-09-03',1,'','2025-09-02 22:57:46','2025-09-02 22:57:46',1),(5,1,58,2,1,'28','2025-09-03',1,'','2025-09-02 22:57:46','2025-09-02 22:57:46',1),(6,1,33,3,1,'3','2025-09-03',4,'biriiga ka dib muu iman','2025-09-03 00:24:56','2025-09-03 00:24:56',1),(7,1,59,3,1,'29','2025-09-03',1,'','2025-09-03 00:24:56','2025-09-03 00:24:56',1),(8,1,34,4,1,'4','2025-09-03',1,'','2025-09-03 03:46:20','2025-09-03 03:46:20',1),(9,1,60,4,1,'30','2025-09-03',1,'','2025-09-03 03:46:20','2025-09-03 03:46:20',1),(10,1,33,3,1,'3','2025-09-04',2,'biriiga ka dib muu iman','2025-09-03 22:12:38','2025-09-03 22:12:38',1),(11,1,59,3,1,'29','2025-09-04',1,'','2025-09-03 22:12:38','2025-09-03 22:12:38',1),(12,1,32,2,1,'2','2025-09-08',4,'tyrty','2025-09-08 01:36:34','2025-09-08 01:36:34',1),(13,1,58,2,1,'28','2025-09-08',1,'','2025-09-08 01:36:34','2025-09-08 01:36:34',1),(14,1,31,1,1,'1','2025-09-10',2,'','2025-09-10 06:17:01','2025-09-10 06:17:01',1),(15,1,57,1,1,'27','2025-09-10',3,'','2025-09-10 06:17:01','2025-09-10 06:17:01',1),(16,1,61,1,1,'31','2025-09-10',4,'','2025-09-10 06:17:01','2025-09-10 06:17:01',1);
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blood_groups`
--

DROP TABLE IF EXISTS `blood_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blood_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blood_groups`
--

LOCK TABLES `blood_groups` WRITE;
/*!40000 ALTER TABLE `blood_groups` DISABLE KEYS */;
INSERT INTO `blood_groups` VALUES (1,'A+',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'A-',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'B+',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'B-',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'O+',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'O-',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'AB+',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'AB-',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `blood_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_categories`
--

DROP TABLE IF EXISTS `book_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_categories`
--

LOCK TABLES `book_categories` WRITE;
/*!40000 ALTER TABLE `book_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rack_no` int NOT NULL,
  `price` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `books_category_id_foreign` (`category_id`),
  CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `book_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `long` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `country_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Noradin Secondary(Jigjiga yar)','1234562433','noradin-jigjigayar@example.com','Hargeisa, Jigajiga Yar','23.8103','90.4125','1',1,'2025-06-03 07:04:08','2025-08-31 06:19:55'),(2,'Noradin Secondary (Sh.madar)','34242445','noradin-shmadar@noradin.com','Hargeisa,Sh.madar',NULL,NULL,'1',1,'2025-08-31 06:21:21','2025-08-31 06:21:21');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `top_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_show` tinyint(1) NOT NULL DEFAULT '1',
  `bg_image` bigint unsigned DEFAULT NULL,
  `bottom_left_signature` bigint unsigned DEFAULT NULL,
  `bottom_left_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bottom_right_signature` bigint unsigned DEFAULT NULL,
  `bottom_right_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` tinyint(1) NOT NULL DEFAULT '1',
  `name` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `certificates_bg_image_foreign` (`bg_image`),
  KEY `certificates_bottom_left_signature_foreign` (`bottom_left_signature`),
  KEY `certificates_bottom_right_signature_foreign` (`bottom_right_signature`),
  CONSTRAINT `certificates_bg_image_foreign` FOREIGN KEY (`bg_image`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_bottom_left_signature_foreign` FOREIGN KEY (`bottom_left_signature`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_bottom_right_signature_foreign` FOREIGN KEY (`bottom_right_signature`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_rooms`
--

DROP TABLE IF EXISTS `class_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_no` int DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_rooms`
--

LOCK TABLES `class_rooms` WRITE;
/*!40000 ALTER TABLE `class_rooms` DISABLE KEYS */;
INSERT INTO `class_rooms` VALUES (1,1,50,1,'2025-08-31 23:45:22','2025-08-31 23:45:22',1),(2,2,60,1,'2025-08-31 23:45:31','2025-08-31 23:45:31',1),(3,3,70,1,'2025-08-31 23:46:01','2025-08-31 23:46:01',1),(4,4,100,1,'2025-08-31 23:46:17','2025-08-31 23:46:17',1),(5,5,120,1,'2025-08-31 23:46:28','2025-08-31 23:46:28',1),(6,6,100,1,'2025-08-31 23:46:38','2025-08-31 23:46:38',1),(7,7,100,1,'2025-08-31 23:46:46','2025-08-31 23:46:46',1),(8,8,100,1,'2025-08-31 23:46:54','2025-08-31 23:46:54',1);
/*!40000 ALTER TABLE `class_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_routine_childrens`
--

DROP TABLE IF EXISTS `class_routine_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_routine_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_routine_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `time_schedule_id` bigint unsigned NOT NULL,
  `class_room_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_routine_childrens_class_routine_id_foreign` (`class_routine_id`),
  KEY `class_routine_childrens_subject_id_foreign` (`subject_id`),
  KEY `class_routine_childrens_time_schedule_id_foreign` (`time_schedule_id`),
  KEY `class_routine_childrens_class_room_id_foreign` (`class_room_id`),
  CONSTRAINT `class_routine_childrens_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_routine_childrens_class_routine_id_foreign` FOREIGN KEY (`class_routine_id`) REFERENCES `class_routines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_routine_childrens_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_routine_childrens_time_schedule_id_foreign` FOREIGN KEY (`time_schedule_id`) REFERENCES `time_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_routine_childrens`
--

LOCK TABLES `class_routine_childrens` WRITE;
/*!40000 ALTER TABLE `class_routine_childrens` DISABLE KEYS */;
INSERT INTO `class_routine_childrens` VALUES (1,1,2,1,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1),(2,1,1,2,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1),(3,1,3,3,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1),(4,1,7,4,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1),(5,1,16,5,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1),(6,1,12,6,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1),(7,1,9,7,2,'2025-09-06 23:54:42','2025-09-06 23:54:42',1);
/*!40000 ALTER TABLE `class_routine_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_routines`
--

DROP TABLE IF EXISTS `class_routines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_routines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `shift_id` bigint unsigned DEFAULT NULL,
  `day` tinyint DEFAULT NULL COMMENT 'sat=1, fri=7',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_routines_session_id_foreign` (`session_id`),
  KEY `class_routines_classes_id_foreign` (`classes_id`),
  KEY `class_routines_section_id_foreign` (`section_id`),
  KEY `class_routines_shift_id_foreign` (`shift_id`),
  CONSTRAINT `class_routines_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_routines_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_routines_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_routines_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_routines`
--

LOCK TABLES `class_routines` WRITE;
/*!40000 ALTER TABLE `class_routines` DISABLE KEYS */;
INSERT INTO `class_routines` VALUES (1,1,1,1,1,1,'2025-09-06 23:54:42','2025-09-06 23:54:42',1);
/*!40000 ALTER TABLE `class_routines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_section_translates`
--

DROP TABLE IF EXISTS `class_section_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_section_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_section_translates_section_id_foreign` (`section_id`),
  CONSTRAINT `class_section_translates_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_section_translates`
--

LOCK TABLES `class_section_translates` WRITE;
/*!40000 ALTER TABLE `class_section_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_section_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_setup_childrens`
--

DROP TABLE IF EXISTS `class_setup_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_setup_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_setup_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_setup_childrens_class_setup_id_foreign` (`class_setup_id`),
  KEY `class_setup_childrens_section_id_foreign` (`section_id`),
  CONSTRAINT `class_setup_childrens_class_setup_id_foreign` FOREIGN KEY (`class_setup_id`) REFERENCES `class_setups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_setup_childrens_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_setup_childrens`
--

LOCK TABLES `class_setup_childrens` WRITE;
/*!40000 ALTER TABLE `class_setup_childrens` DISABLE KEYS */;
INSERT INTO `class_setup_childrens` VALUES (1,1,1,1,'2025-08-31 09:18:53','2025-08-31 09:18:53',1),(2,2,1,1,'2025-08-31 09:19:13','2025-08-31 09:19:13',1),(3,3,1,1,'2025-08-31 09:19:50','2025-08-31 09:19:50',1),(4,4,1,1,'2025-08-31 09:20:01','2025-08-31 09:20:01',1),(5,5,1,1,'2025-08-31 09:20:23','2025-08-31 09:20:23',1),(6,6,1,1,'2025-08-31 09:20:45','2025-08-31 09:20:45',1),(7,7,1,1,'2025-08-31 09:21:03','2025-08-31 09:21:03',1),(8,8,1,1,'2025-08-31 09:21:25','2025-08-31 09:21:25',1),(9,9,2,1,'2025-08-31 09:21:41','2025-08-31 09:21:41',1),(10,10,2,1,'2025-08-31 09:22:18','2025-08-31 09:22:18',1),(11,11,2,1,'2025-08-31 09:23:03','2025-08-31 09:23:03',1),(12,12,2,1,'2025-08-31 09:23:18','2025-08-31 09:23:18',1),(13,13,2,1,'2025-08-31 09:23:39','2025-08-31 09:23:39',1),(14,14,2,1,'2025-08-31 09:23:55','2025-08-31 09:23:55',1),(15,15,2,1,'2025-08-31 09:24:16','2025-08-31 09:24:16',1),(16,16,2,1,'2025-08-31 09:24:59','2025-08-31 09:24:59',1),(17,17,3,1,'2025-08-31 09:25:32','2025-08-31 09:25:32',1),(18,18,3,1,'2025-08-31 09:25:47','2025-08-31 09:25:47',1),(19,19,3,1,'2025-08-31 09:26:22','2025-08-31 09:26:22',1),(20,20,3,1,'2025-08-31 09:26:37','2025-08-31 09:26:37',1),(21,21,3,1,'2025-08-31 09:26:54','2025-08-31 09:26:54',1),(22,22,3,1,'2025-08-31 09:27:55','2025-08-31 09:27:55',1),(23,23,3,1,'2025-08-31 09:28:13','2025-08-31 09:28:13',1),(24,24,3,1,'2025-08-31 09:29:07','2025-08-31 09:29:07',1),(25,25,3,1,'2025-08-31 09:29:47','2025-08-31 09:29:47',1),(26,26,3,1,'2025-08-31 09:30:41','2025-08-31 09:30:41',1);
/*!40000 ALTER TABLE `class_setup_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_setups`
--

DROP TABLE IF EXISTS `class_setups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_setups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_setups_session_id_foreign` (`session_id`),
  KEY `class_setups_classes_id_foreign` (`classes_id`),
  CONSTRAINT `class_setups_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_setups_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_setups`
--

LOCK TABLES `class_setups` WRITE;
/*!40000 ALTER TABLE `class_setups` DISABLE KEYS */;
INSERT INTO `class_setups` VALUES (1,1,1,1,'2025-08-31 09:18:53','2025-08-31 09:18:53',1),(2,1,2,1,'2025-08-31 09:19:13','2025-08-31 09:19:13',1),(3,1,3,1,'2025-08-31 09:19:50','2025-08-31 09:19:50',1),(4,1,4,1,'2025-08-31 09:20:01','2025-08-31 09:20:01',1),(5,1,5,1,'2025-08-31 09:20:23','2025-08-31 09:20:23',1),(6,1,6,1,'2025-08-31 09:20:45','2025-08-31 09:20:45',1),(7,1,7,1,'2025-08-31 09:21:03','2025-08-31 09:21:03',1),(8,1,8,1,'2025-08-31 09:21:25','2025-08-31 09:21:25',1),(9,1,9,1,'2025-08-31 09:21:41','2025-08-31 09:21:41',1),(10,1,10,1,'2025-08-31 09:22:18','2025-08-31 09:22:18',1),(11,1,11,1,'2025-08-31 09:23:03','2025-08-31 09:23:03',1),(12,1,12,1,'2025-08-31 09:23:18','2025-08-31 09:23:18',1),(13,1,13,1,'2025-08-31 09:23:39','2025-08-31 09:23:39',1),(14,1,14,1,'2025-08-31 09:23:55','2025-08-31 09:23:55',1),(15,1,15,1,'2025-08-31 09:24:16','2025-08-31 09:24:16',1),(16,1,16,1,'2025-08-31 09:24:59','2025-08-31 09:24:59',1),(17,1,17,1,'2025-08-31 09:25:32','2025-08-31 09:25:32',1),(18,1,18,1,'2025-08-31 09:25:47','2025-08-31 09:25:47',1),(19,1,19,1,'2025-08-31 09:26:22','2025-08-31 09:26:22',1),(20,1,20,1,'2025-08-31 09:26:36','2025-08-31 09:26:36',1),(21,1,21,1,'2025-08-31 09:26:54','2025-08-31 09:26:54',1),(22,1,22,1,'2025-08-31 09:27:55','2025-08-31 09:27:55',1),(23,1,23,1,'2025-08-31 09:28:13','2025-08-31 09:28:13',1),(24,1,24,1,'2025-08-31 09:29:07','2025-08-31 09:29:07',1),(25,1,25,1,'2025-08-31 09:29:47','2025-08-31 09:29:47',1),(26,1,26,1,'2025-08-31 09:30:41','2025-08-31 09:30:41',1);
/*!40000 ALTER TABLE `class_setups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_translates`
--

DROP TABLE IF EXISTS `class_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_translates_class_id_foreign` (`class_id`),
  CONSTRAINT `class_translates_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_translates`
--

LOCK TABLES `class_translates` WRITE;
/*!40000 ALTER TABLE `class_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_level` enum('kg','primary','secondary','high_school') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Explicitly assigned academic level for this class',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_classes_academic_level` (`academic_level`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'Form4A','secondary',1,'2025-08-31 09:02:23','2025-09-11 04:57:43',1),(2,'Form4B','secondary',1,'2025-08-31 09:02:42','2025-09-11 04:57:52',1),(3,'Form3A','secondary',1,'2025-08-31 09:02:53','2025-09-11 04:57:57',1),(4,'Form3B','secondary',1,'2025-08-31 09:03:23','2025-09-11 04:58:07',1),(5,'Form2A','secondary',1,'2025-08-31 09:03:32','2025-09-11 04:58:13',1),(6,'Form2B','secondary',1,'2025-08-31 09:03:41','2025-09-11 04:58:24',1),(7,'Form1A','secondary',1,'2025-08-31 09:03:53','2025-09-11 04:58:29',1),(8,'Form1B','secondary',1,'2025-08-31 09:04:01','2025-09-11 04:58:40',1),(9,'Grade8A','primary',1,'2025-08-31 09:04:17','2025-09-11 04:58:47',1),(10,'Grade8B','primary',1,'2025-08-31 09:04:26','2025-09-11 04:58:51',1),(11,'Grade7A','primary',1,'2025-08-31 09:05:26','2025-09-11 04:58:53',1),(12,'Grade7B','primary',1,'2025-08-31 09:05:36','2025-09-11 04:58:56',1),(13,'Grade6A','primary',1,'2025-08-31 09:05:46','2025-09-11 04:58:59',1),(14,'Grade6B','primary',1,'2025-08-31 09:05:55','2025-09-11 04:59:02',1),(15,'Grade5A','primary',1,'2025-08-31 09:09:29','2025-09-11 04:59:07',1),(16,'Grade5B','primary',1,'2025-08-31 09:10:40','2025-09-11 04:59:11',1),(17,'Grade4A','primary',1,'2025-08-31 09:10:57','2025-09-11 04:59:15',1),(18,'Grade4B','primary',1,'2025-08-31 09:11:23','2025-09-11 04:59:18',1),(19,'Grade3A','primary',1,'2025-08-31 09:11:49','2025-09-11 04:59:22',1),(20,'Grade3B','primary',1,'2025-08-31 09:12:07','2025-09-11 04:59:25',1),(21,'Grade2A','primary',1,'2025-08-31 09:12:31','2025-09-11 04:59:29',1),(22,'Grade2B','primary',1,'2025-08-31 09:13:03','2025-09-11 04:59:36',1),(23,'Grade1A','primary',1,'2025-08-31 09:13:17','2025-09-11 04:59:45',1),(24,'Grade1B','primary',1,'2025-08-31 09:13:28','2025-09-11 04:59:47',1),(25,'KG-1A','kg',1,'2025-08-31 09:14:07','2025-09-11 04:59:56',1),(26,'KG-1B','kg',1,'2025-08-31 09:15:29','2025-09-11 05:00:00',1);
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_info_translates`
--

DROP TABLE IF EXISTS `contact_info_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_info_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contact_info_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `contact_info_translates_contact_info_id_foreign` (`contact_info_id`),
  CONSTRAINT `contact_info_translates_contact_info_id_foreign` FOREIGN KEY (`contact_info_id`) REFERENCES `contact_infos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_info_translates`
--

LOCK TABLES `contact_info_translates` WRITE;
/*!40000 ALTER TABLE `contact_info_translates` DISABLE KEYS */;
INSERT INTO `contact_info_translates` VALUES (1,1,'en','Our School','222, Tower Building, Country Hall, California 777, United States','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Our School','222, Tower Building, Country Hall, California 777, United States','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Our School','222, Tower Building, Country Hall, California 777, United States','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,4,'en','Our School','222, Tower Building, Country Hall, California 777, United States','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,1,'bn','আমাদের পাঠশালা','222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,2,'bn','আমাদের পাঠশালা','222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,3,'bn','আমাদের পাঠশালা','222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,4,'bn','আমাদের পাঠশালা','222, টাওয়ার বিল্ডিং, কান্ট্রি হল, ক্যালিফোর্নিয়া 777, মার্কিন যুক্তরাষ্ট্র','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `contact_info_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_infos`
--

DROP TABLE IF EXISTS `contact_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_infos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `contact_infos_upload_id_foreign` (`upload_id`),
  CONSTRAINT `contact_infos_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_infos`
--

LOCK TABLES `contact_infos` WRITE;
/*!40000 ALTER TABLE `contact_infos` DISABLE KEYS */;
INSERT INTO `contact_infos` VALUES (1,56,'Our School','222, Tower Building, Country Hall, California 777, United States',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,57,'Our School','222, Tower Building, Country Hall, California 777, United States',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,58,'Our School','222, Tower Building, Country Hall, California 777, United States',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,59,'Our School','222, Tower Building, Country Hall, California 777, United States',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `contact_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `counter_translates`
--

DROP TABLE IF EXISTS `counter_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `counter_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `counter_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_count` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `counter_translates_counter_id_foreign` (`counter_id`),
  CONSTRAINT `counter_translates_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `counter_translates`
--

LOCK TABLES `counter_translates` WRITE;
/*!40000 ALTER TABLE `counter_translates` DISABLE KEYS */;
INSERT INTO `counter_translates` VALUES (1,1,'en','Curriculum','0','0','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Students','45','1','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Expert Teachers','90','2','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,4,'en','User','135','3','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,5,'en','Parents','180','4','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,1,'bn','পাঠ্যক্রম','০','০','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,2,'bn','ছাত্ররা','৪৫','১','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,3,'bn','বিশেষজ্ঞ শিক্ষক','৯০','২','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,4,'bn','ব্যবহারকারী','১৩৫','৩','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,5,'bn','পিতামাতা','১৮০','৪','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `counter_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `counters`
--

DROP TABLE IF EXISTS `counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `counters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_count` int DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `serial` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `counters_upload_id_foreign` (`upload_id`),
  CONSTRAINT `counters_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `counters`
--

LOCK TABLES `counters` WRITE;
/*!40000 ALTER TABLE `counters` DISABLE KEYS */;
INSERT INTO `counters` VALUES (1,'Curriculum',0,14,0,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Students',45,15,1,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Expert Teachers',90,16,2,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'User',135,17,3,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'Parents',180,18,4,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `decimal_digits` int DEFAULT '2',
  `decimal_point_separator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thousand_separator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `with_space` tinyint DEFAULT '0',
  `position` tinyint NOT NULL DEFAULT '1' COMMENT '0 => Suffix, 1 => Prefix',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'US Dollar','USD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(2,'Canadian Dollar','CAD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(3,'Euro','EUR','€',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(4,'UAE Dirham','AED','د.إ.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(5,'Afghan Afghani','AFN','؋',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(6,'Albanian Lek','ALL','Lek',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(7,'Armenian Dram','AMD','դր.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(8,'Argentine Peso','ARS','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(9,'Australian Dollar','AUD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(10,'Azerbaijani Manat','AZN','ман.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(11,'Bosnia-Herzegovina Convertible Mark','BAM','KM',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(12,'Bangladeshi Taka','BDT','৳',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(13,'Bulgarian Lev','BGN','лв.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(14,'Bahraini Dinar','BHD','د.ب.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(15,'Burundian Franc','BIF','FBu',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(16,'Brunei Dollar','BND','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(17,'Bolivian Boliviano','BOB','Bs',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(18,'Brazilian Real','BRL','R$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(19,'Botswanan Pula','BWP','P',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(20,'Belarusian Ruble','BYN','руб.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(21,'Belize Dollar','BZD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(22,'Congolese Franc','CDF','FrCD',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(23,'Swiss Franc','CHF','CHF',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(24,'Chilean Peso','CLP','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(25,'Chinese Yuan','CNY','CN¥',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(26,'Colombian Peso','COP','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(27,'Costa Rican Colón','CRC','₡',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(28,'Cape Verdean Escudo','CVE','CV$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(29,'Czech Republic Koruna','CZK','Kč',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(30,'Djiboutian Franc','DJF','Fdj',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(31,'Danish Krone','DKK','kr',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(32,'Dominican Peso','DOP','RD$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(33,'Algerian Dinar','DZD','د.ج.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(34,'Estonian Kroon','EEK','kr',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(35,'Egyptian Pound','EGP','ج.م.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(36,'Eritrean Nakfa','ERN','Nfk',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(37,'Ethiopian Birr','ETB','Br',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(38,'British Pound Sterling','GBP','£',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(39,'Georgian Lari','GEL','GEL',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(40,'Ghanaian Cedi','GHS','GH₵',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(41,'Guinean Franc','GNF','FG',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(42,'Guatemalan Quetzal','GTQ','Q',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(43,'Hong Kong Dollar','HKD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(44,'Honduran Lempira','HNL','L',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(45,'Croatian Kuna','HRK','kn',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(46,'Hungarian Forint','HUF','Ft',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(47,'Indonesian Rupiah','IDR','Rp',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(48,'Indian Rupee','INR','₹',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(49,'Iraqi Dinar','IQD','د.ع.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(50,'Iranian Rial','IRR','﷼',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(51,'Icelandic Króna','ISK','kr',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(52,'Jamaican Dollar','JMD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(53,'Jordanian Dinar','JOD','د.أ.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(54,'Japanese Yen','JPY','￥',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(55,'Kenyan Shilling','KES','Ksh',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(56,'Cambodian Riel','KHR','៛',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(57,'Comorian Franc','KMF','FC',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(58,'South Korean Won','KRW','₩',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(59,'Kuwaiti Dinar','KWD','د.ك.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(60,'Kazakhstani Tenge','KZT','тңг.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(61,'Lebanese Pound','LBP','ل.ل.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(62,'Sri Lankan Rupee','LKR','SL Re',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(63,'Lithuanian Litas','LTL','Lt',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(64,'Latvian Lats','LVL','Ls',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(65,'Libyan Dinar','LYD','د.ل.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(66,'Moroccan Dirham','MAD','د.م.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(67,'Moldovan Leu','MDL','MDL',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(68,'Malagasy Ariary','MGA','MGA',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(69,'Macedonian Denar','MKD','MKD',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(70,'Myanma Kyat','MMK','K',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(71,'Macanese Pataca','MOP','MOP$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(72,'Mauritian Rupee','MUR','MURs',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(73,'Mexican Peso','MXN','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(74,'Malaysian Ringgit','MYR','RM',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(75,'Mozambican Metical','MZN','MTn',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(76,'Namibian Dollar','NAD','N$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(77,'Nigerian Naira','NGN','₦',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(78,'Nicaraguan Córdoba','NIO','C$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(79,'Norwegian Krone','NOK','kr',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(80,'Nepalese Rupee','NPR','नेरू',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(81,'New Zealand Dollar','NZD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(82,'Omani Rial','OMR','ر.ع.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(83,'Panamanian Balboa','PAB','B/.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(84,'Peruvian Nuevo Sol','PEN','S/.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(85,'Philippine Peso','PHP','₱',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(86,'Pakistani Rupee','PKR','₨',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(87,'Polish Zloty','PLN','zł',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(88,'Paraguayan Guarani','PYG','₲',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(89,'Qatari Rial','QAR','ر.ق.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(90,'Romanian Leu','RON','RON',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(91,'Serbian Dinar','RSD','дин.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(92,'Russian Ruble','RUB','₽.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(93,'Rwandan Franc','RWF','FR',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(94,'Saudi Riyal','SAR','ر.س.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(95,'Sudanese Pound','SDG','SDG',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(96,'Swedish Krona','SEK','kr',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(97,'Singapore Dollar','SGD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(98,'Somali Shilling','SOS','Ssh',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(99,'Syrian Pound','SYP','ل.س.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(100,'Thai Baht','THB','฿',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(101,'Tunisian Dinar','TND','د.ت.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(102,'Tongan Pa\'anga','TOP','T$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(103,'Turkish Lira','TRY','TL',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(104,'Trinidad and Tobago Dollar','TTD','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(105,'New Taiwan Dollar','TWD','NT$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(106,'Tanzanian Shilling','TZS','TSh',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(107,'Ukrainian Hryvnia','UAH','₴',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(108,'Ugandan Shilling','UGX','USh',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(109,'Uruguayan Peso','UYU','$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(110,'Uzbekistan Som','UZS','UZS',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(111,'Venezuelan Bolívar','VEF','Bs.F.',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(112,'Vietnamese Dong','VND','₫',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(113,'CFA Franc BEAC','XAF','FCFA',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(114,'CFA Franc BCEAO','XOF','CFA',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(115,'Yemeni Rial','YER','ر.ي.‏',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(116,'South African Rand','ZAR','R',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(117,'Zambian Kwacha','ZMK','ZK',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1),(118,'Zimbabwean Dollar','ZWL','ZWL$',2,NULL,NULL,0,1,'2025-06-03 07:04:09','2025-06-03 07:04:09',1);
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_contact_translates`
--

DROP TABLE IF EXISTS `department_contact_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_contact_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_contact_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `department_contact_translates_department_contact_id_foreign` (`department_contact_id`),
  CONSTRAINT `department_contact_translates_department_contact_id_foreign` FOREIGN KEY (`department_contact_id`) REFERENCES `department_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_contact_translates`
--

LOCK TABLES `department_contact_translates` WRITE;
/*!40000 ALTER TABLE `department_contact_translates` DISABLE KEYS */;
INSERT INTO `department_contact_translates` VALUES (1,1,'en','Admission','+883459783849','admission@mail.Com','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Examination','+883459783849','examination@mail.Com','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Library','+883459783849','library@mail.Com','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,4,'en','media','+883459783849','media@mail.Com','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,1,'bn','ভর্তি','+৮৮৩৪৫৯৭৮৩৮৪৯','এডমিশন@মেইল.কম','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,2,'bn','পরীক্ষা','+৮৮৩৪৫৯৭৮৩৮৪৯','এক্সামিনেশন@মেইল.কম','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,3,'bn','লাইব্রেরি','+৮৮৩৪৫৯৭৮৩৮৪৯','লাইব্রেরি@মেইল.কম','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,4,'bn','মিডিয়া','+৮৮৩৪৫৯৭৮৩৮৪৯','মিডিয়া@মেইল.কম','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `department_contact_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_contacts`
--

DROP TABLE IF EXISTS `department_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `department_contacts_upload_id_foreign` (`upload_id`),
  CONSTRAINT `department_contacts_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_contacts`
--

LOCK TABLES `department_contacts` WRITE;
/*!40000 ALTER TABLE `department_contacts` DISABLE KEYS */;
INSERT INTO `department_contacts` VALUES (1,60,'Admission','+883459783849','admission@mail.Com',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,61,'Examination','+883459783849','examination@mail.Com',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,62,'Library','+883459783849','library@mail.Com',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,63,'media','+883459783849','media@mail.Com',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `department_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_user_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `departments_staff_user_id_foreign` (`staff_user_id`),
  CONSTRAINT `departments_staff_user_id_foreign` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Arts',NULL,1,'2025-06-03 07:04:08','2025-08-31 09:44:45',1),(2,'Science',NULL,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'Languages',NULL,1,'2025-09-01 03:13:15','2025-09-01 03:13:15',1),(5,'Religious Studies',NULL,1,'2025-09-01 03:13:16','2025-09-01 03:13:16',1),(6,'Sciences',NULL,1,'2025-09-01 03:13:16','2025-09-01 03:13:16',1),(7,'Social Sciences',NULL,1,'2025-09-01 03:13:16','2025-09-01 03:13:16',1),(8,'Mathematics',NULL,1,'2025-09-01 03:13:16','2025-09-01 03:13:16',1),(9,'Administration',NULL,1,'2025-09-01 03:13:17','2025-09-01 03:13:17',1);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designations`
--

LOCK TABLES `designations` WRITE;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` VALUES (1,'HRM',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Admin',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Accounts',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'Development',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'Software',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'Teacher',1,'2025-09-01 03:13:15','2025-09-01 03:13:15',1),(7,'Head Teacher',1,'2025-09-01 03:13:17','2025-09-01 03:13:17',1),(8,'Deputy Head Teacher',1,'2025-09-01 03:13:17','2025-09-01 03:13:17',1),(9,'Academic Coordinator',1,'2025-09-01 03:13:17','2025-09-01 03:13:17',1);
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `early_payment_discounts`
--

DROP TABLE IF EXISTS `early_payment_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `early_payment_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `discount_percentage` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `early_payment_discounts`
--

LOCK TABLES `early_payment_discounts` WRITE;
/*!40000 ALTER TABLE `early_payment_discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `early_payment_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_translates`
--

DROP TABLE IF EXISTS `event_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `event_translates_event_id_foreign` (`event_id`),
  CONSTRAINT `event_translates_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_translates`
--

LOCK TABLES `event_translates` WRITE;
/*!40000 ALTER TABLE `event_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `events_session_id_foreign` (`session_id`),
  KEY `events_upload_id_foreign` (`upload_id`),
  CONSTRAINT `events_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_assign_childrens`
--

DROP TABLE IF EXISTS `exam_assign_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_assign_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_assign_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mark` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `exam_assign_childrens_exam_assign_id_foreign` (`exam_assign_id`),
  CONSTRAINT `exam_assign_childrens_exam_assign_id_foreign` FOREIGN KEY (`exam_assign_id`) REFERENCES `exam_assigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_assign_childrens`
--

LOCK TABLES `exam_assign_childrens` WRITE;
/*!40000 ALTER TABLE `exam_assign_childrens` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_assign_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_assigns`
--

DROP TABLE IF EXISTS `exam_assigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_assigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `exam_type_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `total_mark` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `exam_assigns_session_id_foreign` (`session_id`),
  KEY `exam_assigns_classes_id_foreign` (`classes_id`),
  KEY `exam_assigns_section_id_foreign` (`section_id`),
  KEY `exam_assigns_exam_type_id_foreign` (`exam_type_id`),
  KEY `exam_assigns_subject_id_foreign` (`subject_id`),
  CONSTRAINT `exam_assigns_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_assigns_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_assigns_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_assigns`
--

LOCK TABLES `exam_assigns` WRITE;
/*!40000 ALTER TABLE `exam_assigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_assigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_routine_childrens`
--

DROP TABLE IF EXISTS `exam_routine_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_routine_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_routine_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `time_schedule_id` bigint unsigned NOT NULL,
  `class_room_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `exam_routine_childrens_exam_routine_id_foreign` (`exam_routine_id`),
  KEY `exam_routine_childrens_subject_id_foreign` (`subject_id`),
  KEY `exam_routine_childrens_time_schedule_id_foreign` (`time_schedule_id`),
  KEY `exam_routine_childrens_class_room_id_foreign` (`class_room_id`),
  CONSTRAINT `exam_routine_childrens_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_routine_childrens_exam_routine_id_foreign` FOREIGN KEY (`exam_routine_id`) REFERENCES `exam_routines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_routine_childrens_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_routine_childrens_time_schedule_id_foreign` FOREIGN KEY (`time_schedule_id`) REFERENCES `time_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_routine_childrens`
--

LOCK TABLES `exam_routine_childrens` WRITE;
/*!40000 ALTER TABLE `exam_routine_childrens` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_routine_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_routines`
--

DROP TABLE IF EXISTS `exam_routines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_routines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `type_id` bigint unsigned NOT NULL,
  `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `exam_routines_session_id_foreign` (`session_id`),
  KEY `exam_routines_classes_id_foreign` (`classes_id`),
  KEY `exam_routines_section_id_foreign` (`section_id`),
  KEY `exam_routines_type_id_foreign` (`type_id`),
  CONSTRAINT `exam_routines_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_routines_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_routines_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_routines_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_routines`
--

LOCK TABLES `exam_routines` WRITE;
/*!40000 ALTER TABLE `exam_routines` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_routines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_types`
--

DROP TABLE IF EXISTS `exam_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_types`
--

LOCK TABLES `exam_types` WRITE;
/*!40000 ALTER TABLE `exam_types` DISABLE KEYS */;
INSERT INTO `exam_types` VALUES (4,'First Term',1,'2025-09-07 00:34:35','2025-09-07 00:34:35',1),(5,'Mid Term',1,'2025-09-07 00:34:46','2025-09-07 00:34:46',1),(6,'Final Exam',1,'2025-09-07 00:34:58','2025-09-07 00:34:58',1);
/*!40000 ALTER TABLE `exam_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examination_results`
--

DROP TABLE IF EXISTS `examination_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `examination_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `exam_type_id` bigint unsigned DEFAULT NULL,
  `classes_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `result` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Failed',
  `grade_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int DEFAULT NULL,
  `total_marks` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `examination_results_session_id_foreign` (`session_id`),
  KEY `examination_results_exam_type_id_foreign` (`exam_type_id`),
  KEY `examination_results_classes_id_foreign` (`classes_id`),
  KEY `examination_results_section_id_foreign` (`section_id`),
  KEY `examination_results_student_id_foreign` (`student_id`),
  CONSTRAINT `examination_results_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `examination_results_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `examination_results_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `examination_results_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `examination_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examination_results`
--

LOCK TABLES `examination_results` WRITE;
/*!40000 ALTER TABLE `examination_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `examination_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examination_settings`
--

DROP TABLE IF EXISTS `examination_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `examination_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `examination_settings_session_id_foreign` (`session_id`),
  CONSTRAINT `examination_settings_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examination_settings`
--

LOCK TABLES `examination_settings` WRITE;
/*!40000 ALTER TABLE `examination_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `examination_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_head` bigint unsigned NOT NULL,
  `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `expenses_session_id_foreign` (`session_id`),
  KEY `expenses_expense_head_foreign` (`expense_head`),
  KEY `expenses_upload_id_foreign` (`upload_id`),
  CONSTRAINT `expenses_expense_head_foreign` FOREIGN KEY (`expense_head`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `features`
--

DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `position` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `features_upload_id_foreign` (`upload_id`),
  CONSTRAINT `features_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `features`
--

LOCK TABLES `features` WRITE;
/*!40000 ALTER TABLE `features` DISABLE KEYS */;
/*!40000 ALTER TABLE `features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_system_migration_logs`
--

DROP TABLE IF EXISTS `fee_system_migration_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_system_migration_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the migration executed',
  `status` enum('pending','running','completed','failed','rollback') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `migration_details` json DEFAULT NULL COMMENT 'Detailed information about the migration',
  `fee_types_migrated` int NOT NULL DEFAULT '0' COMMENT 'Number of fee types processed',
  `student_services_created` int NOT NULL DEFAULT '0' COMMENT 'Number of student services created',
  `fees_collects_updated` int NOT NULL DEFAULT '0' COMMENT 'Number of fee collect records updated',
  `discounts_migrated` int NOT NULL DEFAULT '0' COMMENT 'Number of discount records migrated',
  `errors` text COLLATE utf8mb4_unicode_ci COMMENT 'Any errors encountered during migration',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional migration notes',
  `migration_date` timestamp NULL DEFAULT NULL COMMENT 'When the migration was executed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_system_migration_logs_migration_name_index` (`migration_name`),
  KEY `fee_system_migration_logs_status_migration_date_index` (`status`,`migration_date`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_system_migration_logs`
--

LOCK TABLES `fee_system_migration_logs` WRITE;
/*!40000 ALTER TABLE `fee_system_migration_logs` DISABLE KEYS */;
INSERT INTO `fee_system_migration_logs` VALUES (1,'pre_migration_analysis','completed','{\"total_fee_assignments\": 69, \"existing_student_services\": 0, \"duplicate_assignments_found\": 11, \"most_duplicated_combinations\": [{\"session_id\": 1, \"student_id\": 31, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 34, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 35, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 35, \"fees_type_id\": 2, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 37, \"fees_type_id\": 1, \"duplicate_count\": 2}], \"consolidation_rate_percentage\": 15.94, \"unique_combinations_to_create\": 58}',0,0,0,0,NULL,'No data integrity issues found','2025-09-09 09:03:27','2025-09-09 09:03:27','2025-09-09 09:03:27'),(2,'pre_migration_analysis','completed','{\"total_fee_assignments\": 69, \"existing_student_services\": 58, \"duplicate_assignments_found\": 11, \"most_duplicated_combinations\": [{\"session_id\": 1, \"student_id\": 31, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 34, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 35, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 35, \"fees_type_id\": 2, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 37, \"fees_type_id\": 1, \"duplicate_count\": 2}], \"consolidation_rate_percentage\": 15.94, \"unique_combinations_to_create\": 58}',0,0,0,0,NULL,'No data integrity issues found','2025-09-09 09:10:09','2025-09-09 09:10:09','2025-09-09 09:10:09'),(3,'pre_migration_analysis','completed','{\"total_fee_assignments\": 69, \"existing_student_services\": 58, \"duplicate_assignments_found\": 11, \"most_duplicated_combinations\": [{\"session_id\": 1, \"student_id\": 31, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 34, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 35, \"fees_type_id\": 1, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 35, \"fees_type_id\": 2, \"duplicate_count\": 2}, {\"session_id\": 1, \"student_id\": 37, \"fees_type_id\": 1, \"duplicate_count\": 2}], \"consolidation_rate_percentage\": 15.94, \"unique_combinations_to_create\": 58}',0,0,0,0,NULL,'No data integrity issues found','2025-09-09 09:16:26','2025-09-09 09:16:26','2025-09-09 09:16:26'),(4,'migrate_existing_fee_data_to_service_structure','completed','{\"migration_method\": \"automated_with_consolidation\", \"discounts_migrated\": 0, \"enhanced_fees_types\": 2, \"data_safety_measures\": {\"transaction_wrapped\": true, \"batch_processing_used\": true, \"duplicate_consolidation_enabled\": true, \"pre_migration_validation_performed\": true}, \"fees_collects_enhanced\": 63, \"migration_completed_at\": \"2025-09-09T12:16:26.652546Z\", \"student_services_created\": 58}',2,58,63,0,NULL,'Migration completed successfully with duplicate consolidation and data validation','2025-09-09 09:16:26','2025-09-09 09:16:26','2025-09-09 09:16:26');
/*!40000 ALTER TABLE `fee_system_migration_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_assign_childrens`
--

DROP TABLE IF EXISTS `fees_assign_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_assign_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fees_assign_id` bigint unsigned NOT NULL,
  `fees_master_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fees_assign_childrens_fees_assign_id_foreign` (`fees_assign_id`),
  KEY `fees_assign_childrens_fees_master_id_foreign` (`fees_master_id`),
  KEY `fees_assign_childrens_student_id_foreign` (`student_id`),
  CONSTRAINT `fees_assign_childrens_fees_assign_id_foreign` FOREIGN KEY (`fees_assign_id`) REFERENCES `fees_assigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assign_childrens_fees_master_id_foreign` FOREIGN KEY (`fees_master_id`) REFERENCES `fees_masters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assign_childrens_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_assign_childrens`
--

LOCK TABLES `fees_assign_childrens` WRITE;
/*!40000 ALTER TABLE `fees_assign_childrens` DISABLE KEYS */;
INSERT INTO `fees_assign_childrens` VALUES (1,1,1,31,'2025-09-01 05:05:37','2025-09-01 05:05:37',1),(2,1,1,57,'2025-09-01 05:05:37','2025-09-01 05:05:37',1),(3,2,1,33,'2025-09-02 01:21:48','2025-09-02 01:21:48',1),(4,2,1,59,'2025-09-02 01:21:48','2025-09-02 01:21:48',1),(5,2,2,33,'2025-09-02 01:21:48','2025-09-02 01:21:48',1),(6,2,2,59,'2025-09-02 01:21:48','2025-09-02 01:21:48',1),(7,3,1,34,'2025-09-02 01:48:05','2025-09-02 01:48:05',1),(8,4,1,35,'2025-09-05 21:44:06','2025-09-05 21:44:06',1),(9,4,2,35,'2025-09-05 21:44:06','2025-09-05 21:44:06',1),(10,5,1,37,'2025-09-05 22:14:35','2025-09-05 22:14:35',1),(11,5,2,37,'2025-09-05 22:14:35','2025-09-05 22:14:35',1),(12,6,3,40,'2025-09-05 22:57:14','2025-09-05 22:57:14',1),(13,6,4,40,'2025-09-05 22:57:14','2025-09-05 22:57:14',1),(14,7,3,42,'2025-09-05 23:12:44','2025-09-05 23:12:44',1),(15,7,4,42,'2025-09-05 23:12:44','2025-09-05 23:12:44',1),(16,8,3,44,'2025-09-05 23:54:58','2025-09-05 23:54:58',1),(17,8,4,44,'2025-09-05 23:54:58','2025-09-05 23:54:58',1),(18,1,3,31,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(19,1,4,31,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(20,3,3,34,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(21,3,4,34,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(22,4,3,35,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(23,4,4,35,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(24,9,3,36,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(25,9,4,36,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(26,5,3,37,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(27,5,4,37,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(28,10,3,38,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(29,10,4,38,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(30,11,3,39,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(31,11,4,39,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(32,12,3,41,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(33,12,4,41,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(34,13,3,43,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(35,13,4,43,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(36,14,3,45,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(37,14,4,45,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(38,15,3,46,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(39,15,4,46,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(40,16,3,47,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(41,16,4,47,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(42,17,3,48,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(43,17,4,48,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(44,18,3,49,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(45,18,4,49,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(46,19,3,50,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(47,19,4,50,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(48,20,3,51,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(49,20,4,51,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(50,21,3,52,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(51,21,4,52,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(52,22,3,53,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(53,22,4,53,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(54,23,3,54,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(55,23,4,54,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(56,24,3,55,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(57,24,4,55,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(58,25,3,56,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(59,25,4,56,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(60,1,3,57,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(61,1,4,57,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(62,26,3,58,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(63,26,4,58,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(64,2,3,59,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(65,2,4,59,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(66,3,3,60,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(67,3,4,60,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(68,1,3,61,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(69,1,4,61,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(70,26,1,58,'2025-09-09 04:45:32','2025-09-09 04:45:32',1),(71,26,2,58,'2025-09-09 04:45:32','2025-09-09 04:45:32',1);
/*!40000 ALTER TABLE `fees_assign_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_assigns`
--

DROP TABLE IF EXISTS `fees_assigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_assigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `gender_id` bigint unsigned DEFAULT NULL,
  `fees_group_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fees_assigns_session_id_foreign` (`session_id`),
  KEY `fees_assigns_classes_id_foreign` (`classes_id`),
  KEY `fees_assigns_section_id_foreign` (`section_id`),
  KEY `fees_assigns_category_id_foreign` (`category_id`),
  KEY `fees_assigns_gender_id_foreign` (`gender_id`),
  KEY `fees_assigns_fees_group_id_foreign` (`fees_group_id`),
  CONSTRAINT `fees_assigns_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assigns_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assigns_fees_group_id_foreign` FOREIGN KEY (`fees_group_id`) REFERENCES `fees_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assigns_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_assigns`
--

LOCK TABLES `fees_assigns` WRITE;
/*!40000 ALTER TABLE `fees_assigns` DISABLE KEYS */;
INSERT INTO `fees_assigns` VALUES (1,1,1,1,1,NULL,1,'2025-09-01 05:05:37','2025-09-01 05:05:37',1),(2,1,3,1,1,NULL,1,'2025-09-02 01:21:48','2025-09-02 01:21:48',1),(3,1,4,1,1,NULL,1,'2025-09-02 01:48:05','2025-09-02 01:48:05',1),(4,1,5,1,1,2,1,'2025-09-05 21:44:06','2025-09-05 21:44:06',1),(5,1,7,1,1,1,1,'2025-09-05 22:14:35','2025-09-05 22:14:35',1),(6,1,10,2,1,1,2,'2025-09-05 22:57:14','2025-09-05 22:57:14',1),(7,1,12,2,1,2,2,'2025-09-05 23:12:44','2025-09-05 23:12:44',1),(8,1,14,2,1,1,2,'2025-09-05 23:54:58','2025-09-05 23:54:58',1),(9,1,6,2,1,1,2,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(10,1,8,2,2,2,2,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(11,1,9,1,1,1,2,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(12,1,11,1,1,1,2,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(13,1,13,1,1,1,2,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(14,1,15,1,1,2,2,'2025-09-06 05:18:19','2025-09-06 05:18:19',1),(15,1,16,2,2,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(16,1,17,1,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(17,1,18,2,2,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(18,1,19,1,1,2,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(19,1,20,3,2,2,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(20,1,21,3,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(21,1,22,2,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(22,1,23,1,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(23,1,24,2,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(24,1,25,1,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(25,1,26,3,2,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1),(26,1,2,1,1,1,2,'2025-09-06 05:18:20','2025-09-06 05:18:20',1);
/*!40000 ALTER TABLE `fees_assigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_collects`
--

DROP TABLE IF EXISTS `fees_collects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_collects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `generation_batch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generation_method` enum('manual','bulk','automated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `due_date` date DEFAULT NULL,
  `late_fee_applied` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discount_applied` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discount_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes about discount applied',
  `date` date DEFAULT NULL,
  `payment_method` tinyint DEFAULT NULL,
  `payment_gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fees_assign_children_id` bigint unsigned NOT NULL,
  `fee_type_id` bigint unsigned DEFAULT NULL,
  `fees_collect_by` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL COMMENT 'total amount + fine',
  `fine_amount` decimal(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fees_collects_fees_assign_children_id_foreign` (`fees_assign_children_id`),
  KEY `fees_collects_fees_collect_by_foreign` (`fees_collect_by`),
  KEY `fees_collects_student_id_foreign` (`student_id`),
  KEY `fees_collects_session_id_foreign` (`session_id`),
  KEY `fees_collects_generation_batch_id_index` (`generation_batch_id`),
  KEY `fees_collects_due_date_generation_method_index` (`due_date`,`generation_method`),
  KEY `fees_collects_fee_type_id_foreign` (`fee_type_id`),
  KEY `fees_collects_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `fees_collects_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fees_collects_fee_type_id_foreign` FOREIGN KEY (`fee_type_id`) REFERENCES `fees_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fees_collects_fees_assign_children_id_foreign` FOREIGN KEY (`fees_assign_children_id`) REFERENCES `fees_assign_childrens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_collects_fees_collect_by_foreign` FOREIGN KEY (`fees_collect_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_collects_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_collects_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_collects`
--

LOCK TABLES `fees_collects` WRITE;
/*!40000 ALTER TABLE `fees_collects` DISABLE KEYS */;
INSERT INTO `fees_collects` VALUES (1,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-03',1,NULL,NULL,1,1,1,31,1,1,30.00,0.00,'2025-09-02 22:05:16','2025-09-09 12:16:26',1),(3,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-03',1,NULL,NULL,3,1,1,33,1,1,30.00,0.00,'2025-09-03 00:18:45','2025-09-09 12:16:26',1),(4,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-03',1,NULL,NULL,5,2,1,33,1,1,15.00,0.00,'2025-09-03 00:18:45','2025-09-09 12:16:26',1),(13,'FG_20250906021251_MFAegt','bulk','2025-09-10',0.00,0.00,NULL,'2025-09-06',1,NULL,NULL,14,1,1,42,1,1,15.00,0.00,'2025-09-05 23:12:51','2025-09-09 12:16:26',1),(14,'FG_20250906021251_MFAegt','bulk','2025-09-10',0.00,0.00,NULL,'2025-09-10',1,NULL,NULL,15,2,1,42,1,1,15.00,0.00,'2025-09-05 23:12:51','2025-09-09 12:16:26',1),(15,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-10',1,NULL,NULL,14,1,1,42,1,1,15.00,0.00,'2025-09-05 23:19:34','2025-09-09 12:16:26',1),(16,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-10',1,NULL,NULL,15,2,1,42,1,1,15.00,0.00,'2025-09-05 23:19:34','2025-09-09 12:16:26',1),(39,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-10',1,NULL,NULL,16,1,1,44,1,1,15.00,0.00,'2025-09-06 04:35:54','2025-09-09 12:16:26',1),(40,NULL,'manual',NULL,0.00,0.00,NULL,'2025-09-10',1,NULL,NULL,17,2,1,44,1,1,15.00,0.00,'2025-09-06 04:35:54','2025-09-09 12:16:26',1),(41,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-07',1,NULL,NULL,18,1,1,31,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(42,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,19,2,1,31,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(43,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,20,1,1,34,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(44,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,21,2,1,34,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(45,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,22,1,1,35,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(46,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,23,2,1,35,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(47,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,24,1,1,36,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(48,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,25,2,1,36,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(49,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,26,1,1,37,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(50,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,27,2,1,37,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(51,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,28,1,1,38,1,1,15.00,0.00,'2025-09-06 05:18:48','2025-09-09 12:16:26',1),(52,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,29,2,1,38,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(53,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,30,1,1,39,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(54,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,31,2,1,39,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(55,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,12,1,1,40,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(56,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,13,2,1,40,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(57,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,32,1,1,41,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(58,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,33,2,1,41,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(59,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,34,1,1,43,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(60,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,35,2,1,43,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(61,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,36,1,1,45,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(62,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,37,2,1,45,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(63,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,38,1,1,46,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(64,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,39,2,1,46,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(65,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,40,1,1,47,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(66,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,41,2,1,47,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(67,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,42,1,1,48,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(68,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,43,2,1,48,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(69,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,44,1,1,49,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(70,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,45,2,1,49,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(71,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,46,1,1,50,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(72,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,47,2,1,50,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(73,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,48,1,1,51,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(74,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,49,2,1,51,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(75,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,50,1,1,52,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(76,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,51,2,1,52,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(77,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,52,1,1,53,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(78,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,53,2,1,53,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(79,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,54,1,1,54,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(80,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,55,2,1,54,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(81,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,56,1,1,55,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(82,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,57,2,1,55,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(83,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,58,1,1,56,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(84,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,59,2,1,56,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(85,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,60,1,1,57,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(86,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,61,2,1,57,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(87,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,62,1,1,58,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(88,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,63,2,1,58,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(89,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,64,1,1,59,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(90,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,65,2,1,59,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(91,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,66,1,1,60,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(92,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,67,2,1,60,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(93,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,68,1,1,61,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1),(94,'FG_20250906081848_TwnWKP','bulk','2025-09-30',0.00,0.00,NULL,'2025-09-06',NULL,NULL,NULL,69,2,1,61,1,1,15.00,0.00,'2025-09-06 05:18:49','2025-09-09 12:16:26',1);
/*!40000 ALTER TABLE `fees_collects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_generation_logs`
--

DROP TABLE IF EXISTS `fees_generation_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_generation_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fees_generation_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `fees_collect_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','success','failed','skipped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `fee_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fees_generation_logs_fees_generation_id_student_id_unique` (`fees_generation_id`,`student_id`),
  KEY `fees_generation_logs_fees_collect_id_foreign` (`fees_collect_id`),
  KEY `fees_generation_logs_fees_generation_id_status_index` (`fees_generation_id`,`status`),
  KEY `fees_generation_logs_student_id_status_index` (`student_id`,`status`),
  CONSTRAINT `fees_generation_logs_fees_collect_id_foreign` FOREIGN KEY (`fees_collect_id`) REFERENCES `fees_collects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fees_generation_logs_fees_generation_id_foreign` FOREIGN KEY (`fees_generation_id`) REFERENCES `fees_generations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_generation_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_generation_logs`
--

LOCK TABLES `fees_generation_logs` WRITE;
/*!40000 ALTER TABLE `fees_generation_logs` DISABLE KEYS */;
INSERT INTO `fees_generation_logs` VALUES (1,1,35,NULL,'failed',0.00,'No fee assignments found for student ',NULL,'2025-09-04 04:54:17','2025-09-04 04:54:17'),(2,2,35,NULL,'failed',0.00,'No fee assignments found for student ',NULL,'2025-09-05 05:01:24','2025-09-05 05:01:24'),(3,3,35,NULL,'failed',0.00,'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'fees_master_id\' in \'where clause\' (Connection: mysql, SQL: select * from `fees_collects` where `student_id` = 35 and `fees_master_id` = 1 and month(`created_at`) = 09 and year(`created_at`) = 2025 and `fees_collects`.`branch_id` = 1 limit 1)',NULL,'2025-09-05 05:24:23','2025-09-05 05:24:23'),(4,4,35,NULL,'failed',0.00,'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'fees_master_id\' in \'where clause\' (Connection: mysql, SQL: select * from `fees_collects` where `student_id` = 35 and `fees_master_id` = 1 and month(`created_at`) = 09 and year(`created_at`) = 2025 and `fees_collects`.`branch_id` = 1 limit 1)',NULL,'2025-09-05 05:32:13','2025-09-05 05:32:13'),(5,5,35,NULL,'failed',0.00,'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'fees_master_id\' in \'where clause\' (Connection: mysql, SQL: select * from `fees_collects` where `student_id` = 35 and `fees_master_id` = 1 and month(`created_at`) = 09 and year(`created_at`) = 2025 and `fees_collects`.`branch_id` = 1 limit 1)',NULL,'2025-09-05 21:16:00','2025-09-05 21:16:00'),(6,6,35,NULL,'failed',0.00,'Add [session_id] to fillable property to allow mass assignment on [App\\Models\\Fees\\FeesAssign].',NULL,'2025-09-05 21:32:32','2025-09-05 21:32:32'),(7,7,35,NULL,'success',45.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 30, \"fees_master_id\": 1, \"original_amount\": \"30.00\", \"fees_assign_children_id\": 8}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 2, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 9}]','2025-09-05 21:44:13','2025-09-05 21:44:13'),(8,8,37,NULL,'success',45.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 30, \"fees_master_id\": 1, \"original_amount\": \"30.00\", \"fees_assign_children_id\": 10}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 2, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 11}]','2025-09-05 22:14:41','2025-09-05 22:14:41'),(9,9,40,NULL,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 12}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 13}]','2025-09-05 22:57:25','2025-09-05 22:57:25'),(10,10,42,13,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 14}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 15}]','2025-09-05 23:12:51','2025-09-05 23:12:51'),(11,11,44,NULL,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 16}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 17}]','2025-09-05 23:55:05','2025-09-05 23:55:05'),(12,12,42,NULL,'failed',0.00,'All fees for this month already exist for student Saynab Hussein',NULL,'2025-09-06 00:20:12','2025-09-06 00:20:12'),(13,13,31,41,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 18}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 19}]','2025-09-06 05:18:48','2025-09-06 05:18:48'),(14,13,34,43,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 20}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 21}]','2025-09-06 05:18:48','2025-09-06 05:18:48'),(15,13,35,45,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 22}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 23}]','2025-09-06 05:18:48','2025-09-06 05:18:48'),(16,13,36,47,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 24}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 25}]','2025-09-06 05:18:48','2025-09-06 05:18:48'),(17,13,37,49,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 26}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 27}]','2025-09-06 05:18:48','2025-09-06 05:18:48'),(18,13,38,51,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 28}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 29}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(19,13,39,53,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 30}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 31}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(20,13,40,55,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 12}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 13}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(21,13,41,57,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 32}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 33}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(22,13,42,NULL,'failed',0.00,'All fees for this month already exist for student Saynab Hussein',NULL,'2025-09-06 05:18:48','2025-09-06 05:18:49'),(23,13,43,59,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 34}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 35}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(24,13,44,NULL,'failed',0.00,'All fees for this month already exist for student Maxamed Ismail',NULL,'2025-09-06 05:18:48','2025-09-06 05:18:49'),(25,13,45,61,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 36}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 37}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(26,13,46,63,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 38}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 39}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(27,13,47,65,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 40}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 41}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(28,13,48,67,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 42}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 43}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(29,13,49,69,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 44}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 45}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(30,13,50,71,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 46}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 47}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(31,13,51,73,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 48}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 49}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(32,13,52,75,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 50}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 51}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(33,13,53,77,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 52}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 53}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(34,13,54,79,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 54}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 55}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(35,13,55,81,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 56}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 57}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(36,13,56,83,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 58}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 59}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(37,13,57,85,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 60}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 61}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(38,13,58,87,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 62}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 63}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(39,13,59,89,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 64}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 65}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(40,13,60,91,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 66}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 67}]','2025-09-06 05:18:48','2025-09-06 05:18:49'),(41,13,61,93,'success',30.00,NULL,'[{\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 3, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 68}, {\"discount\": 0, \"fees_name\": \"Fee\", \"net_amount\": 15, \"fees_master_id\": 4, \"original_amount\": \"15.00\", \"fees_assign_children_id\": 69}]','2025-09-06 05:18:48','2025-09-06 05:18:49');
/*!40000 ALTER TABLE `fees_generation_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_generations`
--

DROP TABLE IF EXISTS `fees_generations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_generations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_students` int NOT NULL DEFAULT '0',
  `processed_students` int NOT NULL DEFAULT '0',
  `successful_students` int NOT NULL DEFAULT '0',
  `failed_students` int NOT NULL DEFAULT '0',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `filters` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fees_generations_batch_id_unique` (`batch_id`),
  KEY `fees_generations_created_by_foreign` (`created_by`),
  KEY `fees_generations_status_created_at_index` (`status`,`created_at`),
  KEY `fees_generations_school_id_status_index` (`school_id`,`status`),
  KEY `fees_generations_batch_id_index` (`batch_id`),
  CONSTRAINT `fees_generations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_generations_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_generations`
--

LOCK TABLES `fees_generations` WRITE;
/*!40000 ALTER TABLE `fees_generations` DISABLE KEYS */;
INSERT INTO `fees_generations` VALUES (1,'FG_20250904075417_LTLhFZ','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-04 04:54:17','2025-09-04 04:54:17',1,NULL,'2025-09-04 04:54:17','2025-09-04 04:54:17'),(2,'FG_20250905080124_rJQSj2','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 05:01:24','2025-09-05 05:01:24',1,NULL,'2025-09-05 05:01:24','2025-09-05 05:01:24'),(3,'FG_20250905082423_g15Bjb','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 05:24:23','2025-09-05 05:24:23',1,NULL,'2025-09-05 05:24:23','2025-09-05 05:24:23'),(4,'FG_20250905083213_2X3GKB','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 05:32:13','2025-09-05 05:32:13',1,NULL,'2025-09-05 05:32:13','2025-09-05 05:32:13'),(5,'FG_20250906001600_5Yreli','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 21:16:00','2025-09-05 21:16:00',1,NULL,'2025-09-05 21:16:00','2025-09-05 21:16:00'),(6,'FG_20250906003232_4N8cep','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 21:32:32','2025-09-05 21:32:32',1,NULL,'2025-09-05 21:32:32','2025-09-05 21:32:32'),(7,'FG_20250906004413_dg7CyB','completed',1,1,1,0,45.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"5\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 21:44:13','2025-09-05 21:44:13',1,NULL,'2025-09-05 21:44:13','2025-09-05 21:44:13'),(8,'FG_20250906011441_38dbXx','completed',1,1,1,0,45.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"7\"], \"sections\": [\"1\"], \"fees_groups\": [\"1\"], \"selected_students\": \"all\"}','','2025-09-05 22:14:41','2025-09-05 22:14:41',1,NULL,'2025-09-05 22:14:41','2025-09-05 22:14:41'),(9,'FG_20250906015725_g65ZFc','completed',1,1,1,0,30.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"10\"], \"sections\": [\"2\"], \"fees_groups\": [\"2\"], \"selected_students\": \"all\"}','','2025-09-05 22:57:25','2025-09-05 22:57:25',1,NULL,'2025-09-05 22:57:25','2025-09-05 22:57:25'),(10,'FG_20250906021251_MFAegt','completed',1,1,1,0,30.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"12\"], \"sections\": [\"2\"], \"fees_groups\": [\"2\"], \"selected_students\": \"all\"}','','2025-09-05 23:12:51','2025-09-05 23:12:51',1,NULL,'2025-09-05 23:12:51','2025-09-05 23:12:51'),(11,'FG_20250906025505_0dGIx5','completed',1,1,1,0,30.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"14\"], \"sections\": [\"2\"], \"fees_groups\": [\"2\"], \"selected_students\": \"all\"}','','2025-09-05 23:55:05','2025-09-05 23:55:05',1,NULL,'2025-09-05 23:55:05','2025-09-05 23:55:05'),(12,'FG_20250906032012_WC01cq','failed',1,1,0,1,0.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"12\"], \"sections\": [\"2\"], \"fees_groups\": [\"2\"], \"selected_students\": \"all\"}','','2025-09-06 00:20:12','2025-09-06 00:20:12',1,NULL,'2025-09-06 00:20:12','2025-09-06 00:20:12'),(13,'FG_20250906081848_TwnWKP','completed',29,29,27,2,810.00,'{\"year\": \"2025\", \"month\": \"9\", \"classes\": [\"1\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"9\", \"10\", \"11\", \"12\", \"13\", \"14\", \"15\", \"16\", \"17\", \"18\", \"19\", \"20\", \"21\", \"22\", \"23\", \"24\", \"25\", \"26\"], \"sections\": [], \"fees_groups\": [\"2\"], \"selected_students\": \"all\"}','','2025-09-06 05:18:48','2025-09-06 05:18:49',1,NULL,'2025-09-06 05:18:48','2025-09-06 05:18:49');
/*!40000 ALTER TABLE `fees_generations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_groups`
--

DROP TABLE IF EXISTS `fees_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `online_admission_fees` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_groups`
--

LOCK TABLES `fees_groups` WRITE;
/*!40000 ALTER TABLE `fees_groups` DISABLE KEYS */;
INSERT INTO `fees_groups` VALUES (1,'Secondary Fees',NULL,1,0,'2025-09-01 05:00:57','2025-09-02 21:19:29',1),(2,'Primary Fees',NULL,1,0,'2025-09-02 21:20:22','2025-09-02 21:20:22',1),(3,'KG fees',NULL,1,0,'2025-09-02 21:20:36','2025-09-02 21:20:36',1);
/*!40000 ALTER TABLE `fees_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_master_childrens`
--

DROP TABLE IF EXISTS `fees_master_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_master_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fees_master_id` bigint unsigned NOT NULL,
  `fees_type_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fees_master_childrens_fees_master_id_foreign` (`fees_master_id`),
  KEY `fees_master_childrens_fees_type_id_foreign` (`fees_type_id`),
  CONSTRAINT `fees_master_childrens_fees_master_id_foreign` FOREIGN KEY (`fees_master_id`) REFERENCES `fees_masters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_master_childrens_fees_type_id_foreign` FOREIGN KEY (`fees_type_id`) REFERENCES `fees_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_master_childrens`
--

LOCK TABLES `fees_master_childrens` WRITE;
/*!40000 ALTER TABLE `fees_master_childrens` DISABLE KEYS */;
/*!40000 ALTER TABLE `fees_master_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_masters`
--

DROP TABLE IF EXISTS `fees_masters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_masters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `fees_group_id` bigint unsigned NOT NULL,
  `fees_type_id` bigint unsigned NOT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT '0.00',
  `fine_type` tinyint NOT NULL DEFAULT '0' COMMENT '0 = none, 1 = percentage, 2 = fixed',
  `percentage` int DEFAULT '0',
  `fine_amount` decimal(16,2) DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fees_masters_session_id_foreign` (`session_id`),
  KEY `fees_masters_fees_group_id_foreign` (`fees_group_id`),
  KEY `fees_masters_fees_type_id_foreign` (`fees_type_id`),
  CONSTRAINT `fees_masters_fees_group_id_foreign` FOREIGN KEY (`fees_group_id`) REFERENCES `fees_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_masters_fees_type_id_foreign` FOREIGN KEY (`fees_type_id`) REFERENCES `fees_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_masters_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_masters`
--

LOCK TABLES `fees_masters` WRITE;
/*!40000 ALTER TABLE `fees_masters` DISABLE KEYS */;
INSERT INTO `fees_masters` VALUES (1,1,1,1,'2025-09-10',30.00,0,0,0.00,1,'2025-09-01 05:02:08','2025-09-02 01:17:24',1),(2,1,1,2,'2025-09-10',15.00,0,0,0.00,1,'2025-09-02 01:07:39','2025-09-02 01:17:08',1),(3,1,2,1,'2025-09-10',15.00,0,0,0.00,1,'2025-09-05 22:55:46','2025-09-05 22:55:46',1),(4,1,2,2,'2025-09-10',15.00,0,0,0.00,1,'2025-09-05 22:56:37','2025-09-05 22:56:37',1);
/*!40000 ALTER TABLE `fees_masters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_types`
--

DROP TABLE IF EXISTS `fees_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `academic_level` enum('primary','secondary','high_school','kg','all') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all' COMMENT 'Academic level this fee type applies to',
  `amount` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT 'Default/base amount for this service',
  `due_date_offset` int NOT NULL DEFAULT '30' COMMENT 'Days from term start when this fee is due',
  `is_mandatory_for_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Required for students in the specified academic level',
  `category` enum('academic','transport','meal','accommodation','activity','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'academic' COMMENT 'Category of service for organization',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_fees_types_level_status` (`academic_level`,`status`),
  KEY `idx_fees_types_category_status` (`category`,`status`),
  KEY `idx_fees_types_mandatory_level` (`is_mandatory_for_level`,`academic_level`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_types`
--

LOCK TABLES `fees_types` WRITE;
/*!40000 ALTER TABLE `fees_types` DISABLE KEYS */;
INSERT INTO `fees_types` VALUES (1,'Full Tution Fee Secondary','Secondary Tuition','Lacagta Bisha ardayda Secondaryga','secondary',30.00,30,1,'academic',1,'2025-09-01 05:01:27','2025-09-10 08:10:15',1),(2,'Bus Fee','Bus','Lacagta Baska','all',15.00,30,0,'transport',1,'2025-09-02 00:46:06','2025-09-10 08:10:15',1),(3,'Full Tution Fee Primary','Primary Tuition','Lacagta Bisha Primaryga','primary',15.00,50,1,'academic',1,'2025-09-10 00:08:12','2025-09-10 08:10:15',1),(4,'Full Tution Fee KG','KG Tuition','Lacagta Bisha ardayda KG','kg',15.00,30,1,'academic',1,'2025-09-10 00:09:24','2025-09-10 08:10:15',1);
/*!40000 ALTER TABLE `fees_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag_icons`
--

DROP TABLE IF EXISTS `flag_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flag_icons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `icon_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag_icons`
--

LOCK TABLES `flag_icons` WRITE;
/*!40000 ALTER TABLE `flag_icons` DISABLE KEYS */;
INSERT INTO `flag_icons` VALUES (1,'flag-icon flag-icon-ad','ad','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'flag-icon flag-icon-ae','ae','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'flag-icon flag-icon-af','af','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'flag-icon flag-icon-ag','ag','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'flag-icon flag-icon-ai','ai','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'flag-icon flag-icon-al','al','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'flag-icon flag-icon-am','am','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'flag-icon flag-icon-ao','ao','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'flag-icon flag-icon-aq','aq','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'flag-icon flag-icon-ar','ar','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'flag-icon flag-icon-as','as','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'flag-icon flag-icon-at','at','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,'flag-icon flag-icon-au','au','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,'flag-icon flag-icon-aw','aw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,'flag-icon flag-icon-ax','ax','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,'flag-icon flag-icon-az','az','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,'flag-icon flag-icon-ba','ba','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,'flag-icon flag-icon-bb','bb','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,'flag-icon flag-icon-bd','bd','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,'flag-icon flag-icon-be','be','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,'flag-icon flag-icon-bf','bf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,'flag-icon flag-icon-bg','bg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,'flag-icon flag-icon-bh','bh','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,'flag-icon flag-icon-bi','bi','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(25,'flag-icon flag-icon-bj','bj','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(26,'flag-icon flag-icon-bl','bl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(27,'flag-icon flag-icon-bm','bm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(28,'flag-icon flag-icon-bn','bn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(29,'flag-icon flag-icon-bo','bo','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(30,'flag-icon flag-icon-bq','bq','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(31,'flag-icon flag-icon-br','br','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(32,'flag-icon flag-icon-bs','bs','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(33,'flag-icon flag-icon-bt','bt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(34,'flag-icon flag-icon-bv','bv','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(35,'flag-icon flag-icon-bw','bw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(36,'flag-icon flag-icon-by','by','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(37,'flag-icon flag-icon-bz','bz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(38,'flag-icon flag-icon-ca','ca','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(39,'flag-icon flag-icon-cc','cc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(40,'flag-icon flag-icon-cd','cd','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(41,'flag-icon flag-icon-cf','cf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(42,'flag-icon flag-icon-cg','cg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(43,'flag-icon flag-icon-ch','ch','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(44,'flag-icon flag-icon-ci','ci','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(45,'flag-icon flag-icon-ck','ck','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(46,'flag-icon flag-icon-cl','cl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(47,'flag-icon flag-icon-cm','cm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(48,'flag-icon flag-icon-cn','cn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(49,'flag-icon flag-icon-co','co','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(50,'flag-icon flag-icon-cr','cr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(51,'flag-icon flag-icon-cu','cu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(52,'flag-icon flag-icon-cv','cv','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(53,'flag-icon flag-icon-cw','cw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(54,'flag-icon flag-icon-cx','cx','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(55,'flag-icon flag-icon-cy','cy','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(56,'flag-icon flag-icon-cz','cz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(57,'flag-icon flag-icon-de','de','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(58,'flag-icon flag-icon-dj','dj','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(59,'flag-icon flag-icon-dk','dk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(60,'flag-icon flag-icon-dm','dm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(61,'flag-icon flag-icon-do','do','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(62,'flag-icon flag-icon-dz','dz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(63,'flag-icon flag-icon-ec','ec','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(64,'flag-icon flag-icon-ee','ee','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(65,'flag-icon flag-icon-eg','eg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(66,'flag-icon flag-icon-eh','eh','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(67,'flag-icon flag-icon-er','er','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(68,'flag-icon flag-icon-es','es','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(69,'flag-icon flag-icon-et','et','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(70,'flag-icon flag-icon-fi','fi','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(71,'flag-icon flag-icon-fj','fj','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(72,'flag-icon flag-icon-fk','fk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(73,'flag-icon flag-icon-fm','fm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(74,'flag-icon flag-icon-fo','fo','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(75,'flag-icon flag-icon-fr','fr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(76,'flag-icon flag-icon-ga','ga','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(77,'flag-icon flag-icon-gb','gb','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(78,'flag-icon flag-icon-gd','gd','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(79,'flag-icon flag-icon-ge','ge','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(80,'flag-icon flag-icon-gf','gf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(81,'flag-icon flag-icon-gg','gg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(82,'flag-icon flag-icon-gh','gh','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(83,'flag-icon flag-icon-gi','gi','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(84,'flag-icon flag-icon-gl','gl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(85,'flag-icon flag-icon-gm','gm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(86,'flag-icon flag-icon-gn','gn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(87,'flag-icon flag-icon-gp','gp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(88,'flag-icon flag-icon-gq','gq','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(89,'flag-icon flag-icon-gr','gr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(90,'flag-icon flag-icon-gs','gs','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(91,'flag-icon flag-icon-gt','gt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(92,'flag-icon flag-icon-gu','gu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(93,'flag-icon flag-icon-gw','gw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(94,'flag-icon flag-icon-gy','gy','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(95,'flag-icon flag-icon-hk','hk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(96,'flag-icon flag-icon-hm','hm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(97,'flag-icon flag-icon-hn','hn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(98,'flag-icon flag-icon-hr','hr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(99,'flag-icon flag-icon-ht','ht','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(100,'flag-icon flag-icon-hu','hu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(101,'flag-icon flag-icon-id','id','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(102,'flag-icon flag-icon-ie','ie','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(103,'flag-icon flag-icon-il','il','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(104,'flag-icon flag-icon-im','im','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(105,'flag-icon flag-icon-in','in','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(106,'flag-icon flag-icon-io','io','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(107,'flag-icon flag-icon-iq','iq','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(108,'flag-icon flag-icon-ir','ir','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(109,'flag-icon flag-icon-is','is','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(110,'flag-icon flag-icon-it','it','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(111,'flag-icon flag-icon-je','je','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(112,'flag-icon flag-icon-jm','jm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(113,'flag-icon flag-icon-jo','jo','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(114,'flag-icon flag-icon-jp','jp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(115,'flag-icon flag-icon-ke','ke','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(116,'flag-icon flag-icon-kg','kg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(117,'flag-icon flag-icon-kh','kh','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(118,'flag-icon flag-icon-ki','ki','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(119,'flag-icon flag-icon-km','km','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(120,'flag-icon flag-icon-kn','kn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(121,'flag-icon flag-icon-kp','kp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(122,'flag-icon flag-icon-kr','kr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(123,'flag-icon flag-icon-kw','kw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(124,'flag-icon flag-icon-ky','ky','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(125,'flag-icon flag-icon-kz','kz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(126,'flag-icon flag-icon-la','la','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(127,'flag-icon flag-icon-lb','lb','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(128,'flag-icon flag-icon-lc','lc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(129,'flag-icon flag-icon-li','li','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(130,'flag-icon flag-icon-lk','lk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(131,'flag-icon flag-icon-lr','lr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(132,'flag-icon flag-icon-ls','ls','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(133,'flag-icon flag-icon-lt','lt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(134,'flag-icon flag-icon-lu','lu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(135,'flag-icon flag-icon-lv','lv','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(136,'flag-icon flag-icon-ly','ly','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(137,'flag-icon flag-icon-ma','ma','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(138,'flag-icon flag-icon-mc','mc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(139,'flag-icon flag-icon-md','md','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(140,'flag-icon flag-icon-me','me','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(141,'flag-icon flag-icon-mf','mf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(142,'flag-icon flag-icon-mg','mg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(143,'flag-icon flag-icon-mh','mh','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(144,'flag-icon flag-icon-mk','mk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(145,'flag-icon flag-icon-ml','ml','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(146,'flag-icon flag-icon-mm','mm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(147,'flag-icon flag-icon-mn','mn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(148,'flag-icon flag-icon-mo','mo','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(149,'flag-icon flag-icon-mp','mp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(150,'flag-icon flag-icon-mq','mq','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(151,'flag-icon flag-icon-mr','mr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(152,'flag-icon flag-icon-ms','ms','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(153,'flag-icon flag-icon-mt','mt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(154,'flag-icon flag-icon-mu','mu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(155,'flag-icon flag-icon-mv','mv','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(156,'flag-icon flag-icon-mw','mw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(157,'flag-icon flag-icon-mx','mx','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(158,'flag-icon flag-icon-my','my','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(159,'flag-icon flag-icon-mz','mz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(160,'flag-icon flag-icon-na','na','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(161,'flag-icon flag-icon-nc','nc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(162,'flag-icon flag-icon-ne','ne','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(163,'flag-icon flag-icon-nf','nf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(164,'flag-icon flag-icon-ng','ng','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(165,'flag-icon flag-icon-ni','ni','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(166,'flag-icon flag-icon-nl','nl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(167,'flag-icon flag-icon-no','no','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(168,'flag-icon flag-icon-np','np','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(169,'flag-icon flag-icon-nr','nr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(170,'flag-icon flag-icon-nu','nu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(171,'flag-icon flag-icon-nz','nz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(172,'flag-icon flag-icon-om','om','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(173,'flag-icon flag-icon-pa','pa','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(174,'flag-icon flag-icon-pe','pe','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(175,'flag-icon flag-icon-pf','pf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(176,'flag-icon flag-icon-pg','pg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(177,'flag-icon flag-icon-ph','ph','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(178,'flag-icon flag-icon-pk','pk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(179,'flag-icon flag-icon-pl','pl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(180,'flag-icon flag-icon-pm','pm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(181,'flag-icon flag-icon-pn','pn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(182,'flag-icon flag-icon-pr','pr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(183,'flag-icon flag-icon-ps','ps','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(184,'flag-icon flag-icon-pt','pt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(185,'flag-icon flag-icon-pw','pw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(186,'flag-icon flag-icon-py','py','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(187,'flag-icon flag-icon-qa','qa','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(188,'flag-icon flag-icon-re','re','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(189,'flag-icon flag-icon-ro','ro','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(190,'flag-icon flag-icon-rs','rs','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(191,'flag-icon flag-icon-ru','ru','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(192,'flag-icon flag-icon-rw','rw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(193,'flag-icon flag-icon-sa','sa','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(194,'flag-icon flag-icon-sb','sb','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(195,'flag-icon flag-icon-sc','sc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(196,'flag-icon flag-icon-sd','sd','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(197,'flag-icon flag-icon-se','se','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(198,'flag-icon flag-icon-sg','sg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(199,'flag-icon flag-icon-sh','sh','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(200,'flag-icon flag-icon-si','si','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(201,'flag-icon flag-icon-sj','sj','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(202,'flag-icon flag-icon-sk','sk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(203,'flag-icon flag-icon-sl','sl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(204,'flag-icon flag-icon-sm','sm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(205,'flag-icon flag-icon-sn','sn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(206,'flag-icon flag-icon-so','so','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(207,'flag-icon flag-icon-sr','sr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(208,'flag-icon flag-icon-ss','ss','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(209,'flag-icon flag-icon-st','st','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(210,'flag-icon flag-icon-sv','sv','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(211,'flag-icon flag-icon-sx','sx','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(212,'flag-icon flag-icon-sy','sy','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(213,'flag-icon flag-icon-sz','sz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(214,'flag-icon flag-icon-tc','tc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(215,'flag-icon flag-icon-td','td','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(216,'flag-icon flag-icon-tf','tf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(217,'flag-icon flag-icon-tg','tg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(218,'flag-icon flag-icon-th','th','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(219,'flag-icon flag-icon-tj','tj','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(220,'flag-icon flag-icon-tk','tk','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(221,'flag-icon flag-icon-tl','tl','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(222,'flag-icon flag-icon-tm','tm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(223,'flag-icon flag-icon-tn','tn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(224,'flag-icon flag-icon-to','to','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(225,'flag-icon flag-icon-tr','tr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(226,'flag-icon flag-icon-tt','tt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(227,'flag-icon flag-icon-tv','tv','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(228,'flag-icon flag-icon-tw','tw','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(229,'flag-icon flag-icon-tz','tz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(230,'flag-icon flag-icon-ua','ua','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(231,'flag-icon flag-icon-ug','ug','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(232,'flag-icon flag-icon-um','um','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(233,'flag-icon flag-icon-us','us','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(234,'flag-icon flag-icon-uy','uy','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(235,'flag-icon flag-icon-uz','uz','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(236,'flag-icon flag-icon-va','va','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(237,'flag-icon flag-icon-vc','vc','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(238,'flag-icon flag-icon-ve','ve','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(239,'flag-icon flag-icon-vg','vg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(240,'flag-icon flag-icon-vi','vi','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(241,'flag-icon flag-icon-vn','vn','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(242,'flag-icon flag-icon-vu','vu','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(243,'flag-icon flag-icon-wf','wf','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(244,'flag-icon flag-icon-ws','ws','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(245,'flag-icon flag-icon-ye','ye','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(246,'flag-icon flag-icon-yt','yt','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(247,'flag-icon flag-icon-za','za','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(248,'flag-icon flag-icon-zm','zm','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(249,'flag-icon flag-icon-zw','zw','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `flag_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_post_comments`
--

DROP TABLE IF EXISTS `forum_post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_post_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `forum_post_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int DEFAULT NULL,
  `published_by` bigint unsigned NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_post_comments_parent_id_foreign` (`parent_id`),
  KEY `forum_post_comments_forum_post_id_foreign` (`forum_post_id`),
  KEY `forum_post_comments_published_by_foreign` (`published_by`),
  CONSTRAINT `forum_post_comments_forum_post_id_foreign` FOREIGN KEY (`forum_post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_post_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forum_post_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_post_comments_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_post_comments`
--

LOCK TABLES `forum_post_comments` WRITE;
/*!40000 ALTER TABLE `forum_post_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_post_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `target_roles` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `upload_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `rejected_by` int DEFAULT NULL,
  `pending_by` int DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_posts_upload_id_foreign` (`upload_id`),
  KEY `forum_posts_created_by_foreign` (`created_by`),
  CONSTRAINT `forum_posts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_posts_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gallery_category_id` bigint unsigned NOT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `galleries_gallery_category_id_foreign` (`gallery_category_id`),
  KEY `galleries_upload_id_foreign` (`upload_id`),
  CONSTRAINT `galleries_gallery_category_id_foreign` FOREIGN KEY (`gallery_category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `galleries_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
INSERT INTO `galleries` VALUES (1,1,32,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,3,33,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,1,34,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,1,35,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,2,36,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,3,37,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,4,38,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,3,39,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,4,40,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,1,41,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,4,42,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,3,43,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,1,44,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,2,45,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,1,46,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,2,47,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,4,48,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,3,49,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,2,50,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,1,51,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,1,52,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,2,53,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,4,54,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,4,55,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_categories`
--

DROP TABLE IF EXISTS `gallery_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_categories`
--

LOCK TABLES `gallery_categories` WRITE;
/*!40000 ALTER TABLE `gallery_categories` DISABLE KEYS */;
INSERT INTO `gallery_categories` VALUES (1,'Admission',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Annual Program',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Awards',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'Curriculum',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `gallery_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_category_translates`
--

DROP TABLE IF EXISTS `gallery_category_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_category_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gallery_category_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `gallery_category_translates_gallery_category_id_foreign` (`gallery_category_id`),
  CONSTRAINT `gallery_category_translates_gallery_category_id_foreign` FOREIGN KEY (`gallery_category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_category_translates`
--

LOCK TABLES `gallery_category_translates` WRITE;
/*!40000 ALTER TABLE `gallery_category_translates` DISABLE KEYS */;
INSERT INTO `gallery_category_translates` VALUES (1,1,'en','Admission','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Annual Program','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Awards','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,4,'en','Curriculum','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,1,'bn','ভর্তি','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,2,'bn','বার্ষিক প্রোগ্রাম','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,3,'bn','পুরস্কার','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,4,'bn','পাঠ্যক্রম','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `gallery_category_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gender_translates`
--

DROP TABLE IF EXISTS `gender_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gender_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gender_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `gender_translates_gender_id_foreign` (`gender_id`),
  CONSTRAINT `gender_translates_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gender_translates`
--

LOCK TABLES `gender_translates` WRITE;
/*!40000 ALTER TABLE `gender_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `gender_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genders`
--

DROP TABLE IF EXISTS `genders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genders`
--

LOCK TABLES `genders` WRITE;
/*!40000 ALTER TABLE `genders` DISABLE KEYS */;
INSERT INTO `genders` VALUES (1,'Male',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Female',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `genders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gmeets`
--

DROP TABLE IF EXISTS `gmeets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gmeets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gmeet_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `gmeets_session_id_foreign` (`session_id`),
  KEY `gmeets_classes_id_foreign` (`classes_id`),
  KEY `gmeets_section_id_foreign` (`section_id`),
  KEY `gmeets_subject_id_foreign` (`subject_id`),
  CONSTRAINT `gmeets_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeets_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeets_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeets_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gmeets`
--

LOCK TABLES `gmeets` WRITE;
/*!40000 ALTER TABLE `gmeets` DISABLE KEYS */;
/*!40000 ALTER TABLE `gmeets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homework`
--

DROP TABLE IF EXISTS `homework`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homework` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `submission_date` date DEFAULT NULL,
  `marks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `document_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `homework_session_id_foreign` (`session_id`),
  KEY `homework_classes_id_foreign` (`classes_id`),
  KEY `homework_section_id_foreign` (`section_id`),
  KEY `homework_subject_id_foreign` (`subject_id`),
  KEY `homework_document_id_foreign` (`document_id`),
  KEY `homework_created_by_foreign` (`created_by`),
  CONSTRAINT `homework_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homework`
--

LOCK TABLES `homework` WRITE;
/*!40000 ALTER TABLE `homework` DISABLE KEYS */;
/*!40000 ALTER TABLE `homework` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homework_students`
--

DROP TABLE IF EXISTS `homework_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homework_students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `homework_id` bigint unsigned NOT NULL,
  `homework` bigint unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `marks` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `homework_students_student_id_foreign` (`student_id`),
  KEY `homework_students_homework_id_foreign` (`homework_id`),
  KEY `homework_students_homework_foreign` (`homework`),
  CONSTRAINT `homework_students_homework_foreign` FOREIGN KEY (`homework`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_students_homework_id_foreign` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homework_students`
--

LOCK TABLES `homework_students` WRITE;
/*!40000 ALTER TABLE `homework_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `homework_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `id_cards`
--

DROP TABLE IF EXISTS `id_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `id_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_date` date DEFAULT NULL,
  `frontside_bg_image` bigint unsigned DEFAULT NULL,
  `backside_bg_image` bigint unsigned DEFAULT NULL,
  `signature` bigint unsigned DEFAULT NULL,
  `qr_code` bigint unsigned DEFAULT NULL,
  `backside_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `student_name` tinyint(1) NOT NULL DEFAULT '1',
  `admission_no` tinyint(1) NOT NULL DEFAULT '1',
  `roll_no` tinyint(1) NOT NULL DEFAULT '1',
  `class_name` tinyint(1) NOT NULL DEFAULT '1',
  `section_name` tinyint(1) NOT NULL DEFAULT '1',
  `blood_group` tinyint(1) NOT NULL DEFAULT '1',
  `dob` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_cards_frontside_bg_image_foreign` (`frontside_bg_image`),
  KEY `id_cards_backside_bg_image_foreign` (`backside_bg_image`),
  KEY `id_cards_signature_foreign` (`signature`),
  KEY `id_cards_qr_code_foreign` (`qr_code`),
  CONSTRAINT `id_cards_backside_bg_image_foreign` FOREIGN KEY (`backside_bg_image`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `id_cards_frontside_bg_image_foreign` FOREIGN KEY (`frontside_bg_image`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `id_cards_qr_code_foreign` FOREIGN KEY (`qr_code`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `id_cards_signature_foreign` FOREIGN KEY (`signature`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `id_cards`
--

LOCK TABLES `id_cards` WRITE;
/*!40000 ALTER TABLE `id_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `id_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incomes`
--

DROP TABLE IF EXISTS `incomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incomes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_head` bigint unsigned NOT NULL,
  `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fees_collect_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `incomes_session_id_foreign` (`session_id`),
  KEY `incomes_income_head_foreign` (`income_head`),
  KEY `incomes_upload_id_foreign` (`upload_id`),
  KEY `incomes_fees_collect_id_foreign` (`fees_collect_id`),
  CONSTRAINT `incomes_fees_collect_id_foreign` FOREIGN KEY (`fees_collect_id`) REFERENCES `fees_collects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incomes_income_head_foreign` FOREIGN KEY (`income_head`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incomes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incomes_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incomes`
--

LOCK TABLES `incomes` WRITE;
/*!40000 ALTER TABLE `incomes` DISABLE KEYS */;
/*!40000 ALTER TABLE `incomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issue_books`
--

DROP TABLE IF EXISTS `issue_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `issue_books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `book_id` bigint unsigned NOT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `issue_books_user_id_foreign` (`user_id`),
  KEY `issue_books_book_id_foreign` (`book_id`),
  CONSTRAINT `issue_books_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `issue_books_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue_books`
--

LOCK TABLES `issue_books` WRITE;
/*!40000 ALTER TABLE `issue_books` DISABLE KEYS */;
/*!40000 ALTER TABLE `issue_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"f672f930-6cfc-4cc7-86ed-c8f58325fe7c\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:31;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(2,'default','{\"uuid\":\"8bde2a54-9bf6-43e8-8d02-d52766958957\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:57;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Ayan Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251001\\\";s:7:\\\"roll_no\\\";i:1;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(3,'default','{\"uuid\":\"645cf97d-9663-4563-9a26-e53609b6e179\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:48;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Ayan Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251001\\\";s:7:\\\"roll_no\\\";i:1;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(4,'default','{\"uuid\":\"36b35e89-323f-4e9d-9bee-6afdb1e37df9\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:57;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(5,'default','{\"uuid\":\"69c479c9-f2dd-4fbe-9f13-1a7a112f685d\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:83;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:9:\\\"Hodan Ali\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251027\\\";s:7:\\\"roll_no\\\";i:27;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(6,'default','{\"uuid\":\"07a9ffb6-bd41-4fa6-81bc-a928f305558d\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:47;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:9:\\\"Hodan Ali\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251027\\\";s:7:\\\"roll_no\\\";i:27;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(7,'default','{\"uuid\":\"f9b604a2-25aa-430f-ba6b-c9724e52f214\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:61;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(8,'default','{\"uuid\":\"fdba5623-29fc-4aec-8669-1c9429ecda01\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:108;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:18:\\\"Iksiir Ali Jimcale\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254031\\\";s:7:\\\"roll_no\\\";i:31;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(9,'default','{\"uuid\":\"93c96024-a4aa-4d08-bcad-87c384239d7c\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:45;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:18:\\\"Iksiir Ali Jimcale\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254031\\\";s:7:\\\"roll_no\\\";i:31;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756878618,\"delay\":null}',0,NULL,1756878618,1756878618,1),(10,'default','{\"uuid\":\"51f0366e-8638-4f98-9cb4-9ed36c5aac8e\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:32;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756879066,\"delay\":null}',0,NULL,1756879066,1756879066,1),(11,'default','{\"uuid\":\"e8b15937-9678-4faf-b4ad-d051b9143850\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:58;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:20:\\\"Abdirashid Abdullahi\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252002\\\";s:7:\\\"roll_no\\\";i:2;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756879066,\"delay\":null}',0,NULL,1756879066,1756879066,1),(12,'default','{\"uuid\":\"cba41152-8af7-4e01-b90e-a8d41232aceb\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:55;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:20:\\\"Abdirashid Abdullahi\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252002\\\";s:7:\\\"roll_no\\\";i:2;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756879066,\"delay\":null}',0,NULL,1756879066,1756879066,1),(13,'default','{\"uuid\":\"e9e85bed-efd5-4ffd-b5fd-c52fbed945fc\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:58;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756879066,\"delay\":null}',0,NULL,1756879066,1756879066,1),(14,'default','{\"uuid\":\"123c86ae-5312-4308-9555-01ed94e81c16\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:84;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Cali Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252028\\\";s:7:\\\"roll_no\\\";i:28;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756879066,\"delay\":null}',0,NULL,1756879066,1756879066,1),(15,'default','{\"uuid\":\"2c2c9689-49ec-42aa-b04f-ace112681a19\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:50;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Cali Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252028\\\";s:7:\\\"roll_no\\\";i:28;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756879066,\"delay\":null}',0,NULL,1756879066,1756879066,1),(16,'default','{\"uuid\":\"e4ea756f-3ae5-4f37-8096-9eceb5feb62d\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:31;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(17,'default','{\"uuid\":\"da936743-01ae-4b79-96d0-f85c07ae4066\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:57;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Ayan Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251001\\\";s:7:\\\"roll_no\\\";i:1;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(18,'default','{\"uuid\":\"ac31c2d5-3f16-4212-8ca7-d34604c40452\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:48;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Ayan Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251001\\\";s:7:\\\"roll_no\\\";i:1;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(19,'default','{\"uuid\":\"cceecf66-a71f-460b-846e-3c0340fbf646\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:57;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(20,'default','{\"uuid\":\"9f2f819b-2c05-4c31-bfac-b07b13439b58\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:83;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:9:\\\"Hodan Ali\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251027\\\";s:7:\\\"roll_no\\\";i:27;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(21,'default','{\"uuid\":\"88a6e07c-65c4-4a51-9413-67cfb28c8dd1\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:47;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:9:\\\"Hodan Ali\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251027\\\";s:7:\\\"roll_no\\\";i:27;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(22,'default','{\"uuid\":\"0694a057-8164-41f6-8d67-b6b652ce5104\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:61;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(23,'default','{\"uuid\":\"ec05edac-d7f9-4ae7-8462-bde4d6acbacf\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:108;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:18:\\\"Iksiir Ali Jimcale\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254031\\\";s:7:\\\"roll_no\\\";i:31;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(24,'default','{\"uuid\":\"986b02d0-86e5-4c88-840c-a36fa0e19abe\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:45;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:18:\\\"Iksiir Ali Jimcale\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254031\\\";s:7:\\\"roll_no\\\";i:31;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756879111,\"delay\":null}',0,NULL,1756879111,1756879111,1),(25,'default','{\"uuid\":\"b1aa281e-021c-4527-a241-61b69f861305\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:33;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:6;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756884296,\"delay\":null}',0,NULL,1756884296,1756884296,1),(26,'default','{\"uuid\":\"1afd6959-7ab0-47f4-88e9-f1e8e8e28e31\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:59;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Omar Saeed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253003\\\";s:7:\\\"roll_no\\\";i:3;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"HALFDAY\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756884296,\"delay\":null}',0,NULL,1756884296,1756884296,1),(27,'default','{\"uuid\":\"786e6d3d-a657-46f7-9110-7f4a0d2d5578\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:48;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Omar Saeed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253003\\\";s:7:\\\"roll_no\\\";i:3;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"HALFDAY\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756884296,\"delay\":null}',0,NULL,1756884296,1756884296,1),(28,'default','{\"uuid\":\"863d7756-bda7-47be-8d14-85a0a5165252\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:59;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756884296,\"delay\":null}',0,NULL,1756884296,1756884296,1),(29,'default','{\"uuid\":\"4f445909-f347-4581-8f9d-8afafc3b35cb\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:85;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:12:\\\"Halima Yusuf\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253029\\\";s:7:\\\"roll_no\\\";i:29;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756884296,\"delay\":null}',0,NULL,1756884296,1756884296,1),(30,'default','{\"uuid\":\"94915bea-437b-4afc-a8f0-e95af04c7042\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:45;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:12:\\\"Halima Yusuf\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253029\\\";s:7:\\\"roll_no\\\";i:29;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756884296,\"delay\":null}',0,NULL,1756884296,1756884296,1),(31,'default','{\"uuid\":\"4a0f967b-1abf-4d32-9e3c-e04b515e9aa3\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:34;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:8;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756896380,\"delay\":null}',0,NULL,1756896380,1756896380,1),(32,'default','{\"uuid\":\"a85544ba-edd9-47c9-8cee-fae181bb9584\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:60;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:13:\\\"Mohamed Saeed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254004\\\";s:7:\\\"roll_no\\\";i:4;s:5:\\\"class\\\";s:6:\\\"Form3B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756896380,\"delay\":null}',0,NULL,1756896380,1756896380,1),(33,'default','{\"uuid\":\"6c9075b1-741a-454d-af44-cf2e465c797e\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:54;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:13:\\\"Mohamed Saeed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254004\\\";s:7:\\\"roll_no\\\";i:4;s:5:\\\"class\\\";s:6:\\\"Form3B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756896380,\"delay\":null}',0,NULL,1756896380,1756896380,1),(34,'default','{\"uuid\":\"53e5b230-119e-41a3-b7b1-7f459c8beb95\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:60;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756896380,\"delay\":null}',0,NULL,1756896380,1756896380,1),(35,'default','{\"uuid\":\"f47baf0d-df74-465a-833b-82796bfbc3dc\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:86;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:14:\\\"Halima Guuleed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254030\\\";s:7:\\\"roll_no\\\";i:30;s:5:\\\"class\\\";s:6:\\\"Form3B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756896380,\"delay\":null}',0,NULL,1756896380,1756896380,1),(36,'default','{\"uuid\":\"7aae660b-7aa9-4db2-9e9d-4266b2cbf5b4\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:49;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:14:\\\"Halima Guuleed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254030\\\";s:7:\\\"roll_no\\\";i:30;s:5:\\\"class\\\";s:6:\\\"Form3B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"03 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756896380,\"delay\":null}',0,NULL,1756896380,1756896380,1),(37,'default','{\"uuid\":\"db8f2397-cc5b-48a6-9431-760ea9c5823b\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:33;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756962758,\"delay\":null}',0,NULL,1756962758,1756962758,1),(38,'default','{\"uuid\":\"d1c1b2d1-259f-45f9-988f-2a6034c2eb7c\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:59;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Omar Saeed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253003\\\";s:7:\\\"roll_no\\\";i:3;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"04 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756962758,\"delay\":null}',0,NULL,1756962758,1756962758,1),(39,'default','{\"uuid\":\"a762c19f-9b0b-419b-af57-338eef1cd425\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:48;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Omar Saeed\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253003\\\";s:7:\\\"roll_no\\\";i:3;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"04 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756962758,\"delay\":null}',0,NULL,1756962758,1756962758,1),(40,'default','{\"uuid\":\"424d0c5c-e478-431b-9015-822df8b79c9f\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:59;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1756962758,\"delay\":null}',0,NULL,1756962758,1756962758,1),(41,'default','{\"uuid\":\"5758290f-c882-430c-b7a2-e2e60a21471a\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:85;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:12:\\\"Halima Yusuf\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253029\\\";s:7:\\\"roll_no\\\";i:29;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"04 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1756962758,\"delay\":null}',0,NULL,1756962758,1756962758,1),(42,'default','{\"uuid\":\"ba0e9413-8951-478f-9fec-14cbdb585098\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:45;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:12:\\\"Halima Yusuf\\\";s:12:\\\"admission_no\\\";s:8:\\\"20253029\\\";s:7:\\\"roll_no\\\";i:29;s:5:\\\"class\\\";s:6:\\\"Form3A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"04 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1756962758,\"delay\":null}',0,NULL,1756962758,1756962758,1),(43,'default','{\"uuid\":\"cad5541d-150a-454a-8811-6d240884f15a\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:32;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:12;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1757320594,\"delay\":null}',0,NULL,1757320594,1757320594,1),(44,'default','{\"uuid\":\"04de0924-5656-477a-b276-f0b7860e50bc\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:58;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:20:\\\"Abdirashid Abdullahi\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252002\\\";s:7:\\\"roll_no\\\";i:2;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"08 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"HALFDAY\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1757320594,\"delay\":null}',0,NULL,1757320594,1757320594,1),(45,'default','{\"uuid\":\"df7fd828-3cad-475f-a591-aceabe604045\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:55;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:20:\\\"Abdirashid Abdullahi\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252002\\\";s:7:\\\"roll_no\\\";i:2;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"08 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"HALFDAY\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1757320594,\"delay\":null}',0,NULL,1757320594,1757320594,1),(46,'default','{\"uuid\":\"2f0bfad4-92a1-475f-83e7-36e02e1284bc\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:58;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1757320594,\"delay\":null}',0,NULL,1757320594,1757320594,1),(47,'default','{\"uuid\":\"b41bb794-6a48-4fc0-8eb5-4701343f007b\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:84;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Cali Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252028\\\";s:7:\\\"roll_no\\\";i:28;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"08 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1757320594,\"delay\":null}',0,NULL,1757320594,1757320594,1),(48,'default','{\"uuid\":\"b73b9dce-d20b-49fa-9a58-85f42b1aecd3\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:50;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Cali Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20252028\\\";s:7:\\\"roll_no\\\";i:28;s:5:\\\"class\\\";s:6:\\\"Form4B\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"08 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"PRESENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1757320594,\"delay\":null}',0,NULL,1757320594,1757320594,1),(49,'default','{\"uuid\":\"0ef3de47-9924-4c9f-966f-b55f555a5fe4\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:31;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(50,'default','{\"uuid\":\"968962ed-6232-4c10-8853-7a26baadb410\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:57;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Ayan Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251001\\\";s:7:\\\"roll_no\\\";i:1;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"10 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(51,'default','{\"uuid\":\"44d87c11-b7c0-4b8a-92f5-16688dbdce94\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:48;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:10:\\\"Ayan Farah\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251001\\\";s:7:\\\"roll_no\\\";i:1;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"10 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:4:\\\"LATE\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(52,'default','{\"uuid\":\"b8abc602-cb76-4248-98cd-50a9ea506c73\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:57;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(53,'default','{\"uuid\":\"38f4efaf-1d44-46ef-bacc-f135df02b245\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:83;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:9:\\\"Hodan Ali\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251027\\\";s:7:\\\"roll_no\\\";i:27;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"10 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:6:\\\"ABSENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(54,'default','{\"uuid\":\"c4050a2e-43d3-4655-b5cb-3ce8cff8e414\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:47;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:9:\\\"Hodan Ali\\\";s:12:\\\"admission_no\\\";s:8:\\\"20251027\\\";s:7:\\\"roll_no\\\";i:27;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"10 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:6:\\\"ABSENT\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(55,'default','{\"uuid\":\"e4972e73-bdfe-47fd-aaf9-7db1e7308898\",\"displayName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\",\"command\":\"O:41:\\\"App\\\\Jobs\\\\StudentAttendanceNotificationJOb\\\":2:{s:10:\\\"\\u0000*\\u0000student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:30:\\\"App\\\\Models\\\\StudentInfo\\\\Student\\\";s:2:\\\"id\\\";i:61;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"parent\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"\\u0000*\\u0000attendace\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Attendance\\\\Attendance\\\";s:2:\\\"id\\\";i:16;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(56,'default','{\"uuid\":\"cf15198d-627b-477a-8b9b-f6227bc279d5\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:108;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:18:\\\"Iksiir Ali Jimcale\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254031\\\";s:7:\\\"roll_no\\\";i:31;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"10 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"HALFDAY\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:7:\\\"Student\\\";}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1),(57,'default','{\"uuid\":\"cee1b71d-ba34-4ce5-a16b-db7f801143c3\",\"displayName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\NotificationSendJob\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\NotificationSendJob\\\":4:{s:10:\\\"\\u0000*\\u0000purpose\\\";s:18:\\\"Student_Attendance\\\";s:11:\\\"\\u0000*\\u0000user_ids\\\";a:1:{i:0;i:45;}s:7:\\\"\\u0000*\\u0000data\\\";a:8:{s:12:\\\"student_name\\\";s:18:\\\"Iksiir Ali Jimcale\\\";s:12:\\\"admission_no\\\";s:8:\\\"20254031\\\";s:7:\\\"roll_no\\\";i:31;s:5:\\\"class\\\";s:6:\\\"Form4A\\\";s:7:\\\"section\\\";s:1:\\\"A\\\";s:13:\\\"guardian_name\\\";s:0:\\\"\\\";s:15:\\\"attendance_date\\\";s:11:\\\"10 Sep 2025\\\";s:15:\\\"attendance_type\\\";s:7:\\\"HALFDAY\\\";}s:7:\\\"\\u0000*\\u0000role\\\";a:1:{i:0;s:6:\\\"Parent\\\";}}\"},\"createdAt\":1757510221,\"delay\":null}',0,NULL,1757510221,1757510221,1);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English','en','flag-icon flag-icon-us','ltr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Bangla','bn','flag-icon flag-icon-bd','ltr','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Arabic','ar','flag-icon flag-icon-sa','rtl','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `leave_type_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `request_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `session_id` bigint unsigned DEFAULT NULL,
  `attachment_id` bigint unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_days` int NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_requests_user_id_foreign` (`user_id`),
  KEY `leave_requests_role_id_foreign` (`role_id`),
  KEY `leave_requests_request_by_foreign` (`request_by`),
  KEY `leave_requests_approved_by_foreign` (`approved_by`),
  KEY `leave_requests_session_id_foreign` (`session_id`),
  KEY `leave_requests_attachment_id_foreign` (`attachment_id`),
  CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_attachment_id_foreign` FOREIGN KEY (`attachment_id`) REFERENCES `uploads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_request_by_foreign` FOREIGN KEY (`request_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint unsigned NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_types_role_id_foreign` (`role_id`),
  CONSTRAINT `leave_types_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mark_sheet_approvals`
--

DROP TABLE IF EXISTS `mark_sheet_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mark_sheet_approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `exam_type_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mark_sheet_approvals_session_id_foreign` (`session_id`),
  KEY `mark_sheet_approvals_classes_id_foreign` (`classes_id`),
  KEY `mark_sheet_approvals_section_id_foreign` (`section_id`),
  KEY `mark_sheet_approvals_exam_type_id_foreign` (`exam_type_id`),
  KEY `mark_sheet_approvals_student_id_foreign` (`student_id`),
  CONSTRAINT `mark_sheet_approvals_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mark_sheet_approvals_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mark_sheet_approvals_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mark_sheet_approvals_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mark_sheet_approvals_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mark_sheet_approvals`
--

LOCK TABLES `mark_sheet_approvals` WRITE;
/*!40000 ALTER TABLE `mark_sheet_approvals` DISABLE KEYS */;
/*!40000 ALTER TABLE `mark_sheet_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks_grades`
--

DROP TABLE IF EXISTS `marks_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marks_grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent_from` double NOT NULL,
  `percent_upto` double NOT NULL,
  `point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `marks_grades_session_id_foreign` (`session_id`),
  CONSTRAINT `marks_grades_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks_grades`
--

LOCK TABLES `marks_grades` WRITE;
/*!40000 ALTER TABLE `marks_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `marks_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks_register_childrens`
--

DROP TABLE IF EXISTS `marks_register_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marks_register_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marks_register_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mark` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `marks_register_childrens_marks_register_id_foreign` (`marks_register_id`),
  KEY `marks_register_childrens_student_id_foreign` (`student_id`),
  CONSTRAINT `marks_register_childrens_marks_register_id_foreign` FOREIGN KEY (`marks_register_id`) REFERENCES `marks_registers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marks_register_childrens_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks_register_childrens`
--

LOCK TABLES `marks_register_childrens` WRITE;
/*!40000 ALTER TABLE `marks_register_childrens` DISABLE KEYS */;
/*!40000 ALTER TABLE `marks_register_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks_registers`
--

DROP TABLE IF EXISTS `marks_registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marks_registers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `exam_type_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `is_marksheet_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `marks_registers_session_id_foreign` (`session_id`),
  KEY `marks_registers_classes_id_foreign` (`classes_id`),
  KEY `marks_registers_section_id_foreign` (`section_id`),
  KEY `marks_registers_exam_type_id_foreign` (`exam_type_id`),
  KEY `marks_registers_subject_id_foreign` (`subject_id`),
  CONSTRAINT `marks_registers_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marks_registers_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marks_registers_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marks_registers_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marks_registers_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks_registers`
--

LOCK TABLES `marks_registers` WRITE;
/*!40000 ALTER TABLE `marks_registers` DISABLE KEYS */;
/*!40000 ALTER TABLE `marks_registers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_categories`
--

DROP TABLE IF EXISTS `member_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_categories`
--

LOCK TABLES `member_categories` WRITE;
/*!40000 ALTER TABLE `member_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `members_user_id_foreign` (`user_id`),
  KEY `members_category_id_foreign` (`category_id`),
  CONSTRAINT `members_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `member_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memories`
--

DROP TABLE IF EXISTS `memories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_image_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_by` bigint unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `rejected_by` int DEFAULT NULL,
  `pending_by` int DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memories_feature_image_id_foreign` (`feature_image_id`),
  KEY `memories_created_by_foreign` (`created_by`),
  CONSTRAINT `memories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `memories_feature_image_id_foreign` FOREIGN KEY (`feature_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memories`
--

LOCK TABLES `memories` WRITE;
/*!40000 ALTER TABLE `memories` DISABLE KEYS */;
/*!40000 ALTER TABLE `memories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memory_galleries`
--

DROP TABLE IF EXISTS `memory_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memory_galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `memory_id` bigint unsigned DEFAULT NULL,
  `gallery_image_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memory_galleries_memory_id_foreign` (`memory_id`),
  KEY `memory_galleries_gallery_image_id_foreign` (`gallery_image_id`),
  CONSTRAINT `memory_galleries_gallery_image_id_foreign` FOREIGN KEY (`gallery_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `memory_galleries_memory_id_foreign` FOREIGN KEY (`memory_id`) REFERENCES `memories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memory_galleries`
--

LOCK TABLES `memory_galleries` WRITE;
/*!40000 ALTER TABLE `memory_galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `memory_galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `sender_id` bigint unsigned DEFAULT NULL,
  `receiver_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2013_08_03_072002_create_uploads_table',1,1),(2,'2013_08_03_072003_create_roles_table',1,1),(3,'2014_10_12_000000_create_users_table',1,1),(4,'2014_10_12_100000_create_password_resets_table',1,1),(5,'2019_08_19_000000_create_failed_jobs_table',1,1),(6,'2019_12_14_000001_create_personal_access_tokens_table',1,1),(7,'2022_07_19_045514_create_flag_icons_table',1,1),(8,'2022_08_08_043550_create_permissions_table',1,1),(9,'2022_08_16_103633_create_settings_table',1,1),(10,'2022_08_17_092623_create_languages_table',1,1),(11,'2022_10_04_044255_create_searches_table',1,1),(12,'2022_10_13_064230_create_designations_table',1,1),(13,'2023_02_20_101104_create_genders_table',1,1),(14,'2023_02_22_044252_create_religions_table',1,1),(15,'2023_02_22_053608_create_blood_groups_table',1,1),(16,'2023_02_22_070416_create_sessions_table',1,1),(17,'2023_02_22_100221_create_classes_table',1,1),(18,'2023_02_22_102118_create_student_categories_table',1,1),(19,'2023_02_22_115507_create_sections_table',1,1),(20,'2023_02_23_042918_create_shifts_table',1,1),(21,'2023_02_23_081806_create_subjects_table',1,1),(22,'2023_02_23_095042_create_parent_guardians_table',1,1),(23,'2023_02_23_113001_create_departments_table',1,1),(24,'2023_02_24_124400_create_students_table',1,1),(25,'2023_02_25_052716_create_class_rooms_table',1,1),(26,'2023_02_25_071052_create_fees_groups_table',1,1),(27,'2023_02_25_091226_create_fees_types_table',1,1),(28,'2023_02_25_102359_create_fees_masters_table',1,1),(29,'2023_02_27_045430_create_staff_table',1,1),(30,'2023_02_28_051437_create_exam_types_table',1,1),(31,'2023_02_28_065459_create_class_setups_table',1,1),(32,'2023_02_28_065614_create_class_setup_childrens_table',1,1),(33,'2023_02_28_090453_create_session_class_students_table',1,1),(34,'2023_03_01_115144_create_subject_assigns_table',1,1),(35,'2023_03_01_115229_create_subject_assign_childrens_table',1,1),(36,'2023_03_03_114236_create_marks_grades_table',1,1),(37,'2023_03_07_062402_create_exam_assigns_table',1,1),(38,'2023_03_12_053023_create_fees_assigns_table',1,1),(39,'2023_03_12_053024_create_fees_assign_childrens_table',1,1),(40,'2023_03_12_053025_create_account_heads_table',1,1),(41,'2023_03_12_053025_create_fees_collects_table',1,1),(42,'2023_03_12_053026_create_incomes_table',1,1),(43,'2023_03_12_090806_create_expenses_table',1,1),(44,'2023_03_13_054359_create_marks_registers_table',1,1),(45,'2023_03_13_101938_create_exam_assign_childrens_table',1,1),(46,'2023_03_13_132615_create_marks_register_childrens_table',1,1),(47,'2023_03_14_090857_create_fees_master_childrens_table',1,1),(48,'2023_03_17_113815_create_promote_students_table',1,1),(49,'2023_03_22_062320_create_time_schedules_table',1,1),(50,'2023_03_22_062321_create_class_routines_table',1,1),(51,'2023_03_24_053514_create_class_routine_childrens_table',1,1),(52,'2023_04_07_045518_create_exam_routines_table',1,1),(53,'2023_04_07_045719_create_exam_routine_childrens_table',1,1),(54,'2023_04_27_105438_create_examination_settings_table',1,1),(55,'2023_04_28_093751_create_sliders_table',1,1),(56,'2023_04_28_105549_create_counters_table',1,1),(57,'2023_04_30_070252_create_news_table',1,1),(58,'2023_04_30_123236_create_examination_results_table',1,1),(59,'2023_05_02_054153_create_gallery_categories_table',1,1),(60,'2023_05_02_060903_create_galleries_table',1,1),(61,'2023_05_03_033302_create_attendances_table',1,1),(62,'2023_05_09_095159_create_events_table',1,1),(63,'2023_05_18_095505_create_page_sections_table',1,1),(64,'2023_05_21_104600_create_contact_infos_table',1,1),(65,'2023_05_21_122123_create_department_contacts_table',1,1),(66,'2023_05_22_045924_create_contacts_table',1,1),(67,'2023_05_22_095703_create_subscribes_table',1,1),(68,'2023_05_24_044715_create_abouts_table',1,1),(69,'2023_06_14_071848_create_online_admissions_table',1,1),(70,'2023_06_17_090920_create_book_categories_table',1,1),(71,'2023_06_18_080708_create_books_table',1,1),(72,'2023_06_18_091300_create_member_categories_table',1,1),(73,'2023_06_18_091301_create_members_table',1,1),(74,'2023_06_18_093638_create_issue_books_table',1,1),(75,'2023_06_22_044425_create_homework_table',1,1),(76,'2023_07_12_083329_add_user_type_column_in_searches_table',1,1),(77,'2023_07_18_045644_create_question_groups_table',1,1),(78,'2023_07_18_055005_create_question_banks_table',1,1),(79,'2023_07_18_091545_create_question_bank_childrens_table',1,1),(80,'2023_07_19_085237_create_online_exams_table',1,1),(81,'2023_07_20_074247_create_online_exam_children_students_table',1,1),(82,'2023_07_20_074318_create_online_exam_children_questions_table',1,1),(83,'2023_07_26_041901_create_answers_table',1,1),(84,'2023_07_26_041949_create_answer_childrens_table',1,1),(85,'2023_07_28_150210_create_currencies_table',1,1),(86,'2023_08_02_132147_add_payment_gateway_and_transaction_id_in_fees_collects_table',1,1),(87,'2023_08_30_111142_create_subscriptions_table',1,1),(88,'2023_11_10_120311_create_homework_students_table',1,1),(89,'2023_11_14_155008_create_id_cards_table',1,1),(90,'2023_11_15_152219_create_certificates_table',1,1),(91,'2023_11_22_113507_create_gmeets_table',1,1),(92,'2023_11_23_122832_create_notice_boards_table',1,1),(93,'2023_11_27_122348_create_sms_mail_templates_table',1,1),(94,'2023_11_28_123854_create_sms_mail_logs_table',1,1),(95,'2024_02_28_085432_create_student_absent_notifications_table',1,1),(96,'2024_02_28_102602_create_system_notifications_table',1,1),(97,'2024_02_28_110330_create_jobs_table',1,1),(98,'2024_02_29_050637_create_notification_settings_table',1,1),(99,'2024_03_04_064053_create_pages_table',1,1),(100,'2024_03_06_123332_create_slider_translates_table',1,1),(101,'2024_03_07_074949_create_online_admission_settings_table',1,1),(102,'2024_03_07_141027_create_page_translates_table',1,1),(103,'2024_03_07_172038_create_section_translates_table',1,1),(104,'2024_03_08_113402_create_about_translates_table',1,1),(105,'2024_03_08_124638_create_counter_translates_table',1,1),(106,'2024_03_08_145357_create_contact_info_translates_table',1,1),(107,'2024_03_08_153350_create_department_contact_translates_table',1,1),(108,'2024_03_08_155742_create_news_translates_table',1,1),(109,'2024_03_08_163636_create_event_translates_table',1,1),(110,'2024_03_14_061235_create_online_admission_fees_assigns_table',1,1),(111,'2024_03_14_085756_create_online_admission_payments_table',1,1),(112,'2024_03_19_033526_create_gallery_category_translates_table',1,1),(113,'2024_03_19_094031_create_notice_board_translates_table',1,1),(114,'2024_03_19_104803_create_setting_translates_table',1,1),(115,'2024_03_28_070846_create_gender_translates_table',1,1),(116,'2024_03_28_075421_create_religon_translates_table',1,1),(117,'2024_04_01_035342_create_class_translates_table',1,1),(118,'2024_04_01_035412_create_class_section_translates_table',1,1),(119,'2024_04_01_061856_create_session_translates_table',1,1),(120,'2024_04_02_052447_create_shift_translates_table',1,1),(121,'2024_08_30_151926_add_columns_to_students_table',1,1),(122,'2024_08_30_152016_add_columns_to_parent_guardians_table',1,1),(123,'2024_09_03_121530_add_fields_to_users_table',1,1),(124,'2025_01_01_122002_add_branch_id_to_all_tables',1,1),(125,'2025_01_13_054622_add_new_columns_notice_board_table',1,1),(126,'2025_01_14_115157_add_manager_id_column_to_departments_table',1,1),(127,'2025_01_14_124219_add_department_id_column_to_student_table',1,1),(128,'2025_01_14_132943_add_department_id_column_to_notice_board_table',1,1),(129,'2025_01_16_131514_add_marksheet_published_column_to_marks_registers_table',1,1),(130,'2025_01_16_151441_add_health_status_family_rank_siblings_column_to_students_table',1,1),(131,'2025_01_16_160558_add_place_of_work_and_position_column_to_parent_guardians',1,1),(132,'2025_05_12_135041_create_sibling_fees_discounts_table',1,1),(133,'2025_05_14_115833_add_siblings_discount_to_students_table',1,1),(134,'2025_05_14_122836_create_assign_fees_discounts_table',1,1),(135,'2025_05_14_181851_create_early_payment_discounts_table',1,1),(136,'2025_05_22_114443_create_mark_sheet_approvals_table',1,1),(137,'2025_05_23_094821_create_leave_types_table',1,1),(138,'2025_05_23_094837_create_leave_requests_table',1,1),(139,'2025_05_23_113354_create_subject_attendances_table',1,1),(140,'2023_06_21_150627_create_messages_table',2,1),(141,'2024_10_17_070044_create_forum_posts_table',3,1),(142,'2024_10_17_072823_create_forum_post_comments_table',3,1),(143,'2024_10_21_054332_create_memories_table',3,1),(144,'2024_10_21_054342_create_memory_galleries_table',3,1),(145,'2024_12_31_160536_create_branches_table',4,1),(146,'2019_09_15_000010_create_tenants_table',5,1),(147,'2019_09_15_000020_create_domains_table',6,1),(148,'2023_08_10_083847_create_packages_table',7,1),(149,'2023_08_10_083848_create_schools_table',8,1),(150,'2023_08_10_091618_create_features_table',9,1),(154,'2025_09_04_050530_create_fees_generations_table',10,1),(155,'2025_09_04_050640_create_fees_generation_logs_table',11,1),(156,'2025_09_04_050752_add_generation_batch_id_to_fees_collects_table',12,1),(157,'2025_09_04_054823_add_fees_generation_permissions',13,1),(158,'2025_01_09_120000_enhance_fees_types_table',14,1),(159,'2025_01_09_121000_create_student_services_table',14,1),(160,'2025_01_09_122000_create_academic_level_configs_table',14,1),(161,'2025_01_09_125000_create_fee_system_migration_logs_table',14,1),(162,'2025_01_09_130000_migrate_existing_fee_data_to_service_structure',15,1),(163,'2025_01_11_100000_fix_academic_level_configurations',16,1),(164,'2025_01_11_110000_add_academic_level_to_classes_table',17,1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `news_upload_id_foreign` (`upload_id`),
  CONSTRAINT `news_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,'20+ Academic Curriculum We Done!0','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-02','2025-06-03',19,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'20+ Academic Curriculum We Done!1','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-01','2025-06-03',20,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'20+ Academic Curriculum We Done!2','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-31','2025-06-03',21,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'20+ Academic Curriculum We Done!3','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-30','2025-06-03',22,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'20+ Academic Curriculum We Done!4','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-29','2025-06-03',23,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'20+ Academic Curriculum We Done!5','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-28','2025-06-03',24,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'20+ Academic Curriculum We Done!6','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-27','2025-06-03',25,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'20+ Academic Curriculum We Done!7','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-26','2025-06-03',26,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'20+ Academic Curriculum We Done!8','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-25','2025-06-03',27,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'20+ Academic Curriculum We Done!9','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-24','2025-06-03',28,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'20+ Academic Curriculum We Done!10','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-23','2025-06-03',29,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'20+ Academic Curriculum We Done!11','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-22','2025-06-03',30,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,'20+ Academic Curriculum We Done!12','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-05-21','2025-06-03',31,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_translates`
--

DROP TABLE IF EXISTS `news_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `news_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `news_translates_news_id_foreign` (`news_id`),
  CONSTRAINT `news_translates_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_translates`
--

LOCK TABLES `news_translates` WRITE;
/*!40000 ALTER TABLE `news_translates` DISABLE KEYS */;
INSERT INTO `news_translates` VALUES (1,1,'en','20+ Academic Curriculum We Done!0','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','20+ Academic Curriculum We Done!1','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','20+ Academic Curriculum We Done!2','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,4,'en','20+ Academic Curriculum We Done!3','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,5,'en','20+ Academic Curriculum We Done!4','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,6,'en','20+ Academic Curriculum We Done!5','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,7,'en','20+ Academic Curriculum We Done!6','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,8,'en','20+ Academic Curriculum We Done!7','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,9,'en','20+ Academic Curriculum We Done!8','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,10,'en','20+ Academic Curriculum We Done!9','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,11,'en','20+ Academic Curriculum We Done!10','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,12,'en','20+ Academic Curriculum We Done!11','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,13,'en','20+ Academic Curriculum We Done!12','Onsest Schooled Is Home To More Than 20,000 Students And 230,000 Alumni With A Wide Variety Of Interests, Ages And Backgrounds, The University Reflects The City’s Dynamic Mix Of Populations.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,1,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!0','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,2,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!1','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,3,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!2','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,4,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!3','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,5,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!4','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,6,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!5','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,7,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!6','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,8,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!7','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,9,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!8','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,10,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!9','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,11,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!10','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(25,12,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!11','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(26,13,'bn','20+ একাডেমিক পাঠ্যক্রম আমরা সম্পন্ন করেছি!12','অনসেস্ট স্কুলে 20,000-এরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রের বাসস্থান, আগ্রহ, বয়স এবং পটভূমির বিস্তৃত বৈচিত্র্য সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `news_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notice_board_translates`
--

DROP TABLE IF EXISTS `notice_board_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notice_board_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notice_board_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `notice_board_translates_notice_board_id_foreign` (`notice_board_id`),
  CONSTRAINT `notice_board_translates_notice_board_id_foreign` FOREIGN KEY (`notice_board_id`) REFERENCES `notice_boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notice_board_translates`
--

LOCK TABLES `notice_board_translates` WRITE;
/*!40000 ALTER TABLE `notice_board_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `notice_board_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notice_boards`
--

DROP TABLE IF EXISTS `notice_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notice_boards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `publish_date` datetime NOT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` bigint unsigned DEFAULT NULL,
  `is_visible_web` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `visible_to` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `notice_boards_session_id_foreign` (`session_id`),
  KEY `notice_boards_class_id_foreign` (`class_id`),
  KEY `notice_boards_section_id_foreign` (`section_id`),
  KEY `notice_boards_student_id_foreign` (`student_id`),
  KEY `notice_boards_department_id_foreign` (`department_id`),
  KEY `notice_boards_attachment_foreign` (`attachment`),
  CONSTRAINT `notice_boards_attachment_foreign` FOREIGN KEY (`attachment`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notice_boards_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notice_boards_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `notice_boards_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notice_boards_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notice_boards_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notice_boards`
--

LOCK TABLES `notice_boards` WRITE;
/*!40000 ALTER TABLE `notice_boards` DISABLE KEYS */;
/*!40000 ALTER TABLE `notice_boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_settings`
--

DROP TABLE IF EXISTS `notification_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e=email, s=SMS, w=web, a=app',
  `reciever` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shortcode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_settings`
--

LOCK TABLES `notification_settings` WRITE;
/*!40000 ALTER TABLE `notification_settings` DISABLE KEYS */;
INSERT INTO `notification_settings` VALUES (1,'Student_Attendance','{\"email\":1,\"sms\":1,\"web\":1,\"app\":1}','{\"Student\":1,\"Parent\":1}','{\"Student\":\"Student Attendance\",\"Parent\":\"Student Attendance\"}','{\"Student\":{\"Email\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"SMS\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"Web\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"App\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\"},\"Parent\":{\"Email\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"SMS\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name].\",\"Web\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\",\"App\":\"Dear [parent_name],\\n                        Your child\'s attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\"}}','{\"Student\":\"[student_name], [class], [section], [admission_no], [roll_no], [attendance_type], [attendance_date], [school_name]\",\"Parent\":\"[guardian_name], [student_name], [class], [section], [admission_no], [roll_no], [attendance_type], [attendance_date], [school_name]\"}','2025-06-03 07:04:07','2025-06-03 07:04:07',1),(2,'Online_Admission','{\"email\":1,\"sms\":1,\"web\":1,\"app\":1}','{\"Super Admin\":1,\"Student\":1,\"Parent\":1}','{\"Super Admin\":\"Student Online Admission\",\"Student\":\"Student Online Admission\",\"Parent\":\"Student Online Admission\"}','{\"Super Admin\":{\"Email\":\"Dear Super Admin,\\n                         [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\",\"SMS\":\"Dear Super Admin,\\n                        [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\",\"Web\":\"Dear Super Admin,\\n                        [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\",\"App\":\"Dear Super Admin,\\n                        [student_name] admitted on class : [class] , section : [section] on [admission_date]. Thank You [school_name] .\"},\"Student\":{\"Email\":\"Dear [student_name],\\n                        You are admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [student_email] , Default Password : 123456 Thank You for choosing [school_name] .\",\"SMS\":\"Dear [student_name],\\n                        You are admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [student_email]  , Default Password : 123456 Thank You for choosing [school_name] .\",\"Web\":\"You are admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name].\",\"App\":\"Dear [student_name],\\n                        Your attendance was listed [attendance_type] on [attendance_date] for Class: [class], Section: [section]. Thank You [school_name] .\"},\"Parent\":{\"Email\":\"Dear [parent_name],\\n                        Your child [student_name] admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email]  , Default Password : 123456 Thank You for choosing [school_name]\",\"SMS\":\"Dear [parent_name],\\n                        Your child [student_name] admitted on class : [class] , section : [section] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email]  , Default Password : 123456 Thank You for choosing [school_name]\",\"Web\":\"Dear [parent_name],\\n                        Your child [student_name] admitted on class : [class_name] , section : [section_name] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email]  , Default Password : 123456 Thank You for choosing [school_name]\",\"App\":\" Your child [student_name] admitted on class : [class_name] , section : [section_name] , Admission No : [admission_no] on [school_name]. Login Username : [parent_email] , Default Password : 123456 Thank You for choosing [school_name]\"}}','{\"Super Admin\":\"[student_name], [class], [section], [admission_no], [admission_date], [school_name]\",\"Student\":\"[parent_name], [student_name], [class], [section], [admission_no], [student_email], [phone] , [school_name]\",\"Parent\":\"[parent_name], [student_name], [class], [section], [admission_no], [parent_email], [phone] , [school_name]\"}','2025-06-03 07:04:07','2025-06-03 07:04:07',1);
/*!40000 ALTER TABLE `notification_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_admission_fees_assigns`
--

DROP TABLE IF EXISTS `online_admission_fees_assigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_admission_fees_assigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fees_group_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `online_admission_fees_assigns_fees_group_id_foreign` (`fees_group_id`),
  KEY `online_admission_fees_assigns_session_id_foreign` (`session_id`),
  KEY `online_admission_fees_assigns_class_id_foreign` (`class_id`),
  KEY `online_admission_fees_assigns_section_id_foreign` (`section_id`),
  CONSTRAINT `online_admission_fees_assigns_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admission_fees_assigns_fees_group_id_foreign` FOREIGN KEY (`fees_group_id`) REFERENCES `fees_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admission_fees_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admission_fees_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_admission_fees_assigns`
--

LOCK TABLES `online_admission_fees_assigns` WRITE;
/*!40000 ALTER TABLE `online_admission_fees_assigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_admission_fees_assigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_admission_payments`
--

DROP TABLE IF EXISTS `online_admission_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_admission_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admission_id` bigint unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `payment_method` tinyint DEFAULT NULL,
  `fees_assign_id` bigint unsigned NOT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_admission_payments`
--

LOCK TABLES `online_admission_payments` WRITE;
/*!40000 ALTER TABLE `online_admission_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_admission_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_admission_settings`
--

DROP TABLE IF EXISTS `online_admission_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_admission_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'online_admission',
  `field` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '1',
  `is_required` tinyint(1) DEFAULT '0',
  `is_system_required` tinyint(1) DEFAULT '0',
  `field_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_admission_settings`
--

LOCK TABLES `online_admission_settings` WRITE;
/*!40000 ALTER TABLE `online_admission_settings` DISABLE KEYS */;
INSERT INTO `online_admission_settings` VALUES (1,'online_admission','student_first_name',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(2,'online_admission','student_last_name',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(3,'online_admission','student_phone',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(4,'online_admission','student_email',1,1,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(5,'online_admission','student_dob',1,1,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(6,'online_admission','student_document',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(7,'online_admission','student_photo',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(8,'online_admission','session',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(9,'online_admission','class',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(10,'online_admission','section',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(11,'online_admission','shift',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(12,'online_admission','gender',1,1,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(13,'online_admission','religion',1,1,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(14,'online_admission','previous_school',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(15,'online_admission','previous_school_info',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(16,'online_admission','previous_school_doc',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(17,'online_admission','admission_payment',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(18,'online_admission','admission_payment_info',1,0,0,'Enter Payment Information ,Bank Name . Swift Code, Account Number, Account Branch Information Or Any kind of special note you can wrote here ','2025-06-03 07:04:07','2025-06-03 07:04:07',1),(19,'online_admission','place_of_birth',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(20,'online_admission','nationality',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(21,'online_admission','cpr_no',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(22,'online_admission','spoken_lang_at_home',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(23,'online_admission','residance_address',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(24,'online_admission','father_nationality',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(25,'online_admission','gurdian_name',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(26,'online_admission','gurdian_email',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(27,'online_admission','gurdian_phone',1,1,1,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(28,'online_admission','gurdian_photo',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(29,'online_admission','gurdian_profession',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(30,'online_admission','father_name',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(31,'online_admission','father_phone',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(32,'online_admission','father_photo',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(33,'online_admission','father_profession',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(34,'online_admission','mother_name',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(35,'online_admission','mother_phone',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(36,'online_admission','mother_photo',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1),(37,'online_admission','mother_profession',1,0,0,NULL,'2025-06-03 07:04:07','2025-06-03 07:04:07',1);
/*!40000 ALTER TABLE `online_admission_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_admissions`
--

DROP TABLE IF EXISTS `online_admissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_admissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` tinyint NOT NULL DEFAULT '0' COMMENT '0 = no_need, 2 = need, 1 = done',
  `payslip_image_id` bigint unsigned DEFAULT NULL,
  `fees_assign_id` bigint unsigned DEFAULT NULL,
  `shift_id` bigint unsigned DEFAULT NULL,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `religion_id` bigint unsigned DEFAULT NULL,
  `gender_id` bigint unsigned DEFAULT NULL,
  `dob` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_image_id` bigint unsigned DEFAULT NULL,
  `previous_school` tinyint NOT NULL DEFAULT '0',
  `previous_school_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `previous_school_image_id` bigint unsigned DEFAULT NULL,
  `guardian_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gurdian_image_id` bigint unsigned DEFAULT NULL,
  `father_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_image_id` bigint unsigned DEFAULT NULL,
  `mother_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_image_id` bigint unsigned DEFAULT NULL,
  `upload_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `place_of_birth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpr_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spoken_lang_at_home` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residance_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `online_admissions_payslip_image_id_foreign` (`payslip_image_id`),
  KEY `online_admissions_shift_id_foreign` (`shift_id`),
  KEY `online_admissions_session_id_foreign` (`session_id`),
  KEY `online_admissions_classes_id_foreign` (`classes_id`),
  KEY `online_admissions_section_id_foreign` (`section_id`),
  KEY `online_admissions_religion_id_foreign` (`religion_id`),
  KEY `online_admissions_gender_id_foreign` (`gender_id`),
  KEY `online_admissions_student_image_id_foreign` (`student_image_id`),
  KEY `online_admissions_previous_school_image_id_foreign` (`previous_school_image_id`),
  KEY `online_admissions_gurdian_image_id_foreign` (`gurdian_image_id`),
  KEY `online_admissions_father_image_id_foreign` (`father_image_id`),
  KEY `online_admissions_mother_image_id_foreign` (`mother_image_id`),
  CONSTRAINT `online_admissions_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_father_image_id_foreign` FOREIGN KEY (`father_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_gurdian_image_id_foreign` FOREIGN KEY (`gurdian_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_mother_image_id_foreign` FOREIGN KEY (`mother_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_payslip_image_id_foreign` FOREIGN KEY (`payslip_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_previous_school_image_id_foreign` FOREIGN KEY (`previous_school_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_admissions_student_image_id_foreign` FOREIGN KEY (`student_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_admissions`
--

LOCK TABLES `online_admissions` WRITE;
/*!40000 ALTER TABLE `online_admissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_admissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exam_children_questions`
--

DROP TABLE IF EXISTS `online_exam_children_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exam_children_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_id` bigint unsigned NOT NULL,
  `question_bank_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `online_exam_children_questions_online_exam_id_foreign` (`online_exam_id`),
  KEY `online_exam_children_questions_question_bank_id_foreign` (`question_bank_id`),
  CONSTRAINT `online_exam_children_questions_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exam_children_questions_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_children_questions`
--

LOCK TABLES `online_exam_children_questions` WRITE;
/*!40000 ALTER TABLE `online_exam_children_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_exam_children_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exam_children_students`
--

DROP TABLE IF EXISTS `online_exam_children_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exam_children_students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `online_exam_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `online_exam_children_students_online_exam_id_foreign` (`online_exam_id`),
  KEY `online_exam_children_students_student_id_foreign` (`student_id`),
  CONSTRAINT `online_exam_children_students_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exam_children_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exam_children_students`
--

LOCK TABLES `online_exam_children_students` WRITE;
/*!40000 ALTER TABLE `online_exam_children_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_exam_children_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_exams`
--

DROP TABLE IF EXISTS `online_exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_exams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_type_id` bigint unsigned DEFAULT NULL,
  `total_mark` double DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `published` datetime DEFAULT NULL,
  `question_group_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `online_exams_session_id_foreign` (`session_id`),
  KEY `online_exams_classes_id_foreign` (`classes_id`),
  KEY `online_exams_section_id_foreign` (`section_id`),
  KEY `online_exams_subject_id_foreign` (`subject_id`),
  KEY `online_exams_exam_type_id_foreign` (`exam_type_id`),
  KEY `online_exams_question_group_id_foreign` (`question_group_id`),
  CONSTRAINT `online_exams_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exams_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exams_question_group_id_foreign` FOREIGN KEY (`question_group_id`) REFERENCES `question_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exams_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exams_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_exams`
--

LOCK TABLES `online_exams` WRITE;
/*!40000 ALTER TABLE `online_exams` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_type` enum('prepaid','postpaid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prepaid',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `per_student_price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `student_limit` int DEFAULT NULL,
  `staff_limit` int DEFAULT NULL,
  `duration` tinyint DEFAULT NULL,
  `duration_number` int DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `popular` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_sections`
--

DROP TABLE IF EXISTS `page_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `upload_id` bigint unsigned DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_sections_upload_id_foreign` (`upload_id`),
  CONSTRAINT `page_sections_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_sections`
--

LOCK TABLES `page_sections` WRITE;
/*!40000 ALTER TABLE `page_sections` DISABLE KEYS */;
INSERT INTO `page_sections` VALUES (1,'social_links','','',NULL,'[{\"name\":\"Facebook\",\"icon\":\"fab fa-facebook-f\",\"link\":\"http:\\/\\/www.facebook.com\"},{\"name\":\"Twitter\",\"icon\":\"fab fa-twitter\",\"link\":\"http:\\/\\/www.twitter.com\"},{\"name\":\"Pinterest\",\"icon\":\"fab fa-pinterest-p\",\"link\":\"http:\\/\\/www.pinterest.com\"},{\"name\":\"Instagram\",\"icon\":\"fab fa-instagram\",\"link\":\"http:\\/\\/www.instagram.com\"}]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'statement','Statement Of Onest Schooleded','',5,'[{\"title\":\"Mission Statement\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Read More\"},{\"title\":\"Vision Statement\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet Read More\"}]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'study_at','Study at Onest Schooleded','Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet',6,'[{\"icon\":8,\"title\":\"Out Prospects\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\"},{\"icon\":9,\"title\":\"Out Prospects\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\"},{\"icon\":10,\"title\":\"Out Prospects\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\"}]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'explore','Explore Onest Schoooled','\"We Educate Knowledge & Essential Skills\" is a phrase that emphasizes the importance of both theoretical knowledge',7,'[{\"tab\":\"Campus Life\",\"title\":\"Campus Life\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"},{\"tab\":\"Academic\",\"title\":\"Academic\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"},{\"tab\":\"Athletics\",\"title\":\"Athletics\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"},{\"tab\":\"School\",\"title\":\"School\",\"description\":\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\"}]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'why_choose_us','Excellence In Teaching And Learning','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will frequently occurs that pleasures. Provide Endless Opportunities',NULL,'[\"A higher education qualification\",\"Better career prospects\",\"Better career prospects\",\"Better career prospects\"]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'academic_curriculum','20+ Academic Curriculum','Onsest Schooled is home to more than 20,000 students and 230,000 alumni with a wide variety of interests, ages and backgrounds, the University reflects the city’s dynamic mix of populations.',NULL,'[\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\",\"Bangal Medium\"]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'coming_up','What’s Coming Up?','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.',NULL,'[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'news','Latest From Our Blog','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.',NULL,'[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'our_gallery','Our Gallery','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.',NULL,'[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'contact_information','Find Our <br> Contact Information','',NULL,'[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'department_contact_information','Contact By Department','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will',NULL,'[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'our_teachers','Our Featured Teachers','',NULL,'[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `page_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_translates`
--

DROP TABLE IF EXISTS `page_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_translates_page_id_foreign` (`page_id`),
  CONSTRAINT `page_translates_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_translates`
--

LOCK TABLES `page_translates` WRITE;
/*!40000 ALTER TABLE `page_translates` DISABLE KEYS */;
INSERT INTO `page_translates` VALUES (1,1,'en','Privacy Policy','<div class=\"elementor-element elementor-element-790b948d elementor-widget elementor-widget-text-editor\" data-id=\"790b948d\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: relative; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; color: var( --e-global-color-text ); font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; width: 1280px; margin-bottom: 0px; z-index: 3;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); padding: 0px 0px 100px;\"><h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Last updated: 22 November, 2025</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Introduction</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Onest Schooled Management System values your privacy. This policy explains how we collect, use, and safeguard your data when you use our app.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Information We Collect</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">User Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Names, email addresses, contact details, roles (student, teacher, parent, or admin).</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Operational Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Attendance, grades, homework, library usage, and fee transactions.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Device Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Device type, operating system, and logs for app functionality.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. How We Use Your Information</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To facilitate administrative operations, such as admission, fee collection, attendance, and academic management.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To provide personalized dashboards for students, teachers, and parents.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To enhance user experience and app security.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Sharing Your Information</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We only share data with:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">School administrators for operational purposes.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Authorities when required by law.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Data Security</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We implement industry-standard security measures, including encryption and regular audits.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">5. Your Rights</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Access or modify personal data through the user portal.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Request deletion of your data by contacting support (subject to operational constraints).</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">6. Policy Updates</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We may update this policy. Changes will be notified via email or app alerts.</span></p><p style=\"margin-bottom: 0.9rem;\"><span id=\"docs-internal-guid-0452a37e-7fff-0696-3df4-c342ecd0bf24\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Contact Us</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">For questions or concerns, reach us at sales.onesttech.com</span></p></div></div><div class=\"elementor-element elementor-element-4f36e2b7 elementor-widget__width-initial elementor-absolute elementor-widget elementor-widget-spacer\" data-id=\"4f36e2b7\" data-element_type=\"widget\" data-settings=\"{&quot;_position&quot;:&quot;absolute&quot;}\" data-widget_type=\"spacer.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: absolute; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; z-index: 1; width: var( --container-widget-width, 100vw ); margin-bottom: 0px; max-width: 100vw; --spacer-size: 100vh; --container-widget-width: 100vw; --container-widget-flex-grow: 0; top: -0.5px; right: 551px; color: rgb(51, 51, 51); font-family: -apple-system, &quot;system-ui&quot;, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); background-color: transparent; background-image: radial-gradient(rgba(44, 255, 0, 0.06) 0%, rgba(0, 0, 0, 0) 70%);\"><div class=\"elementor-spacer\"></div></div></div>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(2,2,'en','Support Policy','<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Scope of Support</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We provide assistance for:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Setup and configuration.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Troubleshooting technical issues.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Feature-related queries.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. Support Channels</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Email: [sales.onesttech.com]</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Whatsapp: [+880 1959-335555]</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Response Time</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">General Queries: Response within 48 business hours.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Critical Issues (e.g., service downtime): Response within 24 hours.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Updates &amp; Maintenance</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Regular updates are provided for feature enhancements and bug fixes. Notification will be sent before scheduled maintenance.</span></p><p><span id=\"docs-internal-guid-500ba9c7-7fff-1baf-821f-835a1517b432\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Let me know if you’d like to further customize these policies or add more details!</span></p>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(3,3,'en','Terms & Conditions','<p><b>Terms and Conditions of Use for Ischool Management System Management Software\n                        </b></p><p><b><br></b>\n                                                    These Terms and Conditions govern your access to and use of the School Management Software , provided by Ischool Management System . By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.\n                        </p><p><br></p><p><b>\n                                                    Acceptance of Terms: </b>By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to all the terms and conditions of this agreement, you must not use the Software.</p><p><br></p><p>\n                        </p><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Use of the Software:</b><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software is provided solely for the purpose of managing educational institutions, including but not limited to schools, colleges, and universities. You agree not to use the Software for any illegal or unauthorized purpose.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><b>User Accounts: </b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">You may need to create an account to access certain features of the Software. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n\n                                                    Privacy:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We are committed to protecting your privacy. Our Privacy Policy outlines how we collect, use, and disclose your personal information. By using the Software, you consent to the collection, use, and disclosure of your personal information as described in the Privacy Policy.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Intellectual Property:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software and its original content, features, and functionality are owned by Ischool Management System and are protected by international copyright, trademark, patent, trade secret, and other intellectual property or proprietary rights laws.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Limitation of Liability:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> In no event shall\n                                                    Ischool Management System be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or goodwill, arising from the use of or inability to use the Software.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>Changes to Terms:</b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Governing Law: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">These Terms shall be governed by and construed in accordance with the laws of United Stated Of America , without regard to its conflict of law provisions.\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Contact Us: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">If you have any questions about these Terms, please contact us at&nbsp; Ones .\n\n                                                    By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.</span><br></p>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(4,4,'en','Our Missions','<p>At Ischool Management System , we are dedicated to providing a nurturing and enriching educational environment that empowers students to reach their full potential. Our mission is to foster academic excellence, character development, and lifelong learning in every student we serve.</p><p><br></p><h3><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Our Core Values</b></h3><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><br></b><br></p><p><b>\n                        1. Excellence:\n                        </b> We are committed to excellence in all aspects of education, striving to provide the highest quality teaching, resources, and support to our students.\n                            </p><p><br></p><p><b>\n                        2. Integrity:\n                        </b> We uphold the highest standards of integrity, honesty, and ethical behavior in our interactions with students, parents, staff, and the community.\n                            </p><p><br></p><p><b>\n                        3. Respect:</b>\n                        We foster a culture of respect, valuing the unique abilities, perspectives, and backgrounds of each individual within our school community.\n                            </p><p><br></p><p><b>\n                        4. Collaboration:\n                            </b>  We believe in the power of collaboration and teamwork, working closely with students, parents, educators, and the community to achieve our shared goals.\n                        </p><p><br></p><p><b>\n                        5. Innovation:</b>\n                        We embrace innovation and creativity, continuously seeking new and effective ways to enhance the learning experience and meet the evolving needs of our students.</p><p><br></p><p>\n                            </p><p style=\"text-align: center;\"><b><u>\n                        Our Goals</u></b></p><p style=\"text-align: center;\"><b><u><br></u></b></p><p style=\"text-align: center;\"><b><u>\n                        </u></b></p><ul><li><b>                            1. Academic Excellence:\n                        </b>  We strive to provide rigorous academic programs that challenge and inspire students to achieve their highest academic potential.</li></ul><p><br></p><ul><li><b>\n                        2. Character Development:</b>\n                        We are committed to fostering the development of strong character traits such as honesty, responsibility, compassion, and resilience in our students.</li></ul><p><br></p><ul><li><b>\n                        3. Lifelong Learning:\n                        </b> We aim to instill a love of learning and a growth mindset in our students, empowering them to become lifelong learners who are curious, adaptable, and eager to explore new ideas and opportunities.</li></ul><p><br></p><ul><li><b>\n                        4 Community Engagement:\n                        </b> We seek to actively engage with parents, families, and the broader community to create a supportive and inclusive learning environment that nurtures the holistic development of our students.\n\n                        Join Us in Our Mission\n                        We invite you to join us in our mission to inspire and empower the next generation of leaders, thinkers, and innovators. </li></ul><p><br></p><p>Together, we can make a difference in the lives of our students and create a brighter future for all.\n\n                        This sample content provides an overview of the schools mission, core values, goals, and an invitation for stakeholders to join in achieving those goals. You can customize it further to align with the specific mission and values of your school management application.</p>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(5,1,'bn','গোপনীয়তা নীতি','<div class=\"elementor-element elementor-element-790b948d elementor-widget elementor-widget-text-editor\" data-id=\"790b948d\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\" শৈলী =\"--ফ্লেক্স-নির্দেশ: প্রাথমিক; --ফ্লেক্স-র্যাপ: প্রাথমিক; --জাস্টিফাই-সামগ্রী: প্রাথমিক; --অ্যালাইন-আইটেম: প্রাথমিক; --অ্যালাইন-সামগ্রী: প্রাথমিক; --গ্যাপ: প্রাথমিক; -- ফ্লেক্স-বেসিস: প্রাথমিক; --ফ্লেক্স-গ্রো: প্রাথমিক; --ফ্লেক্স-সঙ্কুচিত: প্রাথমিক; --অর্ডার: প্রাথমিক; --অ্যালাইন-স্ব: প্রাথমিক; ফ্লেক্স-বেসিস: var(--ফ্লেক্স-বেসিস); ফ্লেক্স -গ্রো: var(--ফ্লেক্স-গ্রো); ফ্লেক্স-সঙ্কুচিত: var(--ফ্লেক্স-সঙ্কুচিত); অর্ডার: var(--অর্ডার); align-self: var(--align-self); flex-direction : var(--ফ্লেক্স-ডাইরেকশন); ফ্লেক্স-র্যাপ: var(--ফ্লেক্স-র্যাপ); ন্যায্যতা-সামগ্রী: var(--জাস্টিফাই-কন্টেন্ট); সারিবদ্ধ-আইটেম: var(--সারিবদ্ধ-আইটেম); সারিবদ্ধ -সামগ্রী: var(--align-content); gap: var(--gap); অবস্থান: আপেক্ষিক; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper- পেজিনেশন-বুলেট-আকার: 6px; --swiper-পৃষ্ঠা-পৃষ্ঠা-বুলেট-অনুভূমিক-ব্যবধান: 6px; --উইজেট-স্পেসিং: 20px; রঙ: var( --e-global-color-text); ফন্ট-পরিবার: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; প্রস্থ: 1280px; মার্জিন-নিচ: 0px; z-index: 3;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e -রূপান্তর-পরিবর্তন-সময়কাল,.4s); প্যাডিং: 0px 0px 100px;\"><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">এই গোপনীয়তা নীতি নথিতে এমন ধরনের তথ্য রয়েছে যা সংগৃহীত এবং রেকর্ড করা হয় Ischool Management System এবং আমরা কিভাবে এটি ব্যবহার করি। এ Ischool Management System, থেকে অ্যাক্সেসযোগ্যhttp://127.0.0.1:8000 , আমাদের প্রধান অগ্রাধিকারগুলির মধ্যে একটি হল আমাদের দর্শকদের গোপনীয়তা। এই গোপনীয়তা নীতি নথিতে তথ্যের প্রকার রয়েছে যা দ্বারা সংগৃহীত এবং রেকর্ড করা হয় Ischool Management System&nbsp;এবং আমরা এটি কীভাবে ব্যবহার করি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আপনার যদি অতিরিক্ত প্রশ্ন থাকে বা আমাদের গোপনীয়তা নীতি সম্পর্কে আরও তথ্যের প্রয়োজন হয়, তাহলে আমাদের সাথে যোগাযোগ করতে দ্বিধা করবেন না।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">এই গোপনীয়তা নীতি শুধুমাত্র আমাদের অনলাইন ক্রিয়াকলাপের ক্ষেত্রে প্রযোজ্য এবং আমাদের ওয়েবসাইটের দর্শকদের জন্য তারা যে তথ্য শেয়ার করেছেন এবং/অথবা সংগ্রহ করেছেন তাদের জন্য বৈধ Ischool Management System. এই নীতিটি এই ওয়েবসাইট ছাড়া অফলাইনে বা চ্যানেলের মাধ্যমে সংগ্রহ করা কোনো তথ্যের ক্ষেত্রে প্রযোজ্য নয়।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit; \"><span data-preserver-spaces=\"true\">সম্মতি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">এর দ্বারা আমাদের ওয়েবসাইট ব্যবহার করে, আপনি এতদ্বারা আমাদের গোপনীয়তা নীতিতে সম্মত হন এবং এর শর্তাবলীতে সম্মত হন।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\" <span data-preserver-spaces=\"true\">আমরা যে তথ্য সংগ্রহ করি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\"> আপনাকে যে ব্যক্তিগত তথ্য প্রদান করতে বলা হয়েছে এবং কেন আপনাকে এটি প্রদান করতে বলা হয়েছে, আমরা যখন আপনাকে আপনার ব্যক্তিগত তথ্য প্রদান করতে বলব তখনই আপনাকে স্পষ্ট করে দেওয়া হবে।</span></p><p style =\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আপনি যদি আমাদের সাথে সরাসরি যোগাযোগ করেন, তাহলে আমরা আপনার সম্পর্কে অতিরিক্ত তথ্য পেতে পারি যেমন আপনার নাম, ইমেল ঠিকানা, ফোন নম্বর, এর বিষয়বস্তু আপনি যে বার্তা এবং/অথবা সংযুক্তিগুলি আমাদের পাঠাতে পারেন এবং অন্য যেকোন তথ্য প্রদান করতে পারেন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces \"সত্য \"মার্জিন-টপ: 0.5rem; মার্জিন-নিচ: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">আমরা কীভাবে আপনার তথ্য ব্যবহার করি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span ডেটা -preserver-spaces=\"true\">আমরা বিভিন্ন উপায়ে সংগ্রহ করা তথ্য ব্যবহার করি, যার মধ্যে রয়েছে:</span></p><ul style=\"margin-bottom: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; ব্যাকগ্রাউন্ড: স্বচ্ছ;\"><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আমাদের ওয়েবসাইট প্রদান, পরিচালনা এবং রক্ষণাবেক্ষণ করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আমাদের ওয়েবসাইট উন্নত করুন, ব্যক্তিগতকৃত করুন এবং প্রসারিত করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আপনি আমাদের ওয়েবসাইট কীভাবে ব্যবহার করেন তা বুঝুন এবং বিশ্লেষণ করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">নতুন পণ্য, পরিষেবা, বৈশিষ্ট্য এবং কার্যকারিতা বিকাশ করুন</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আপনার সাথে যোগাযোগ করুন, সরাসরি বা আমাদের অংশীদারদের একজনের মাধ্যমে, গ্রাহক পরিষেবা সহ, আপনাকে ওয়েবসাইট সম্পর্কিত আপডেট এবং অন্যান্য তথ্য প্রদান করতে, এবং বিপণন এবং প্রচারমূলক উদ্দেশ্যে</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">আপনাকে ইমেল পাঠান</span></li><li style=\"margin-top: 0px; মার্জিন-নিচ: 0px; সীমানা: 0px; outline-style: প্রাথমিক; রূপরেখা-প্রস্থ: 0px; উল্লম্ব-সারিবদ্ধ: বেসলাইন; পটভূমি: স্বচ্ছ;\"><span data-preserver-spaces=\"true\">জালিয়াতি খুঁজুন এবং প্রতিরোধ করুন</span></li></ul><h3 style=\"margin-top: 0.5rem; মার্জিন-নিচ: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">লগ ফাইল</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver- spaces=\"true\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-সাইজ: var(--BS-বডি-ফন্ট-সাইজ); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); text-align: var(--bs-body-text-align);\"> Ischool Management System</span>&nbsp;লগ ফাইল ব্যবহার করার একটি আদর্শ পদ্ধতি অনুসরণ করে৷ এই ফাইল ভিজিটর লগ লগ যখন তারা ওয়েবসাইট পরিদর্শন. সমস্ত হোস্টিং কোম্পানি এটি করে এবং হোস্টিং পরিষেবার বিশ্লেষণের একটি অংশ। লগ ফাইলের মাধ্যমে সংগৃহীত তথ্যের মধ্যে রয়েছে ইন্টারনেট প্রোটোকল (IP) ঠিকানা, ব্রাউজারের ধরন, ইন্টারনেট পরিষেবা প্রদানকারী (ISP), তারিখ এবং সময় স্ট্যাম্প, উল্লেখ/প্রস্থান পৃষ্ঠা এবং সম্ভবত ক্লিকের সংখ্যা। এগুলো কোনো ব্যক্তিগতভাবে শনাক্তযোগ্য তথ্যের সাথে যুক্ত নয়। তথ্যের উদ্দেশ্য হল প্রবণতা বিশ্লেষণ করা, সাইট পরিচালনা করা, ওয়েবসাইটে ব্যবহারকারীদের গতিবিধি ট্র্যাক করা এবং জনসংখ্যা সংক্রান্ত তথ্য সংগ্রহ করা।</span></p><p style=\"margin-bottom: 0.9rem;\">&nbsp;</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span ডেটা -preserver-spaces=\"true\">কুকিজ এবং ওয়েব বীকন</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">অন্য যেকোনও মত ওয়েবসাইট, Ischool Management System &nbsp;কুকিজ ব্যবহার করে।. এই কুকিগুলি ভিজিটরদের পছন্দ, এবং ভিজিটর অ্যাক্সেস বা ভিজিট করা ওয়েবসাইটের পৃষ্ঠাগুলি সহ তথ্য সংরক্ষণ করতে ব্যবহার করা হয়। ভিজিটরদের ব্রাউজারের ধরন এবং/অথবা অন্যান্য তথ্যের উপর ভিত্তি করে আমাদের ওয়েব পৃষ্ঠার বিষয়বস্তু কাস্টমাইজ করে ব্যবহারকারীদের অভিজ্ঞতা অপ্টিমাইজ করতে তথ্য ব্যবহার করা হয়।</span></p><h3 style=\"margin-top: 0.5rem; margin- নীচে: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">বিজ্ঞাপন অংশীদারদের গোপনীয়তা নীতি</span></h3><p style=\"margin-bottom: 0.9rem;\"> <span data-preserver-spaces=\"true\">এর প্রতিটি বিজ্ঞাপন অংশীদারের জন্য গোপনীয়তা নীতি খুঁজে পেতে আপনি এই তালিকাটি দেখতে পারেন Ischool Management System .</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">তৃতীয় পক্ষের বিজ্ঞাপন সার্ভার বা বিজ্ঞাপন নেটওয়ার্ক কুকিজ, জাভাস্ক্রিপ্ট, এর মতো প্রযুক্তি ব্যবহার করে অথবা ওয়েব বীকন যা তাদের নিজ নিজ বিজ্ঞাপনে ব্যবহৃত হয় এবং লিঙ্কে প্রদর্শিত হয় Ischool Management System , যা সরাসরি ব্যবহারকারীদের ব্রাউজারে পাঠানো হয়। যখন এটি ঘটে তখন তারা স্বয়ংক্রিয়ভাবে আপনার আইপি ঠিকানা গ্রহণ করে। এই প্রযুক্তিগুলি তাদের বিজ্ঞাপন প্রচারাভিযানের কার্যকারিতা পরিমাপ করতে এবং/অথবা আপনি যে ওয়েবসাইটগুলিতে যান সেই বিজ্ঞাপন সামগ্রীগুলিকে ব্যক্তিগতকৃত করতে ব্যবহার করা হয়৷</span></p><p style=\"margin-bottom: 0.9rem;\" ><span data-preserver-spaces=\"true\">মনে রাখবেন যে Ischool Management System &nbsp;থার্ড-পার্টি বিজ্ঞাপনদাতাদের দ্বারা ব্যবহৃত এই কুকিগুলিতে কোনও অ্যাক্সেস বা নিয়ন্ত্রণ নেই৷</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">তৃতীয় পক্ষের গোপনীয়তা নীতি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces =\"true\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> Ischool Management System </span>&nbsp;গোপনীয়তা নীতি অন্যান্য বিজ্ঞাপনদাতা বা ওয়েবসাইটে প্রযোজ্য নয়৷ সুতরাং, আমরা আপনাকে আরও বিস্তারিত তথ্যের জন্য এই তৃতীয় পক্ষের বিজ্ঞাপন সার্ভারগুলির সংশ্লিষ্ট গোপনীয়তা নীতিগুলির সাথে পরামর্শ করার পরামর্শ দিচ্ছি। এটিতে তাদের অনুশীলন এবং নির্দিষ্ট বিকল্পগুলি কীভাবে অপ্ট আউট করতে হয় সে সম্পর্কে নির্দেশাবলী অন্তর্ভুক্ত থাকতে পারে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\"> আপনি আপনার ব্রাউজার বিকল্পগুলির মাধ্যমে কুকিজ নিষ্ক্রিয় করতে বেছে নিতে পারেন। নির্দিষ্ট ওয়েব ব্রাউজারগুলির সাথে কুকি পরিচালনা সম্পর্কে আরও বিশদ তথ্য জানতে, এটি ব্রাউজারের নিজ নিজ ওয়েবসাইটে পাওয়া যেতে পারে৷</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">পেমেন্ট তথ্য গোপনীয়তা নীতি</span></h3><p style=\"margin-bottom: 0.9rem;\"><span style= \"ফন্ট-ফ্যামিলি: var( --e-global-typography-text-font-family), &quot;Roboto&quot;, Sans-serif; ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; হরফ-আকার: var(--BS-বডি-ফন্ট -আকার); font-weight: var(-bs-body-font-weight); text-align: var(--bs-body-text-align);\">Ischool Management System</span>&nbsp;তদনুসারে আপনার সমস্ত গোপনীয় তথ্য রক্ষা করার গুরুত্বকে দৃঢ়ভাবে স্বীকার করে৷ Ischool Management System&nbsp;এর ওয়েবসাইটে সংগৃহীত ব্যবহারকারীর তথ্যের ভাল সুরক্ষা বজায় রাখে৷Ischool Management System . Ischool Management System&nbsp;ক্লায়েন্টদের ব্যক্তিগত তথ্য কখনও অন্য কোনও বহিরাগতের সাথে শেয়ার করে না। এই গোপনীয়তা নীতি Eduman-এর বর্তমান এবং প্রাক্তন উভয় ক্লায়েন্টের জন্য প্রযোজ্য। গোপনীয়তা নীতির সাথে একমত হওয়ার পরে, আপনি একজন ক্লায়েন্ট হিসাবে আমাদের সাইটে অ্যাক্সেস পাবেন। অন্যথায়, আপনি আমাদের ওয়েবসাইট ব্রাউজার হওয়ার জন্য উপযুক্ত নন। আমরা লগ ফাইলগুলিও বজায় রাখি এবং ফাইলগুলি আপডেট করি। আমাদের সমস্ত কার্যকলাপ সম্পূর্ণরূপে সুরক্ষিত যা কখনও বাইরের তৃতীয় ব্যক্তির সাথে ভাগ করে নেওয়া এবং ঘোষণা করা হবে না। এই গোপনীয়তা নীতিটিকে আরও শক্তিশালী করার জন্য পরিবর্তনযোগ্য তবে Eduman সতর্ক থাকে যাতে এটি কারও ক্ষতি না করে।</p><p style=\"margin-bottom: 0.9rem;\">&nbsp;</p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">রিফান্ড নীতিমালা</span></h3><p style=\"margin-bottom: 0.9rem;\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">Ischool Management System</span>&nbsp;কোনও লেনদেনের জন্য কোনও ফেরত বা চার্জব্যাক গ্রহণ করা হবে না।</p><p style=\"margin-bottom: 0.9rem;\">কিন্তু যদি কোনও লেনদেন নিয়ে কোনও বিরোধ দেখা দেয় তবে আমরা যথাযথ বৈধতা এবং লেনদেনের প্রমাণ সহ লেনদেনের স্থিতি আপডেট করব যার জন্য ১৪-২১ কার্যদিবসের প্রয়োজন।&nbsp;</p><p style=\"margin-bottom: 0.9rem;\">&nbsp;</p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">CCPA গোপনীয়তা অধিকার (আমার ব্যক্তিগত তথ্য বিক্রি করবেন না)</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">CCPA-এর অধীনে, অন্যান্য অধিকারের মধ্যে, ক্যালিফোর্নিয়ার ভোক্তাদের অধিকার রয়েছে:</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">গ্রাহকের তথ্য সংগ্রহকারী একটি ব্যবসাকে গ্রাহকদের সম্পর্কে সংগৃহীত বিভাগ এবং নির্দিষ্ট ব্যক্তিগত তথ্য প্রকাশ করার জন্য অনুরোধ করুন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">কোনও ব্যবসা প্রতিষ্ঠানকে অনুরোধ করুন যে তারা গ্রাহকের সম্পর্কে যে কোনও ব্যক্তিগত তথ্য সংগ্রহ করেছে তা মুছে ফেলুক।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">যে ব্যবসা প্রতিষ্ঠান গ্রাহকের তথ্য বিক্রি করে, তাদের যেন গ্রাহকের তথ্য বিক্রি না করা হয়, সেই প্রতিষ্ঠানকে অনুরোধ করুন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">যদি আপনি কোন অনুরোধ করেন, তাহলে আপনার সাড়া দেওয়ার জন্য আমাদের কাছে এক মাস সময় আছে। আপনি যদি এই অধিকারগুলির কোনটি প্রয়োগ করতে চান, তাহলে অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">জিডিপিআর ডেটা সুরক্ষা অধিকার</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আমরা নিশ্চিত করতে চাই যে আপনি আপনার সমস্ত ডেটা সুরক্ষা অধিকার সম্পর্কে সম্পূর্ণরূপে সচেতন। প্রতিটি ব্যবহারকারীর নিম্নলিখিত অধিকার রয়েছে:</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">অ্যাক্সেসের অধিকার - আপনার তথ্যের কপি অনুরোধ করার অধিকার আপনার আছে। এই পরিষেবার জন্য আমরা আপনার কাছ থেকে সামান্য ফি নিতে পারি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">সংশোধনের অধিকার - আপনার কাছে এমন কোনও তথ্য সংশোধন করার অনুরোধ করার অধিকার আছে যা আপনি ভুল বলে মনে করেন। আপনার কাছে এমন তথ্য সম্পূর্ণ করার অনুরোধ করার অধিকারও আছে যা আপনি অসম্পূর্ণ বলে মনে করেন।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">মুছে ফেলার অধিকার - কিছু শর্তের অধীনে, আপনার ডেটা মুছে ফেলার অনুরোধ করার অধিকার আপনার আছে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">প্রক্রিয়াকরণ সীমাবদ্ধ করার অধিকার - আপনার কাছে কিছু শর্তের অধীনে আপনার ডেটা প্রক্রিয়াকরণ সীমাবদ্ধ করার অনুরোধ করার অধিকার রয়েছে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">প্রক্রিয়াকরণের বিরুদ্ধে আপত্তি জানানোর অধিকার – কিছু শর্তের অধীনে, আপনার ডেটা প্রক্রিয়াকরণের বিরুদ্ধে আপত্তি জানানোর অধিকার আপনার আছে।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">ডেটা পোর্টেবিলিটির অধিকার – আপনার কাছে অনুরোধ করার অধিকার আছে যে আমরা যে ডেটা সংগ্রহ করেছি তা অন্য কোনও সংস্থায়, অথবা সরাসরি আপনার কাছে, কিছু শর্তের অধীনে স্থানান্তর করি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">যদি আপনি কোন অনুরোধ করেন, তাহলে আপনার সাড়া দেওয়ার জন্য আমাদের কাছে এক মাস সময় আছে। আপনি যদি এই অধিকারগুলির কোনটি প্রয়োগ করতে চান, তাহলে অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন।</span></p><h3 style=\"margin-top: 0.5rem; margin-bottom: 1rem; font-family: inherit;\"><span data-preserver-spaces=\"true\">শিশুদের তথ্য</span></h3><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\">আমাদের অগ্রাধিকারের আরেকটি অংশ হল ইন্টারনেট ব্যবহারের সময় শিশুদের সুরক্ষা প্রদান করা। আমরা বাবা-মা এবং অভিভাবকদের তাদের অনলাইন কার্যকলাপ পর্যবেক্ষণ, অংশগ্রহণ এবং/অথবা পর্যবেক্ষণ এবং নির্দেশনা দেওয়ার জন্য উৎসাহিত করি।</span></p><p style=\"margin-bottom: 0.9rem;\"><span data-preserver-spaces=\"true\"><span style=\"font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> Ischool Management System</span>&nbsp; ১৩ বছরের কম বয়সী শিশুদের কাছ থেকে জেনেশুনে কোনও ব্যক্তিগত শনাক্তযোগ্য তথ্য সংগ্রহ করে না। যদি আপনি মনে করেন যে আপনার সন্তান আমাদের ওয়েবসাইটে এই ধরণের তথ্য সরবরাহ করেছে, তাহলে আমরা আপনাকে অবিলম্বে আমাদের সাথে যোগাযোগ করার জন্য জোরালোভাবে উৎসাহিত করছি এবং আমরা আমাদের রেকর্ড থেকে এই ধরণের তথ্য দ্রুত অপসারণের জন্য যথাসাধ্য চেষ্টা করব।</span></p><div><span data-preserver-spaces=\"true\"><br></span></div></div></div><div class=\"elementor-element elementor-element-4f36e2b7 elementor-widget__width-initial elementor-absolute elementor-widget elementor-widget-spacer\" data-id=\"4f36e2b7\" data-element_type=\"widget\" data-settings=\"{&quot;_position&quot;:&quot;absolute&quot;}\" data-widget_type=\"spacer.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: absolute; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; z-index: 1; width: var( --container-widget-width, 100vw ); margin-bottom: 0px; max-width: 100vw; --spacer-size: 100vh; --container-widget-width: 100vw; --container-widget-flex-grow: 0; top: -0.5px; right: 551px; color: rgb(51, 51, 51); font-family: -apple-system, &quot;system-ui&quot;, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); background-color: transparent; background-image: radial-gradient(rgba(44, 255, 0, 0.06) 0%, rgba(0, 0, 0, 0) 70%);\"><div class=\"elementor-spacer\"></div></div></div>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(6,2,'bn','সহায়তা নীতি','<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">ওনেস্ট স্কুলড ম্যানেজমেন্ট সিস্টেমের জন্য সহায়তা নীতি সহায়তা নীতি</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">সহায়তা নীতি</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">১. সহায়তার পরিধি</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">আমরা নিম্নলিখিত ক্ষেত্রে সহায়তা প্রদান করি:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">সেটআপ এবং কনফিগারেশন।</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">প্রযুক্তিগত সমস্যা সমাধান।</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">বৈশিষ্ট্য-সম্পর্কিত প্রশ্ন।</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. সাপোর্ট চ্যানেল</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">ইমেইল: [sales.onesttech.com]</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">হোয়াটসঅ্যাপ: [+৮৮০ ১৯৫৯-৩৩৫৫৫৫]</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">৩. প্রতিক্রিয়া সময়</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">সাধারণ প্রশ্ন: ৪৮ কর্মঘণ্টার মধ্যে উত্তর।</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">গুরুত্বপূর্ণ সমস্যা (যেমন, পরিষেবা বন্ধ থাকার সময়): ২৪ ঘন্টার মধ্যে প্রতিক্রিয়া।</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">৪. আপডেট এবং রক্ষণাবেক্ষণ</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">বৈশিষ্ট্য বৃদ্ধি এবং বাগ সংশোধনের জন্য নিয়মিত আপডেট প্রদান করা হয়। নির্ধারিত রক্ষণাবেক্ষণের আগে বিজ্ঞপ্তি পাঠানো হবে।</span></p><p><span id=\"docs-internal-guid-500ba9c7-7fff-1baf-821f-835a1517b432\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">আপনি যদি এই নীতিগুলি আরও কাস্টমাইজ করতে চান বা আরও বিশদ যোগ করতে চান তবে আমাকে জানান!</span></p>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(7,3,'bn','শর্তাবলী','<p><b>ব্যবহারের শর্তাবলী Ischool Management System\n                </b></p><p><b><br></b>\n                                            এই নিয়ম ও শর্তাবলী স্কুল ম্যানেজমেন্ট সফ্টওয়্যার দ্বারা প্রদত্ত আপনার অ্যাক্সেস এবং ব্যবহার নিয়ন্ত্রণ করে Ischool Management System . সফ্টওয়্যার অ্যাক্সেস বা ব্যবহার করে, আপনি এই শর্তাবলী দ্বারা আবদ্ধ হতে সম্মত হন। আপনি যদি এই শর্তাবলীতে সম্মত না হন তবে অনুগ্রহ করে সফটওয়্যারটি অ্যাক্সেস করা বা ব্যবহার করা থেকে বিরত থাকুন।\n                        </p><p><br></p><p><b>\n                        শর্তাদি গ্রহণ: </b>সফ্টওয়্যার অ্যাক্সেস বা ব্যবহার করে, আপনি এই শর্তাবলী দ্বারা আবদ্ধ হতে সম্মত হন। আপনি যদি এই চুক্তির সমস্ত শর্তাবলীর সাথে সম্মত না হন তবে আপনি অবশ্যই সফ্টওয়্যারটি ব্যবহার করবেন না৷</p><p><br></p><p>\n                        </p><p><b style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(--bs-body-font-size); text- align: var(--bs-body-text-align);\">সফ্টওয়্যারের ব্যবহার:</b><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; হরফ-আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ) ;\"> সফ্টওয়্যারটি শুধুমাত্র স্কুল, কলেজ এবং বিশ্ববিদ্যালয় সহ কিন্তু সীমাবদ্ধ নয় শিক্ষা প্রতিষ্ঠান পরিচালনার উদ্দেশ্যে প্রদান করা হয়৷ আপনি কোনো অবৈধ বা অননুমোদিত উদ্দেশ্যে সফ্টওয়্যার ব্যবহার না করতে সম্মত হন৷</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font -আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ); \"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-সাইজ: var(--bs- body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); ফন্ট-ওজন: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><b>ব্যবহারকারীর অ্যাকাউন্ট: </b></ span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(-bs-body-font-size); font-weight: var(- -bs-body-font-weight); text-align: var(--bs-body-text-align);\">সফ্টওয়্যারের কিছু বৈশিষ্ট্য অ্যাক্সেস করার জন্য আপনাকে একটি অ্যাকাউন্ট তৈরি করতে হতে পারে৷ আপনার অ্যাকাউন্টের শংসাপত্রের গোপনীয়তা বজায় রাখার জন্য এবং আপনার অ্যাকাউন্টের অধীনে হওয়া সমস্ত কার্যকলাপের জন্য আপনি দায়ী৷</span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var(--bs- body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-সাইজ : var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> <br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body- font-size); text-align: var(--bs-body-text-align);\"><b>\n\n                                                    গোপনীয়তা:</b></span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size) ; font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> আমরা আপনার গোপনীয়তা রক্ষা করতে প্রতিশ্রুতিবদ্ধ। আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং প্রকাশ করি তা আমাদের গোপনীয়তা নীতি রূপরেখা দেয়। সফ্টওয়্যার ব্যবহার করে, আপনি গোপনীয়তা নীতিতে বর্ণিত আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং প্রকাশে সম্মত হন।</span></p><p><span style=\"color: var(--ot- টেক্সট-সাবটাইটেল; ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-সাইজ); হরফ-ওজন: var(--bs-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var (--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-আকার); হরফ-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট- সারিবদ্ধ);\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); পাঠ্য-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                    বুদ্ধিবৃত্তিক সম্পত্তি:</b></span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> সফটওয়্যার এবং এর মূল বিষয়বস্তু, বৈশিষ্ট্য এবং কার্যকারিতা হল মালিক Ischool Management System এবং আন্তর্জাতিক কপিরাইট, ট্রেডমার্ক, পেটেন্ট, ট্রেড সিক্রেট এবং অন্যান্য মেধা সম্পত্তি বা মালিকানা অধিকার আইন দ্বারা সুরক্ষিত।</span></p><p><span style=\"color: var(--ot-text-subtitle) ); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-আকার: var(--bs-বডি-ফন্ট-সাইজ); ফন্ট-ওজন: var(--bs-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var(-- bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; ফন্ট -আকার: var(--bs-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ); \">\n                                                    </span></p><p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font-size ); পাঠ্য-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                                                দায়বদ্ধতার সীমাবদ্ধতা:</b></span><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; ফন্ট-সাইজ: var(--bs-body-font- আকার); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> কোন ঘটনাতেই হবে না\n                                                    Ischool Management System যেকোন পরোক্ষ, আনুষঙ্গিক, বিশেষ, আনুষঙ্গিক, বা শাস্তিমূলক ক্ষতির জন্য দায়ী হতে হবে, যার মধ্যে সফ্টওয়্যার ব্যবহার বা ব্যবহারে অক্ষমতা থেকে উদ্ভূত লাভ, ডেটা বা সদিচ্ছার ক্ষতি সহ কিন্তু সীমাবদ্ধ নয়৷</span></p> <p><span style=\"color: var(--ot-text-subtitle); ব্যাকগ্রাউন্ড-রং: স্বচ্ছ; font-size: var(--bs-body-font-size); font-weight: var( --bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color : var(--ot-টেক্সট-সাবটাইটেল); ব্যাকগ্রাউন্ড-রঙ: স্বচ্ছ; হরফ-আকার: var(--BS-বডি-ফন্ট-সাইজ); হরফ-ওজন: var (--BS-বডি-ফন্ট-ওজন ); text-align: var(--bs-body-text-align);\">\n                                                    </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); text-align: var(--bs-body-text-align);\"><b>শর্তাবলীতে পরিবর্তন:</b></span><span style=\"background-color: transparent; color: var (--ot-টেক্সট-সাবটাইটেল); ফন্ট-সাইজ: var(--bs-বডি-ফন্ট-সাইজ); হরফ-ওজন: var(--BS-বডি-ফন্ট-ওজন); পাঠ্য-সারিবদ্ধ: var( --bs-body-text-align);\"> আমরা যে কোনো সময় এই শর্তাবলী পরিবর্তন বা প্রতিস্থাপন করার অধিকার সংরক্ষণ করি৷ যদি একটি সংশোধন বস্তুগত হয়, আমরা যেকোনো নতুন শর্ত কার্যকর হওয়ার অন্তত 30 দিনের নোটিশ প্রদান করব। কোন বস্তুগত পরিবর্তন গঠন করে তা আমাদের নিজস্ব বিবেচনার ভিত্তিতে নির্ধারণ করা হবে।</span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font- আকার: var(--BS-বডি-ফন্ট-আকার); ফন্ট-ওজন: var(--BS-বডি-ফন্ট-ওজন); টেক্সট-সারিবদ্ধ: var(--BS-বডি-টেক্সট-সারিবদ্ধ);\" ><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body -font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                                                    </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); পাঠ্য-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                                                পরিচালনা আইন: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">এই শর্তাবলী দ্বারা নিয়ন্ত্রিত হবে এবং এর সাথে সঙ্গতিপূর্ণ হবে মার্কিন যুক্তরাষ্ট্রের আইন, তার আইনের বিধানের বিরোধ বিবেচনা ছাড়াই।\n                                                    </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); ফন্ট-ওজন: var(--bs-body-font-weight); টেক্সট-সারিবদ্ধ: var(--bs-বডি-টেক্সট-সারিবদ্ধ);\"><br></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(-bs-body-font-size); text-align: var(- -বিএস-বডি-টেক্সট-সারিবদ্ধ);\"><b>\n                                                                                আমাদের সাথে যোগাযোগ করুন: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size ); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">এই শর্তাবলী সম্পর্কে আপনার কোন প্রশ্ন থাকলে, অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন এ&nbsp; ওগুলো\n\n                                                                                সফ্টওয়্যার অ্যাক্সেস বা ব্যবহার করে, আপনি এই শর্তাবলী দ্বারা আবদ্ধ হতে সম্মত হন। আপনি যদি এই শর্তাবলীতে সম্মত না হন তবে অনুগ্রহ করে সফটওয়্যার অ্যাক্সেস বা ব্যবহার করা থেকে বিরত থাকুন৷</span><br></p>','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(8,4,'bn','আমাদের মিশন','<p>At Ischool Management System , আমরা একটি লালনশীল এবং সমৃদ্ধ শিক্ষামূলক পরিবেশ প্রদানের জন্য নিবেদিত যা শিক্ষার্থীদের তাদের পূর্ণ সম্ভাবনায় পৌঁছানোর ক্ষমতা দেয়। আমাদের লক্ষ্য হল শিক্ষাগত উৎকর্ষতা, চরিত্রের বিকাশ, এবং আমরা যে সকল শিক্ষার্থীকে সেবা করি তাদের আজীবন শিক্ষা লাভ করা।</p><p><br></p><h3><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">আমাদের মূল মূল্যবোধ</b></h3><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><br></b><br></p><p><b>\n                ১. শ্রেষ্ঠত্ব : </b>আমরা শিক্ষার সমস্ত দিকগুলিতে শ্রেষ্ঠত্বের জন্য প্রতিশ্রুতিবদ্ধ, আমাদের শিক্ষার্থীদের সর্বোচ্চ মানের শিক্ষাদান, সংস্থান এবং সহায়তা প্রদানের জন্য সচেষ্ট।\n                    </p><p><br></p><p><b>\n                ২. সততা : </b>ছাত্র, পিতামাতা, কর্মচারী এবং সম্প্রদায়ের সাথে আমাদের মিথস্ক্রিয়াতে আমরা সততা, সততা এবং নৈতিক আচরণের সর্বোচ্চ মান বজায় রাখি।\n                    </p><p><br></p><p><b>\n                ৩. সম্মান :</b>আমরা আমাদের স্কুল সম্প্রদায়ের মধ্যে প্রতিটি ব্যক্তির অনন্য ক্ষমতা, দৃষ্টিভঙ্গি এবং পটভূমিকে মূল্যায়ন করে শ্রদ্ধার সংস্কৃতি গড়ে তুলি।\n                    </p><p><br></p><p><b>\n                ৪. সহযোগিতা : </b>আমরা আমাদের ভাগ করা লক্ষ্য অর্জনের জন্য ছাত্র, পিতামাতা, শিক্ষাবিদ এবং সম্প্রদায়ের সাথে ঘনিষ্ঠভাবে কাজ করে সহযোগিতা এবং দলবদ্ধতার শক্তিতে বিশ্বাস করি।\n                </p><p><br></p><p><b>\n                ৫. উদ্ভাবন :</b>\n                আমরা উদ্ভাবন এবং সৃজনশীলতাকে আলিঙ্গন করি, শেখার অভিজ্ঞতা বাড়াতে এবং আমাদের শিক্ষার্থীদের ক্রমবর্ধমান চাহিদা মেটাতে ক্রমাগত নতুন এবং কার্যকর উপায় খুঁজি।</p><p><br></p><p>\n                    </p><p style=\"text-align: center;\"><b><u>\n                আমাদের লক্ষ্য</u></b></p><p style=\"text-align: center;\"><b><u><br></u></b></p><p style=\"text-align: center;\"><b><u>\n                </u></b></p>\n\n\n                <ul>\n                    <li>\n                        <b>একাডেমিক শ্রেষ্ঠত্ব : </b>\n                        আমরা কঠোর একাডেমিক প্রোগ্রাম প্রদান করার চেষ্টা করি যা শিক্ষার্থীদের তাদের সর্বোচ্চ একাডেমিক সম্ভাবনা অর্জনের জন্য চ্যালেঞ্জ ও অনুপ্রাণিত করে।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b> চরিত্র বিকাশ :</b>\n                        আমরা আমাদের শিক্ষার্থীদের মধ্যে সততা, দায়িত্বশীলতা, সহানুভূতি এবং স্থিতিস্থাপকতার মতো দৃঢ় চরিত্রের বৈশিষ্ট্যের বিকাশে প্রতিশ্রুতিবদ্ধ।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b>আজীবন শিক্ষা : </b>\n                        আমাদের লক্ষ্য আমাদের শিক্ষার্থীদের মধ্যে শেখার প্রতি ভালবাসা এবং একটি বৃদ্ধির মানসিকতা জাগিয়ে তোলা, তাদের আজীবন শিক্ষার্থী হয়ে উঠতে ক্ষমতায়ন করা যারা কৌতূহলী, মানিয়ে নিতে পারে এবং নতুন ধারণা এবং সুযোগগুলি অন্বেষণ করতে আগ্রহী।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b> সম্প্রদায় জড়িত :</b>\n                        আমরা একটি সহায়ক এবং অন্তর্ভুক্তিমূলক শিক্ষার পরিবেশ তৈরি করতে পিতামাতা, পরিবার এবং বৃহত্তর সম্প্রদায়ের সাথে সক্রিয়ভাবে জড়িত থাকার চেষ্টা করি যা আমাদের শিক্ষার্থীদের সামগ্রিক বিকাশকে লালন করে। আমাদের মিশনে আমাদের সাথে যোগ দিন\n                        আমরা আপনাকে পরবর্তী প্রজন্মের নেতা, চিন্তাবিদ এবং উদ্ভাবকদের অনুপ্রাণিত ও ক্ষমতায়িত করতে আমাদের মিশনে আমাদের সাথে যোগ দেওয়ার জন্য আমন্ত্রণ জানাচ্ছি।\n                    </li>\n                </ul>\n\n                <ul>\n                    <li>\n                        <b> উজ্জ্বল ভবিষ্যত গঠনে :</b>\n                        একসাথে, আমরা আমাদের শিক্ষার্থীদের জীবনে একটি পরিবর্তন আনতে পারি এবং সবার জন্য একটি উজ্জ্বল ভবিষ্যত তৈরি করতে পারি।\n                        এই নমুনা বিষয়বস্তু স্কুলের মিশন, মূল মান, লক্ষ্য এবং সেই লক্ষ্যগুলি অর্জনে অংশীদারদের যোগদানের জন্য একটি আমন্ত্রণ প্রদান করে। আপনার স্কুল পরিচালনার আবেদনের নির্দিষ্ট মিশন এবং মানগুলির সাথে সারিবদ্ধ করার জন্য আপনি এটিকে আরও কাস্টমাইজ করতে পারেন৷\n                    </li>\n                </ul>','2025-06-03 07:04:09','2025-06-03 07:04:09',1);
/*!40000 ALTER TABLE `page_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `menu_show` enum('header','footer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'Privacy Policy','privacy_policy','<div class=\"elementor-element elementor-element-790b948d elementor-widget elementor-widget-text-editor\" data-id=\"790b948d\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: relative; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; color: var( --e-global-color-text ); font-family: var( --e-global-typography-text-font-family ), &quot;Roboto&quot;, Sans-serif; width: 1280px; margin-bottom: 0px; z-index: 3;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); padding: 0px 0px 100px;\"><h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Privacy Policy</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Last updated: 22 November, 2025</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Introduction</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Onest Schooled Management System values your privacy. This policy explains how we collect, use, and safeguard your data when you use our app.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Information We Collect</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">User Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Names, email addresses, contact details, roles (student, teacher, parent, or admin).</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Operational Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Attendance, grades, homework, library usage, and fee transactions.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Device Data</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">: Device type, operating system, and logs for app functionality.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. How We Use Your Information</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To facilitate administrative operations, such as admission, fee collection, attendance, and academic management.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To provide personalized dashboards for students, teachers, and parents.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">To enhance user experience and app security.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Sharing Your Information</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We only share data with:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">School administrators for operational purposes.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Authorities when required by law.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Data Security</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We implement industry-standard security measures, including encryption and regular audits.</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">5. Your Rights</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Access or modify personal data through the user portal.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Request deletion of your data by contacting support (subject to operational constraints).</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">6. Policy Updates</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We may update this policy. Changes will be notified via email or app alerts.</span></p><p style=\"margin-bottom: 0.9rem;\"><span id=\"docs-internal-guid-0452a37e-7fff-0696-3df4-c342ecd0bf24\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Contact Us</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">For questions or concerns, reach us at sales.onesttech.com</span></p></div></div><div class=\"elementor-element elementor-element-4f36e2b7 elementor-widget__width-initial elementor-absolute elementor-widget elementor-widget-spacer\" data-id=\"4f36e2b7\" data-element_type=\"widget\" data-settings=\"{&quot;_position&quot;:&quot;absolute&quot;}\" data-widget_type=\"spacer.default\" style=\"--flex-direction: initial; --flex-wrap: initial; --justify-content: initial; --align-items: initial; --align-content: initial; --gap: initial; --flex-basis: initial; --flex-grow: initial; --flex-shrink: initial; --order: initial; --align-self: initial; flex-basis: var(--flex-basis); flex-grow: var(--flex-grow); flex-shrink: var(--flex-shrink); order: var(--order); align-self: var(--align-self); flex-direction: var(--flex-direction); flex-wrap: var(--flex-wrap); justify-content: var(--justify-content); align-items: var(--align-items); align-content: var(--align-content); gap: var(--gap); position: absolute; --swiper-theme-color: #000; --swiper-navigation-size: 44px; --swiper-pagination-bullet-size: 6px; --swiper-pagination-bullet-horizontal-gap: 6px; --widgets-spacing: 20px; z-index: 1; width: var( --container-widget-width, 100vw ); margin-bottom: 0px; max-width: 100vw; --spacer-size: 100vh; --container-widget-width: 100vw; --container-widget-flex-grow: 0; top: -0.5px; right: 551px; color: rgb(51, 51, 51); font-family: -apple-system, &quot;system-ui&quot;, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;;\"><div class=\"elementor-widget-container\" style=\"transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform var(--e-transform-transition-duration,.4s); background-color: transparent; background-image: radial-gradient(rgba(44, 255, 0, 0.06) 0%, rgba(0, 0, 0, 0) 70%);\"><div class=\"elementor-spacer\"></div></div></div>',1,'footer','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(2,'Support Policy','support_policy','<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:13pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy for Onest Schooled Management System</span></h3><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Support Policy</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">1. Scope of Support</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">We provide assistance for:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Setup and configuration.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Troubleshooting technical issues.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Feature-related queries.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">2. Support Channels</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Email: [sales.onesttech.com]</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Whatsapp: [+880 1959-335555]</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">3. Response Time</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">General Queries: Response within 48 business hours.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Critical Issues (e.g., service downtime): Response within 24 hours.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">4. Updates &amp; Maintenance</span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"><br></span><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Regular updates are provided for feature enhancements and bug fixes. Notification will be sent before scheduled maintenance.</span></p><p><span id=\"docs-internal-guid-500ba9c7-7fff-1baf-821f-835a1517b432\"></span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Let me know if you’d like to further customize these policies or add more details!</span></p>',1,'footer','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(3,'Terms & Conditions','terms_conditions','<p><b>Terms and Conditions of Use for Ischool Management System Management Software\n                        </b></p><p><b><br></b>\n                                                    These Terms and Conditions govern your access to and use of the School Management Software , provided by Ischool Management System . By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.\n                        </p><p><br></p><p><b>\n                                                    Acceptance of Terms: </b>By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to all the terms and conditions of this agreement, you must not use the Software.</p><p><br></p><p>\n                        </p><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Use of the Software:</b><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software is provided solely for the purpose of managing educational institutions, including but not limited to schools, colleges, and universities. You agree not to use the Software for any illegal or unauthorized purpose.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><b>User Accounts: </b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">You may need to create an account to access certain features of the Software. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n\n                                                    Privacy:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We are committed to protecting your privacy. Our Privacy Policy outlines how we collect, use, and disclose your personal information. By using the Software, you consent to the collection, use, and disclosure of your personal information as described in the Privacy Policy.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Intellectual Property:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> The Software and its original content, features, and functionality are owned by Ischool Management System and are protected by international copyright, trademark, patent, trade secret, and other intellectual property or proprietary rights laws.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Limitation of Liability:</b></span><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> In no event shall\n                                                    Ischool Management System be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or goodwill, arising from the use of or inability to use the Software.</span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>Changes to Terms:</b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"> We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Governing Law: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">These Terms shall be governed by and construed in accordance with the laws of United Stated Of America , without regard to its conflict of law provisions.\n                        </span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><br></span></p><p><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><b>\n                                                    Contact Us: </b></span><span style=\"background-color: transparent; color: var(--ot-text-subtitle); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">If you have any questions about these Terms, please contact us at&nbsp; Ones .\n\n                                                    By accessing or using the Software, you agree to be bound by these Terms. If you do not agree to these Terms, please refrain from accessing or using the Software.</span><br></p>',1,'footer','2025-06-03 07:04:09','2025-06-03 07:04:09',1),(4,'Our Missions','our_missions','<p>At Ischool Management System , we are dedicated to providing a nurturing and enriching educational environment that empowers students to reach their full potential. Our mission is to foster academic excellence, character development, and lifelong learning in every student we serve.</p><p><br></p><h3><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\">Our Core Values</b></h3><p><b style=\"color: var(--ot-text-subtitle); background-color: transparent; font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align);\"><br></b><br></p><p><b>\n                        1. Excellence:\n                        </b> We are committed to excellence in all aspects of education, striving to provide the highest quality teaching, resources, and support to our students.\n                            </p><p><br></p><p><b>\n                        2. Integrity:\n                        </b> We uphold the highest standards of integrity, honesty, and ethical behavior in our interactions with students, parents, staff, and the community.\n                            </p><p><br></p><p><b>\n                        3. Respect:</b>\n                        We foster a culture of respect, valuing the unique abilities, perspectives, and backgrounds of each individual within our school community.\n                            </p><p><br></p><p><b>\n                        4. Collaboration:\n                            </b>  We believe in the power of collaboration and teamwork, working closely with students, parents, educators, and the community to achieve our shared goals.\n                        </p><p><br></p><p><b>\n                        5. Innovation:</b>\n                        We embrace innovation and creativity, continuously seeking new and effective ways to enhance the learning experience and meet the evolving needs of our students.</p><p><br></p><p>\n                            </p><p style=\"text-align: center;\"><b><u>\n                        Our Goals</u></b></p><p style=\"text-align: center;\"><b><u><br></u></b></p><p style=\"text-align: center;\"><b><u>\n                        </u></b></p><ul><li><b>                            1. Academic Excellence:\n                        </b>  We strive to provide rigorous academic programs that challenge and inspire students to achieve their highest academic potential.</li></ul><p><br></p><ul><li><b>\n                        2. Character Development:</b>\n                        We are committed to fostering the development of strong character traits such as honesty, responsibility, compassion, and resilience in our students.</li></ul><p><br></p><ul><li><b>\n                        3. Lifelong Learning:\n                        </b> We aim to instill a love of learning and a growth mindset in our students, empowering them to become lifelong learners who are curious, adaptable, and eager to explore new ideas and opportunities.</li></ul><p><br></p><ul><li><b>\n                        4 Community Engagement:\n                        </b> We seek to actively engage with parents, families, and the broader community to create a supportive and inclusive learning environment that nurtures the holistic development of our students.\n\n                        Join Us in Our Mission\n                        We invite you to join us in our mission to inspire and empower the next generation of leaders, thinkers, and innovators. </li></ul><p><br></p><p>Together, we can make a difference in the lives of our students and create a brighter future for all.\n\n                        This sample content provides an overview of the schools mission, core values, goals, and an invitation for stakeholders to join in achieving those goals. You can customize it further to align with the specific mission and values of your school management application.</p>',1,'footer','2025-06-03 07:04:09','2025-06-03 07:04:09',1);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parent_guardians`
--

DROP TABLE IF EXISTS `parent_guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parent_guardians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `father_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_relation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_place_of_work` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_guardians_user_id_foreign` (`user_id`),
  CONSTRAINT `parent_guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parent_guardians`
--

LOCK TABLES `parent_guardians` WRITE;
/*!40000 ALTER TABLE `parent_guardians` DISABLE KEYS */;
INSERT INTO `parent_guardians` VALUES (13,45,'Abdullahi Abdullahi','+252 63 5315202','Mechanic',NULL,'Somaliland','Halima Ismail','+252 65 5255751','Engineer',NULL,'Abdullahi Abdullahi','abdullahiabdullahi251@parent.somaliland.edu','+252 64 3024195',NULL,'Driver','Father','Ga\'an Libah District, Sheikh, Somaliland','Sheikh Office','Security Guard',1,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL,NULL,1),(14,46,'Axmed Ahmed','+252 63 9767754','Shopkeeper',NULL,'Somaliland','Caasha Osman','+252 64 2037975','Civil Servant',NULL,'Axmed Ahmed','axmedahmed903@parent.somaliland.edu','+252 65 8318416',NULL,'Mechanic','Father','Jigaale District, Oodweyne, Somaliland','Oodweyne School','Civil Servant',1,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL,NULL,1),(15,47,'Ibrahim Ahmed','+252 63 2413232','Security Guard',NULL,'Somaliland','Amina Mustafe','+252 65 4283873','Security Guard',NULL,'Ibrahim Ahmed','ibrahimahmed453@parent.somaliland.edu','+252 64 3138086',NULL,'Shopkeeper','Father','Maroodi Jeex District, Berbera, Somaliland','Berbera Ministry','Security Guard',1,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL,NULL,1),(16,48,'Yusuf Ismail','+252 63 8142529','Cleaner',NULL,'Somaliland','Maryan Dahir','+252 65 4502983','Accountant',NULL,'Yusuf Ismail','yusufismail219@parent.somaliland.edu','+252 64 6734571',NULL,'Chef','Father','Gacan Libaax District, Burao, Somaliland','Burao Market','Driver',1,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL,NULL,1),(17,49,'Yusuf Farah','+252 64 6662410','Mechanic',NULL,'Somaliland','Amina Hersi','+252 64 9167449','Teacher',NULL,'Yusuf Farah','yusuffarah300@parent.somaliland.edu','+252 65 4642283',NULL,'Mechanic','Father','Shacab District, Caynabo, Somaliland','Caynabo Hospital','Security Guard',1,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL,NULL,1),(18,50,'Osman Ali','+252 65 7619271','Civil Servant',NULL,'Somaliland','Sahra Mustafe','+252 65 5850366','Engineer',NULL,'Osman Ali','osmanali639@parent.somaliland.edu','+252 64 9746860',NULL,'Business Owner','Father','Shacab District, Zeila, Somaliland','Zeila Ministry','Chef',1,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL,NULL,1),(19,51,'Ahmed Ali','+252 65 8416723','Engineer',NULL,'Somaliland','Khadija Cabdi','+252 63 1987822','Business Owner',NULL,'Ahmed Ali','ahmedali668@parent.somaliland.edu','+252 64 9338957',NULL,'Business Owner','Father','Gacan Libaax District, Sheikh, Somaliland','Sheikh Ministry','Engineer',1,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,1),(20,52,'Mohamed Abdi','+252 65 6941191','Tailor',NULL,'Somaliland','Hodan Ali','+252 63 4636201','Security Guard',NULL,'Mohamed Abdi','mohamedabdi132@parent.somaliland.edu','+252 65 8287573',NULL,'Tailor','Father','Cabdi Bile District, Zeila, Somaliland','Zeila Market','Trader',1,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,1),(21,53,'Cabdi Osman','+252 63 2273212','Business Owner',NULL,'Somaliland','Halima Cabdi','+252 65 7500403','Trader',NULL,'Cabdi Osman','cabdiosman105@parent.somaliland.edu','+252 63 3481122',NULL,'Shopkeeper','Father','Ahmed Dhagah District, Berbera, Somaliland','Berbera Market','Accountant',1,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,1),(22,54,'Ibrahim Omar','+252 65 3622220','Security Guard',NULL,'Somaliland','Hibo Ahmed','+252 65 1564939','Tailor',NULL,'Ibrahim Omar','ibrahimomar694@parent.somaliland.edu','+252 63 2700334',NULL,'Cleaner','Father','Cabdi Bile District, Sheikh, Somaliland','Sheikh Hospital','Mechanic',1,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,1),(23,55,'Abdirashid Hassan','+252 65 9679639','Teacher',NULL,'Somaliland','Sahra Abdi','+252 63 8020670','Mechanic',NULL,'Abdirashid Hassan','abdirashidhassan487@parent.somaliland.edu','+252 65 4074294',NULL,'Trader','Father','Ahmed Dhagah District, Caynabo, Somaliland','Caynabo Office','Chef',1,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,1),(24,56,'Abdirashid Ibrahim','+252 63 4564129','Accountant',NULL,'Somaliland','Naima Farah','+252 65 5587581','Farmer',NULL,'Abdirashid Ibrahim','abdirashidibrahim399@parent.somaliland.edu','+252 65 3392014',NULL,'Trader','Father','Ga\'an Libah District, Hargeisa, Somaliland','Hargeisa Market','Farmer',1,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,1);
/*!40000 ALTER TABLE `parent_guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','{\"read\":\"calendar_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'student','{\"read\":\"student_read\",\"create\":\"student_create\",\"update\":\"student_update\",\"delete\":\"student_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'student_category','{\"read\":\"student_category_read\",\"create\":\"student_category_create\",\"update\":\"student_category_update\",\"delete\":\"student_category_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'promote_students','{\"read\":\"promote_students_read\",\"create\":\"promote_students_create\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'disabled_students','{\"read\":\"disabled_students_read\",\"create\":\"disabled_students_create\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'parent','{\"read\":\"parent_read\",\"create\":\"parent_create\",\"update\":\"parent_update\",\"delete\":\"parent_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'admission','{\"read\":\"admission_read\",\"create\":\"admission_create\",\"update\":\"admission_update\",\"delete\":\"admission_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'classes','{\"read\":\"classes_read\",\"create\":\"classes_create\",\"update\":\"classes_update\",\"delete\":\"classes_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'section','{\"read\":\"section_read\",\"create\":\"section_create\",\"update\":\"section_update\",\"delete\":\"section_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'shift','{\"read\":\"shift_read\",\"create\":\"shift_create\",\"update\":\"shift_update\",\"delete\":\"shift_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'class_setup','{\"read\":\"class_setup_read\",\"create\":\"class_setup_create\",\"update\":\"class_setup_update\",\"delete\":\"class_setup_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'subject','{\"read\":\"subject_read\",\"create\":\"subject_create\",\"update\":\"subject_update\",\"delete\":\"subject_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,'subject_assign','{\"read\":\"subject_assign_read\",\"create\":\"subject_assign_create\",\"update\":\"subject_assign_update\",\"delete\":\"subject_assign_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,'class_routine','{\"read\":\"report_class_routine_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,'time_schedule','{\"read\":\"time_schedule_read\",\"create\":\"time_schedule_create\",\"update\":\"time_schedule_update\",\"delete\":\"time_schedule_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,'class_room','{\"read\":\"class_room_read\",\"create\":\"class_room_create\",\"update\":\"class_room_update\",\"delete\":\"class_room_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,'fees_group','{\"read\":\"fees_group_read\",\"create\":\"fees_group_create\",\"update\":\"fees_group_update\",\"delete\":\"fees_group_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,'fees_type','{\"read\":\"fees_type_read\",\"create\":\"fees_type_create\",\"update\":\"fees_type_update\",\"delete\":\"fees_type_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,'fees_master','{\"read\":\"fees_master_read\",\"create\":\"fees_master_create\",\"update\":\"fees_master_update\",\"delete\":\"fees_master_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,'fees_assign','{\"read\":\"fees_assign_read\",\"create\":\"fees_assign_create\",\"update\":\"fees_assign_update\",\"delete\":\"fees_assign_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,'fees_collect','{\"read\":\"fees_collect_read\",\"create\":\"fees_collect_create\",\"update\":\"fees_collect_update\",\"delete\":\"fees_collect_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,'discount_setup','{\"siblings_discount\":\"siblings_discount\",\"early_payment_discount\":\"early_payment_discount\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,'exam_type','{\"read\":\"exam_type_read\",\"create\":\"exam_type_create\",\"update\":\"exam_type_update\",\"delete\":\"exam_type_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,'marks_grade','{\"read\":\"marks_grade_read\",\"create\":\"marks_grade_create\",\"update\":\"marks_grade_update\",\"delete\":\"marks_grade_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(25,'exam_assign','{\"read\":\"exam_assign_read\",\"create\":\"exam_assign_create\",\"update\":\"exam_assign_update\",\"delete\":\"exam_assign_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(26,'exam_routine','{\"read\":\"report_exam_routine_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(27,'marks_register','{\"read\":\"marks_register_read\",\"create\":\"marks_register_create\",\"update\":\"marks_register_update\",\"delete\":\"marks_register_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(28,'homework','{\"read\":\"homework_read\",\"create\":\"homework_create\",\"update\":\"homework_update\",\"delete\":\"homework_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(29,'exam_setting','{\"read\":\"exam_setting_read\",\"update\":\"exam_setting_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(30,'account_head','{\"read\":\"account_head_read\",\"create\":\"account_head_create\",\"update\":\"account_head_update\",\"delete\":\"account_head_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(31,'income','{\"read\":\"income_read\",\"create\":\"income_create\",\"update\":\"income_update\",\"delete\":\"income_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(32,'expense','{\"read\":\"expense_read\",\"create\":\"expense_create\",\"update\":\"expense_update\",\"delete\":\"expense_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(33,'attendance','{\"read\":\"attendance_read\",\"create\":\"attendance_create\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(34,'attendance_report','{\"read\":\"report_attendance_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(35,'marksheet','{\"read\":\"report_marksheet_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(36,'merit_list','{\"read\":\"report_merit_list_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(37,'progress_card','{\"read\":\"report_progress_card_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(38,'due_fees','{\"read\":\"report_due_fees_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(39,'fees_collection','{\"read\":\"report_fees_collection_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(40,'account','{\"read\":\"report_account_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(41,'language','{\"read\":\"language_read\",\"create\":\"language_create\",\"update\":\"language_update\",\"update terms\":\"language_update_terms\",\"delete\":\"language_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(42,'roles','{\"read\":\"role_read\",\"create\":\"role_create\",\"update\":\"role_update\",\"delete\":\"role_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(43,'users','{\"read\":\"user_read\",\"create\":\"user_create\",\"update\":\"user_update\",\"delete\":\"user_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(44,'department','{\"read\":\"department_read\",\"create\":\"department_create\",\"update\":\"department_update\",\"delete\":\"department_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(45,'designation','{\"read\":\"designation_read\",\"create\":\"designation_create\",\"update\":\"designation_update\",\"delete\":\"designation_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(46,'sections','{\"read\":\"page_sections_read\",\"update\":\"page_sections_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(47,'slider','{\"read\":\"slider_read\",\"create\":\"slider_create\",\"update\":\"slider_update\",\"delete\":\"slider_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(48,'about','{\"read\":\"about_read\",\"create\":\"about_create\",\"update\":\"about_update\",\"delete\":\"about_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(49,'counter','{\"read\":\"counter_read\",\"create\":\"counter_create\",\"update\":\"counter_update\",\"delete\":\"counter_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(50,'contact_info','{\"read\":\"contact_info_read\",\"create\":\"contact_info_create\",\"update\":\"contact_info_update\",\"delete\":\"contact_info_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(51,'dep_contact','{\"read\":\"dep_contact_read\",\"create\":\"dep_contact_create\",\"update\":\"dep_contact_update\",\"delete\":\"dep_contact_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(52,'news','{\"read\":\"news_read\",\"create\":\"news_create\",\"update\":\"news_update\",\"delete\":\"news_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(53,'event','{\"read\":\"event_read\",\"create\":\"event_create\",\"update\":\"event_update\",\"delete\":\"event_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(54,'gallery_category','{\"read\":\"gallery_category_read\",\"create\":\"gallery_category_create\",\"update\":\"gallery_category_update\",\"delete\":\"gallery_category_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(55,'gallery','{\"read\":\"gallery_read\",\"create\":\"gallery_create\",\"update\":\"gallery_update\",\"delete\":\"gallery_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(56,'subscribe','{\"read\":\"subscribe_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(57,'contact_message','{\"read\":\"contact_message_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(58,'general_settings','{\"read\":\"general_settings_read\",\"update\":\"general_settings_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(59,'storage_settings','{\"read\":\"storage_settings_read\",\"update\":\"storage_settings_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(60,'task_schedules','{\"read\":\"task_schedules_read\",\"update\":\"task_schedules_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(61,'software_update','{\"read\":\"software_update_read\",\"update\":\"software_update_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(62,'recaptcha_settings','{\"read\":\"recaptcha_settings_read\",\"update\":\"recaptcha_settings_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(63,'payment_gateway_settings','{\"read\":\"payment_gateway_settings_read\",\"update\":\"payment_gateway_settings_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(64,'email_settings','{\"read\":\"email_settings_read\",\"update\":\"email_settings_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(65,'sms_settings','{\"read\":\"sms_settings_read\",\"update\":\"sms_settings_update\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(66,'genders','{\"read\":\"gender_read\",\"create\":\"gender_create\",\"update\":\"gender_update\",\"delete\":\"gender_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(67,'religions','{\"read\":\"religion_read\",\"create\":\"religion_create\",\"update\":\"religion_update\",\"delete\":\"religion_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(68,'blood_groups','{\"read\":\"blood_group_read\",\"create\":\"blood_group_create\",\"update\":\"blood_group_update\",\"delete\":\"blood_group_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(69,'sessions','{\"read\":\"session_read\",\"create\":\"session_create\",\"update\":\"session_update\",\"delete\":\"session_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(70,'tax_setup','{\"update\":\"tax_setup\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(71,'book_category','{\"read\":\"book_category_read\",\"create\":\"book_category_create\",\"update\":\"book_category_update\",\"delete\":\"book_category_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(72,'book','{\"read\":\"book_read\",\"create\":\"book_create\",\"update\":\"book_update\",\"delete\":\"book_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(73,'member','{\"read\":\"member_read\",\"create\":\"member_create\",\"update\":\"member_update\",\"delete\":\"member_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(74,'member_category','{\"read\":\"member_category_read\",\"create\":\"member_category_create\",\"update\":\"member_category_update\",\"delete\":\"member_category_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(75,'issue_book','{\"read\":\"issue_book_read\",\"create\":\"issue_book_create\",\"update\":\"issue_book_update\",\"delete\":\"issue_book_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(76,'online_exam_type','{\"read\":\"online_exam_type_read\",\"create\":\"online_exam_type_create\",\"update\":\"online_exam_type_update\",\"delete\":\"online_exam_type_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(77,'question_group','{\"read\":\"question_group_read\",\"create\":\"question_group_create\",\"update\":\"question_group_update\",\"delete\":\"question_group_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(78,'question_bank','{\"read\":\"question_bank_read\",\"create\":\"question_bank_create\",\"update\":\"question_bank_update\",\"delete\":\"question_bank_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(79,'online_exam','{\"read\":\"online_exam_read\",\"create\":\"online_exam_create\",\"update\":\"online_exam_update\",\"delete\":\"online_exam_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(80,'id_card','{\"read\":\"id_card_read\",\"create\":\"id_card_create\",\"update\":\"id_card_update\",\"delete\":\"id_card_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(81,'id_card_generate','{\"read\":\"id_card_generate_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(82,'certificate','{\"read\":\"certificate_read\",\"create\":\"certificate_create\",\"update\":\"certificate_update\",\"delete\":\"certificate_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(83,'certificate_generate','{\"read\":\"certificate_generate_read\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(84,'gmeet','{\"read\":\"gmeet_read\",\"create\":\"gmeet_create\",\"update\":\"gmeet_update\",\"delete\":\"gmeet_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(85,'notice_board','{\"read\":\"notice_board_read\",\"create\":\"notice_board_create\",\"update\":\"notice_board_update\",\"delete\":\"notice_board_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(86,'sms_mail_template','{\"read\":\"sms_mail_template_read\",\"create\":\"sms_mail_template_create\",\"update\":\"nsms_mail_templateupdate\",\"delete\":\"sms_mail_template_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(87,'sms_mail','{\"read\":\"sms_mail_read\",\"create\":\"sms_mail_send\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(88,'forums','{\"read\":\"forum_list\",\"create\":\"forum_create\",\"update\":\"forum_update\",\"delete\":\"forum_delete\",\"forum_feeds\":\"forum_feeds\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(89,'forum_comment','{\"read\":\"forum_comment_list\",\"create\":\"forum_comment_create\",\"update\":\"forum_comment_update\",\"delete\":\"forum_comment_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(90,'memories','{\"read\":\"memory_list\",\"create\":\"memory_create\",\"update\":\"memory_update\",\"delete\":\"memory_delete\"}','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(91,'fees_generation','{\"read\":\"fees_generate_read\",\"create\":\"fees_generate_create\",\"update\":\"fees_generate_update\",\"delete\":\"fees_generate_delete\"}','2025-09-04 02:51:14','2025-09-04 02:51:14',1);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promote_students`
--

DROP TABLE IF EXISTS `promote_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promote_students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promote_students`
--

LOCK TABLES `promote_students` WRITE;
/*!40000 ALTER TABLE `promote_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `promote_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_bank_childrens`
--

DROP TABLE IF EXISTS `question_bank_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_bank_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question_bank_id` bigint unsigned NOT NULL,
  `option` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `question_bank_childrens_question_bank_id_foreign` (`question_bank_id`),
  CONSTRAINT `question_bank_childrens_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_bank_childrens`
--

LOCK TABLES `question_bank_childrens` WRITE;
/*!40000 ALTER TABLE `question_bank_childrens` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_bank_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_banks`
--

DROP TABLE IF EXISTS `question_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `question_group_id` bigint unsigned NOT NULL,
  `type` tinyint DEFAULT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_option` int DEFAULT NULL,
  `mark` int DEFAULT NULL,
  `answer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `question_banks_session_id_foreign` (`session_id`),
  KEY `question_banks_question_group_id_foreign` (`question_group_id`),
  CONSTRAINT `question_banks_question_group_id_foreign` FOREIGN KEY (`question_group_id`) REFERENCES `question_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `question_banks_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_banks`
--

LOCK TABLES `question_banks` WRITE;
/*!40000 ALTER TABLE `question_banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_groups`
--

DROP TABLE IF EXISTS `question_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `question_groups_session_id_foreign` (`session_id`),
  CONSTRAINT `question_groups_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_groups`
--

LOCK TABLES `question_groups` WRITE;
/*!40000 ALTER TABLE `question_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `religions`
--

DROP TABLE IF EXISTS `religions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `religions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `religions`
--

LOCK TABLES `religions` WRITE;
/*!40000 ALTER TABLE `religions` DISABLE KEYS */;
INSERT INTO `religions` VALUES (1,'Islam',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `religions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `religon_translates`
--

DROP TABLE IF EXISTS `religon_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `religon_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `religion_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `religon_translates_religion_id_foreign` (`religion_id`),
  CONSTRAINT `religon_translates_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `religon_translates`
--

LOCK TABLES `religon_translates` WRITE;
/*!40000 ALTER TABLE `religon_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `religon_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','super-admin','1','[\"counter_read\",\"fees_collesction_read\",\"revenue_read\",\"fees_collection_this_month_read\",\"income_expense_read\",\"upcoming_events_read\",\"attendance_chart_read\",\"calendar_read\",\"student_read\",\"student_create\",\"student_update\",\"student_delete\",\"student_category_read\",\"student_category_create\",\"student_category_update\",\"student_category_delete\",\"promote_students_read\",\"promote_students_create\",\"disabled_students_read\",\"disabled_students_create\",\"parent_read\",\"parent_create\",\"parent_update\",\"parent_delete\",\"admission_read\",\"admission_create\",\"admission_update\",\"admission_delete\",\"classes_read\",\"classes_create\",\"classes_update\",\"classes_delete\",\"section_read\",\"section_create\",\"section_update\",\"section_delete\",\"shift_read\",\"shift_create\",\"shift_update\",\"shift_delete\",\"class_setup_read\",\"class_setup_create\",\"class_setup_update\",\"class_setup_delete\",\"subject_read\",\"subject_create\",\"subject_update\",\"subject_delete\",\"subject_assign_read\",\"subject_assign_create\",\"subject_assign_update\",\"subject_assign_delete\",\"class_routine_read\",\"class_routine_create\",\"class_routine_update\",\"class_routine_delete\",\"time_schedule_read\",\"time_schedule_create\",\"time_schedule_update\",\"time_schedule_delete\",\"class_room_read\",\"class_room_create\",\"class_room_update\",\"class_room_delete\",\"fees_group_read\",\"fees_group_create\",\"fees_group_update\",\"fees_group_delete\",\"fees_type_read\",\"fees_type_create\",\"fees_type_update\",\"fees_type_delete\",\"fees_master_read\",\"fees_master_create\",\"fees_master_update\",\"fees_master_delete\",\"fees_assign_read\",\"fees_assign_create\",\"fees_assign_update\",\"fees_assign_delete\",\"fees_collect_read\",\"fees_collect_create\",\"fees_collect_update\",\"fees_collect_delete\",\"exam_type_read\",\"exam_type_create\",\"exam_type_update\",\"exam_type_delete\",\"marks_grade_read\",\"marks_grade_create\",\"marks_grade_update\",\"marks_grade_delete\",\"exam_assign_read\",\"exam_assign_create\",\"exam_assign_update\",\"exam_assign_delete\",\"exam_routine_read\",\"exam_routine_create\",\"exam_routine_update\",\"exam_routine_delete\",\"marks_register_read\",\"marks_register_create\",\"marks_register_update\",\"marks_register_delete\",\"homework_read\",\"homework_create\",\"homework_update\",\"homework_delete\",\"exam_setting_read\",\"exam_setting_update\",\"account_head_read\",\"account_head_create\",\"account_head_update\",\"account_head_delete\",\"income_read\",\"income_create\",\"income_update\",\"income_delete\",\"expense_read\",\"expense_create\",\"expense_update\",\"expense_delete\",\"attendance_read\",\"attendance_create\",\"report_attendance_read\",\"report_marksheet_read\",\"report_merit_list_read\",\"report_progress_card_read\",\"report_due_fees_read\",\"report_fees_collection_read\",\"report_account_read\",\"report_class_routine_read\",\"report_exam_routine_read\",\"report_attendance_read\",\"language_read\",\"language_create\",\"language_update\",\"language_update_terms\",\"language_delete\",\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"department_read\",\"department_create\",\"department_update\",\"department_delete\",\"designation_read\",\"designation_create\",\"designation_update\",\"designation_delete\",\"page_sections_read\",\"page_sections_update\",\"slider_read\",\"slider_create\",\"slider_update\",\"slider_delete\",\"about_read\",\"about_create\",\"about_update\",\"about_delete\",\"counter_read\",\"counter_create\",\"counter_update\",\"counter_delete\",\"contact_info_read\",\"contact_info_create\",\"contact_info_update\",\"contact_info_delete\",\"dep_contact_read\",\"dep_contact_create\",\"dep_contact_update\",\"dep_contact_delete\",\"news_read\",\"news_create\",\"news_update\",\"news_delete\",\"event_read\",\"event_create\",\"event_update\",\"event_delete\",\"gallery_category_read\",\"gallery_category_create\",\"gallery_category_update\",\"gallery_category_delete\",\"gallery_read\",\"gallery_create\",\"gallery_update\",\"gallery_delete\",\"subscribe_read\",\"contact_message_read\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_update\",\"task_schedules_read\",\"task_schedules_update\",\"software_update_read\",\"software_update_update\",\"recaptcha_settings_read\",\"recaptcha_settings_update\",\"payment_gateway_settings_read\",\"payment_gateway_settings_update\",\"email_settings_read\",\"email_settings_update\",\"gender_read\",\"gender_create\",\"gender_update\",\"gender_delete\",\"religion_read\",\"religion_create\",\"religion_update\",\"religion_delete\",\"blood_group_read\",\"blood_group_create\",\"blood_group_update\",\"blood_group_delete\",\"session_read\",\"session_create\",\"session_update\",\"session_delete\",\"book_category_read\",\"book_category_create\",\"book_category_update\",\"book_category_delete\",\"book_read\",\"book_create\",\"book_update\",\"book_delete\",\"member_read\",\"member_create\",\"member_update\",\"member_delete\",\"member_category_read\",\"member_category_create\",\"member_category_update\",\"member_category_delete\",\"issue_book_read\",\"issue_book_create\",\"issue_book_update\",\"issue_book_delete\",\"online_exam_type_read\",\"online_exam_type_create\",\"online_exam_type_update\",\"online_exam_type_delete\",\"question_group_read\",\"question_group_create\",\"question_group_update\",\"question_group_delete\",\"question_bank_read\",\"question_bank_create\",\"question_bank_update\",\"question_bank_delete\",\"online_exam_read\",\"online_exam_create\",\"online_exam_update\",\"online_exam_delete\",\"forum_list\",\"forum_create\",\"forum_update\",\"forum_delete\",\"forum_feeds\",\"forum_comment_list\",\"forum_comment_create\",\"forum_comment_update\",\"forum_comment_delete\",\"memory_list\",\"memory_create\",\"memory_update\",\"memory_delete\"]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Admin','admin','1','[\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"language_read\",\"language_create\",\"language_update_terms\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_read\",\"recaptcha_settings_update\",\"email_settings_read\"]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Staff','staff','1','[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'Accounting','accounting','1','[\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"language_read\",\"language_create\",\"language_update_terms\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_read\",\"recaptcha_settings_update\",\"email_settings_read\"]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'Teacher','teacher','1','[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'Student','student','1','[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'Gurdian','gurdian','1','[]','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_domain_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` bigint unsigned NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schools_package_id_foreign` (`package_id`),
  CONSTRAINT `schools_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `searches`
--

DROP TABLE IF EXISTS `searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `searches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin, Student, Parent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `searches`
--

LOCK TABLES `searches` WRITE;
/*!40000 ALTER TABLE `searches` DISABLE KEYS */;
INSERT INTO `searches` VALUES (1,'dashboard','Dashboard','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'roles.index','Roles','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'genders.index','Genders','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'religions.index','Religions','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'blood-groups.index','Blood Groups','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'sessions.index','Sessions','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'users.index','Users','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'my.profile','Profile','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'languages.index','Languages','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'settings.general-settings','General Settings','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'department.index','Department','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'designation.index','Designation','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,'student.index','Student','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,'student_category.index','Student Category','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,'promote_students.index','Promote Students','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,'disabled_students.index','Disabled Student','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,'parent.index','Parent','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,'online-admissions.index','Online Admissions','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,'book-category.index','Book Category','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,'book.index','Book','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,'member.index','Member','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,'issue-book.index','Issue Book','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,'member-category.index','Member Category','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,'fees-group.index','Fees Group','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(25,'fees-type.index','Fees Type','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(26,'fees-master.index','Fees Master','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(27,'fees-assign.index','Fees Assign','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(28,'fees-collect.index','Fees Collect','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(29,'exam-type.index','Exam Type','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(30,'marks-grade.index','Marks Grade','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(31,'marks-register.index','Marks Register','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(32,'exam-routine.index','Exam Routine','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(33,'exam-assign.index','Exam Assign','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(34,'examination-settings.index','Examination Settings','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(35,'attendance.index','Attendance','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(36,'account_head.index','Account Head','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(37,'income.index','Income','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(38,'expense.index','Expense','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(39,'classes.index','Classes','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(40,'section.index','Sections','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(41,'subject.index','Subjects','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(42,'shift.index','Shifts','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(43,'class-room.index','Class Room','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(44,'class-setup.index','Class Setup','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(45,'assign-subject.index','Assign Subject','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(46,'class-routine.index','Class Routine','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(47,'time_schedule.index','Time Schedule','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(48,'report-marksheet.index','Marksheet Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(49,'report-merit-list.index','Merit list Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(50,'report-progress-card.index','Progress Card Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(51,'report-due-fees.index','Due Fees Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(52,'report-fees-collection.index','Fees Collection Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(53,'report-account.index','Account Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(54,'report-attendance.report','Attendance Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(55,'report-class-routine.index','Class Routine Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(56,'report-exam-routine.index','Exam Routine Report','Admin','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(57,'student-panel-dashboard.index','Dashboard','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(58,'student-panel.profile','Profile','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(59,'student-panel-subject-list.index','Subject List','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(60,'student-panel-class-routine.index','Class Routine','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(61,'student-panel-exam-routine.index','Exam Routine','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(62,'student-panel-marksheet.index','Marksheet','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(63,'student-panel-attendance.index','Attendance','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(64,'student-panel-fees.index','Fees','Student','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(65,'parent-panel-dashboard.index.index','Dashboard','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(66,'parent-panel.profile','Profile','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(67,'parent-panel-subject-list.index','Subject List','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(68,'parent-panel-class-routine.index','Class Routine','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(69,'parent-panel-exam-routine.index','Exam Routine','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(70,'parent-panel-marksheet.index','Marksheet','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(71,'parent-panel-fees.index','Fees','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(72,'parent-panel-attendance.index','Attendance','Parent','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `searches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section_translates`
--

DROP TABLE IF EXISTS `section_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `section_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `section_translates_section_id_foreign` (`section_id`),
  CONSTRAINT `section_translates_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_translates`
--

LOCK TABLES `section_translates` WRITE;
/*!40000 ALTER TABLE `section_translates` DISABLE KEYS */;
INSERT INTO `section_translates` VALUES (1,1,'en','','','\"[{\\\"name\\\":\\\"Facebook\\\",\\\"icon\\\":\\\"fab fa-facebook-f\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.facebook.com\\\"},{\\\"name\\\":\\\"Twitter\\\",\\\"icon\\\":\\\"fab fa-twitter\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.twitter.com\\\"},{\\\"name\\\":\\\"Pinterest\\\",\\\"icon\\\":\\\"fab fa-pinterest-p\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.pinterest.com\\\"},{\\\"name\\\":\\\"Instagram\\\",\\\"icon\\\":\\\"fab fa-instagram\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.instagram.com\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Statement Of Onest Schooleded','','\"[{\\\"title\\\":\\\"Mission Statement\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Read More\\\"},{\\\"title\\\":\\\"Vision Statement\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet Read More\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Study at Onest Schooleded','Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet','\"[{\\\"icon\\\":8,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\\\"},{\\\"icon\\\":9,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\\\"},{\\\"icon\\\":10,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. veniam consequat sunt nostrud amet\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,4,'en','Explore Onest Schoooled','\"We Educate Knowledge & Essential Skills\" is a phrase that emphasizes the importance of both theoretical knowledge','\"[{\\\"tab\\\":\\\"Campus Life\\\",\\\"title\\\":\\\"Campus Life\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"},{\\\"tab\\\":\\\"Academic\\\",\\\"title\\\":\\\"Academic\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"},{\\\"tab\\\":\\\"Athletics\\\",\\\"title\\\":\\\"Athletics\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"},{\\\"tab\\\":\\\"School\\\",\\\"title\\\":\\\"School\\\",\\\"description\\\":\\\"Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,5,'en','Excellence In Teaching And Learning','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will frequently occurs that pleasures. Provide Endless Opportunities','\"[\\\"A higher education qualification\\\",\\\"Better career prospects\\\",\\\"Better career prospects\\\",\\\"Better career prospects\\\"]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,6,'en','20+ Academic Curriculum','Onsest Schooled is home to more than 20,000 students and 230,000 alumni with a wide variety of interests, ages and backgrounds, the University reflects the city’s dynamic mix of populations.','\"[\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\",\\\"Bangal Medium\\\"]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,7,'en','What’s Coming Up?','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,8,'en','Latest From Our Blog','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,9,'en','Our Gallery','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligation.','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,10,'en','Find Our <br> Contact Information','','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,11,'en','Contact By Department','Welcomed every pain avoided but in certain circumstances owing obligations of business it will to the claims of duty or the obligations of business it will','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,12,'en','Our Featured Teachers','','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,1,'bn','','','\"[{\\\"name\\\":\\\"\\\\u09ab\\\\u09c7\\\\u09b8\\\\u09ac\\\\u09c1\\\\u0995\\\",\\\"icon\\\":\\\"fab fa-facebook-f\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.facebook.com\\\"},{\\\"name\\\":\\\"\\\\u099f\\\\u09c1\\\\u0987\\\\u099f\\\\u09be\\\\u09b0\\\",\\\"icon\\\":\\\"fab fa-twitter\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.twitter.com\\\"},{\\\"name\\\":\\\"Pinterest\\\",\\\"icon\\\":\\\"fab fa-pinterest-p\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.pinterest.com\\\"},{\\\"name\\\":\\\"\\\\u0987\\\\u09a8\\\\u09b8\\\\u09cd\\\\u099f\\\\u09be\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u09ae\\\",\\\"icon\\\":\\\"fab fa-instagram\\\",\\\"link\\\":\\\"http:\\\\\\/\\\\\\/www.instagram.com\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,2,'bn','Onest Schooled এর স্টেটমেন্ট','','\"[{\\\"title\\\":\\\"\\\\u09ae\\\\u09bf\\\\u09b6\\\\u09a8 \\\\u09ac\\\\u09bf\\\\u09ac\\\\u09c3\\\\u09a4\\\\u09bf\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09aa\\\\u09a1\\\\u09bc\\\\u09c1\\\\u09a8\\\"},{\\\"title\\\":\\\"\\\\u09a6\\\\u09c3\\\\u09b7\\\\u09cd\\\\u099f\\\\u09bf \\\\u09ac\\\\u09bf\\\\u09ac\\\\u09c3\\\\u09a4\\\\u09bf\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u09af\\\\u09bc\\\\u09be\\\\u09ae \\\\u0986\\\\u09aa\\\\u09a8\\\\u09be\\\\u0995\\\\u09c7 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09aa\\\\u09a1\\\\u09bc\\\\u09a4\\\\u09c7 \\\\u09b6\\\\u09bf\\\\u0996\\\\u09a4\\\\u09c7 \\\\u09b8\\\\u09be\\\\u09b9\\\\u09be\\\\u09af\\\\u09cd\\\\u09af \\\\u0995\\\\u09b0\\\\u09ac\\\\u09c7\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,3,'bn','শিক্ষাদান এবং শেখার ক্ষেত্রে শ্রেষ্ঠত্ব','Onsest Schooled হল 20,000 টিরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রদের বিভিন্ন ধরনের আগ্রহ, বয়স এবং ব্যাকগ্রাউন্ড সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','\"[{\\\"icon\\\":8,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Onsest Schooled \\\\u09b9\\\\u09b2 20,000 \\\\u099f\\\\u09bf\\\\u09b0\\\\u0993 \\\\u09ac\\\\u09c7\\\\u09b6\\\\u09bf \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0 \\\\u098f\\\\u09ac\\\\u0982 230,000 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09be\\\\u0995\\\\u09cd\\\\u09a4\\\\u09a8 \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0\\\\u09a6\\\\u09c7\\\\u09b0 \\\\u09ac\\\\u09bf\\\\u09ad\\\\u09bf\\\\u09a8\\\\u09cd\\\\u09a8 \\\\u09a7\\\\u09b0\\\\u09a8\\\\u09c7\\\\u09b0 \\\\u0986\\\\u0997\\\\u09cd\\\\u09b0\\\\u09b9, \\\\u09ac\\\\u09af\\\\u09bc\\\\u09b8 \\\\u098f\\\\u09ac\\\\u0982 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u0995\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u0989\\\\u09a8\\\\u09cd\\\\u09a1 \\\\u09b8\\\\u09b9, \\\\u09ac\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09ac\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\\u099f\\\\u09bf \\\\u09b6\\\\u09b9\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u099c\\\\u09a8\\\\u09b8\\\\u0982\\\\u0996\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0 \\\\u0997\\\\u09a4\\\\u09bf\\\\u09b6\\\\u09c0\\\\u09b2 \\\\u09ae\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09b0\\\\u09a3\\\\u0995\\\\u09c7 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09a4\\\\u09bf\\\\u09ab\\\\u09b2\\\\u09bf\\\\u09a4 \\\\u0995\\\\u09b0\\\\u09c7\\\\u0964\\\"},{\\\"icon\\\":9,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Onsest Schooled \\\\u09b9\\\\u09b2 20,000 \\\\u099f\\\\u09bf\\\\u09b0\\\\u0993 \\\\u09ac\\\\u09c7\\\\u09b6\\\\u09bf \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0 \\\\u098f\\\\u09ac\\\\u0982 230,000 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09be\\\\u0995\\\\u09cd\\\\u09a4\\\\u09a8 \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0\\\\u09a6\\\\u09c7\\\\u09b0 \\\\u09ac\\\\u09bf\\\\u09ad\\\\u09bf\\\\u09a8\\\\u09cd\\\\u09a8 \\\\u09a7\\\\u09b0\\\\u09a8\\\\u09c7\\\\u09b0 \\\\u0986\\\\u0997\\\\u09cd\\\\u09b0\\\\u09b9, \\\\u09ac\\\\u09af\\\\u09bc\\\\u09b8 \\\\u098f\\\\u09ac\\\\u0982 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u0995\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u0989\\\\u09a8\\\\u09cd\\\\u09a1 \\\\u09b8\\\\u09b9, \\\\u09ac\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09ac\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\\u099f\\\\u09bf \\\\u09b6\\\\u09b9\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u099c\\\\u09a8\\\\u09b8\\\\u0982\\\\u0996\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0 \\\\u0997\\\\u09a4\\\\u09bf\\\\u09b6\\\\u09c0\\\\u09b2 \\\\u09ae\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09b0\\\\u09a3\\\\u0995\\\\u09c7 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09a4\\\\u09bf\\\\u09ab\\\\u09b2\\\\u09bf\\\\u09a4 \\\\u0995\\\\u09b0\\\\u09c7\\\\u0964\\\"},{\\\"icon\\\":10,\\\"title\\\":\\\"Out Prospects\\\",\\\"description\\\":\\\"Onsest Schooled \\\\u09b9\\\\u09b2 20,000 \\\\u099f\\\\u09bf\\\\u09b0\\\\u0993 \\\\u09ac\\\\u09c7\\\\u09b6\\\\u09bf \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0 \\\\u098f\\\\u09ac\\\\u0982 230,000 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09be\\\\u0995\\\\u09cd\\\\u09a4\\\\u09a8 \\\\u099b\\\\u09be\\\\u09a4\\\\u09cd\\\\u09b0\\\\u09a6\\\\u09c7\\\\u09b0 \\\\u09ac\\\\u09bf\\\\u09ad\\\\u09bf\\\\u09a8\\\\u09cd\\\\u09a8 \\\\u09a7\\\\u09b0\\\\u09a8\\\\u09c7\\\\u09b0 \\\\u0986\\\\u0997\\\\u09cd\\\\u09b0\\\\u09b9, \\\\u09ac\\\\u09af\\\\u09bc\\\\u09b8 \\\\u098f\\\\u09ac\\\\u0982 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09be\\\\u0995\\\\u0997\\\\u09cd\\\\u09b0\\\\u09be\\\\u0989\\\\u09a8\\\\u09cd\\\\u09a1 \\\\u09b8\\\\u09b9, \\\\u09ac\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09ac\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\\u099f\\\\u09bf \\\\u09b6\\\\u09b9\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u099c\\\\u09a8\\\\u09b8\\\\u0982\\\\u0996\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0 \\\\u0997\\\\u09a4\\\\u09bf\\\\u09b6\\\\u09c0\\\\u09b2 \\\\u09ae\\\\u09bf\\\\u09b6\\\\u09cd\\\\u09b0\\\\u09a3\\\\u0995\\\\u09c7 \\\\u09aa\\\\u09cd\\\\u09b0\\\\u09a4\\\\u09bf\\\\u09ab\\\\u09b2\\\\u09bf\\\\u09a4 \\\\u0995\\\\u09b0\\\\u09c7\\\\u0964\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,4,'bn','অনেস্ট স্কুলড এক্সপ্লোর করুন','\"আমরা জ্ঞান এবং অপরিহার্য দক্ষতা শিক্ষা করি\" একটি বাক্যাংশ যা উভয় তাত্ত্বিক জ্ঞানের গুরুত্বের উপর জোর দেয়','\"[{\\\"tab\\\":\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09ae\\\\u09cd\\\\u09aa\\\\u09be\\\\u09b8 \\\\u099c\\\\u09c0\\\\u09ac\\\\u09a8\\\",\\\"title\\\":\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09ae\\\\u09cd\\\\u09aa\\\\u09be\\\\u09b8 \\\\u099c\\\\u09c0\\\\u09ac\\\\u09a8\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"},{\\\"tab\\\":\\\"\\\\u098f\\\\u0995\\\\u09be\\\\u09a1\\\\u09c7\\\\u09ae\\\\u09bf\\\\u0995\\\",\\\"title\\\":\\\"\\\\u098f\\\\u0995\\\\u09be\\\\u09a1\\\\u09c7\\\\u09ae\\\\u09bf\\\\u0995\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"},{\\\"tab\\\":\\\"\\\\u0985\\\\u09cd\\\\u09af\\\\u09be\\\\u09a5\\\\u09b2\\\\u09c7\\\\u099f\\\\u09bf\\\\u0995\\\\u09cd\\\\u09b8\\\",\\\"title\\\":\\\"\\\\u0985\\\\u09cd\\\\u09af\\\\u09be\\\\u09a5\\\\u09b2\\\\u09c7\\\\u099f\\\\u09bf\\\\u0995\\\\u09cd\\\\u09b8\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"},{\\\"tab\\\":\\\"\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\",\\\"title\\\":\\\"\\\\u09ac\\\\u09bf\\\\u09a6\\\\u09cd\\\\u09af\\\\u09be\\\\u09b2\\\\u09af\\\\u09bc\\\",\\\"description\\\":\\\"\\\\u09a4\\\\u09be\\\\u09b0\\\\u09be \\\\u0996\\\\u09c1\\\\u09ac \\\\u09a8\\\\u09b0\\\\u09ae \\\\u098f\\\\u09ac\\\\u0982 \\\\u0995\\\\u09cb\\\\u09a5\\\\u09be\\\\u0993 \\\\u0995\\\\u09cb\\\\u09a8 \\\\u09ac\\\\u09cd\\\\u09af\\\\u09a5\\\\u09be \\\\u0986\\\\u099b\\\\u09c7 \\\\u099b\\\\u09c7\\\\u09a1\\\\u09bc\\\\u09c7 \\\\u09a8\\\\u09be. \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\\u0964 \\\\u09a4\\\\u09bf\\\\u09a8\\\\u09bf \\\\u09a4\\\\u09be\\\\u09b0 \\\\u09aa\\\\u09b0\\\\u09bf\\\\u09ac\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u09af\\\\u09a4\\\\u09cd\\\\u09a8 \\\\u09a8\\\\u09bf\\\\u09a4\\\\u09c7 \\\\u09aa\\\\u099b\\\\u09a8\\\\u09cd\\\\u09a6 \\\\u0995\\\\u09b0\\\\u09c7\\\\u09a8\\\\u0964 \\\\u0985\\\\u09a8\\\\u09c1\\\\u09b6\\\\u09c0\\\\u09b2\\\\u09a8 \\\\u09ab\\\\u09b2\\\\u09aa\\\\u09cd\\\\u09b0\\\\u09b8\\\\u09c2 \\\\u09b9\\\\u09ac\\\\u09c7\\\"}]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,5,'bn','শিক্ষাদান এবং শেখার ক্ষেত্রে শ্রেষ্ঠত্ব','স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া কিন্তু নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি দায়িত্বের দাবি বা ব্যবসার বাধ্যবাধকতাগুলির জন্য এটি প্রায়শই ঘটবে যে আনন্দ। অফুরন্ত সুযোগ প্রদান','\"[\\\"\\\\u0989\\\\u099a\\\\u09cd\\\\u099a \\\\u09b6\\\\u09bf\\\\u0995\\\\u09cd\\\\u09b7\\\\u09be\\\\u0997\\\\u09a4 \\\\u09af\\\\u09cb\\\\u0997\\\\u09cd\\\\u09af\\\\u09a4\\\\u09be\\\",\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0\\\\u09bf\\\\u09af\\\\u09bc\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09ad\\\\u09be\\\\u09b2\\\\u09cb \\\\u09b8\\\\u09ae\\\\u09cd\\\\u09ad\\\\u09be\\\\u09ac\\\\u09a8\\\\u09be\\\",\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0\\\\u09bf\\\\u09af\\\\u09bc\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09ad\\\\u09be\\\\u09b2\\\\u09cb \\\\u09b8\\\\u09ae\\\\u09cd\\\\u09ad\\\\u09be\\\\u09ac\\\\u09a8\\\\u09be\\\",\\\"\\\\u0995\\\\u09cd\\\\u09af\\\\u09be\\\\u09b0\\\\u09bf\\\\u09af\\\\u09bc\\\\u09be\\\\u09b0\\\\u09c7\\\\u09b0 \\\\u0986\\\\u09b0\\\\u0993 \\\\u09ad\\\\u09be\\\\u09b2\\\\u09cb \\\\u09b8\\\\u09ae\\\\u09cd\\\\u09ad\\\\u09be\\\\u09ac\\\\u09a8\\\\u09be\\\"]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,6,'bn','20+ একাডেমিক পাঠ্যক্রম','Onsest Schooled হল 20,000 টিরও বেশি ছাত্র এবং 230,000 প্রাক্তন ছাত্রদের বিভিন্ন ধরনের আগ্রহ, বয়স এবং ব্যাকগ্রাউন্ড সহ, বিশ্ববিদ্যালয়টি শহরের জনসংখ্যার গতিশীল মিশ্রণকে প্রতিফলিত করে।','\"[\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\",\\\"\\\\u09ac\\\\u09be\\\\u0982\\\\u09b2\\\\u09be \\\\u09ae\\\\u09be\\\\u09a7\\\\u09cd\\\\u09af\\\\u09ae\\\"]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,7,'bn','কি আসছে?','স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া তবে নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি কর্তব্য বা বাধ্যবাধকতার দাবিতে হবে।','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,8,'bn','আমাদের ব্লগ থেকে সর্বশেষ','স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া তবে নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি কর্তব্য বা বাধ্যবাধকতার দাবিতে হবে।','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,9,'bn','আমাদের গ্যালারি','স্বাগত জানাই প্রতিটি ব্যথা এড়িয়ে যাওয়া তবে নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি কর্তব্য বা বাধ্যবাধকতার দাবিতে হবে।','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,10,'bn','আমাদের যোগাযোগের তথ্য খুঁজুন','','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,11,'bn','বিভাগ দ্বারা যোগাযোগ','স্বাগত জানাই প্রতিটি কষ্টকে এড়িয়ে যাওয়া কিন্তু কিছু নির্দিষ্ট পরিস্থিতিতে ব্যবসার বাধ্যবাধকতার কারণে এটি দায়িত্বের দাবি বা ব্যবসার বাধ্যবাধকতার জন্য এটি করবে','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,12,'bn','আমাদের বৈশিষ্ট্যযুক্ত শিক্ষক','','\"[]\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `section_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'A',1,'2025-08-31 09:17:42','2025-08-31 09:17:42',1),(2,'B',1,'2025-08-31 09:18:01','2025-08-31 09:18:01',1),(3,'C',1,'2025-08-31 09:18:16','2025-08-31 09:18:16',1);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_class_students`
--

DROP TABLE IF EXISTS `session_class_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_class_students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `classes_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `shift_id` bigint unsigned DEFAULT NULL,
  `result` tinyint NOT NULL DEFAULT '1' COMMENT '0 = fail, 1 = pass',
  `roll` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `session_class_students_session_id_foreign` (`session_id`),
  KEY `session_class_students_student_id_foreign` (`student_id`),
  KEY `session_class_students_classes_id_foreign` (`classes_id`),
  KEY `session_class_students_section_id_foreign` (`section_id`),
  KEY `session_class_students_shift_id_foreign` (`shift_id`),
  CONSTRAINT `session_class_students_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `session_class_students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `session_class_students_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `session_class_students_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `session_class_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_class_students`
--

LOCK TABLES `session_class_students` WRITE;
/*!40000 ALTER TABLE `session_class_students` DISABLE KEYS */;
INSERT INTO `session_class_students` VALUES (31,1,31,1,1,1,1,'1','2025-09-01 02:38:41','2025-09-01 02:38:41',1),(32,1,32,2,1,1,1,'2','2025-09-01 02:38:41','2025-09-01 02:38:41',1),(33,1,33,3,1,1,1,'3','2025-09-01 02:38:41','2025-09-01 02:38:41',1),(34,1,34,4,1,2,1,'4','2025-09-01 02:38:41','2025-09-01 02:38:41',1),(35,1,35,5,1,2,1,'5','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(36,1,36,6,2,1,1,'6','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(37,1,37,7,1,1,1,'7','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(38,1,38,8,2,2,1,'8','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(39,1,39,9,1,1,1,'9','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(40,1,40,10,2,1,1,'10','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(41,1,41,11,1,2,1,'11','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(42,1,42,12,2,1,1,'12','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(43,1,43,13,1,2,1,'13','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(44,1,44,14,2,2,1,'14','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(45,1,45,15,1,2,1,'15','2025-09-01 02:38:42','2025-09-01 02:38:42',1),(46,1,46,16,2,1,1,'16','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(47,1,47,17,1,1,1,'17','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(48,1,48,18,2,1,1,'18','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(49,1,49,19,1,2,1,'19','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(50,1,50,20,3,1,1,'20','2025-09-01 02:38:43','2025-09-01 22:30:23',1),(51,1,51,21,3,1,1,'21','2025-09-01 02:38:43','2025-09-01 02:04:20',1),(52,1,52,22,2,2,1,'22','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(53,1,53,23,1,1,1,'23','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(54,1,54,24,3,1,1,'24','2025-09-01 02:38:43','2025-09-11 04:47:22',1),(55,1,55,25,1,1,1,'25','2025-09-01 02:38:43','2025-09-01 02:38:43',1),(56,1,56,26,3,2,1,'26','2025-09-01 02:38:44','2025-09-01 04:10:07',1),(57,1,57,1,1,1,1,'27','2025-09-01 02:38:44','2025-09-01 02:38:44',1),(58,1,58,2,1,1,1,'28','2025-09-01 02:38:44','2025-09-01 02:38:44',1),(59,1,59,3,1,1,1,'29','2025-09-01 02:38:44','2025-09-01 02:38:44',1),(60,1,60,4,1,1,1,'30','2025-09-01 02:38:44','2025-09-01 01:41:13',1),(61,1,61,1,1,1,1,'31','2025-09-01 00:32:41','2025-09-01 00:32:41',1),(62,1,62,14,2,1,1,'32','2025-09-10 22:48:27','2025-09-10 22:48:27',1),(63,1,63,11,2,1,1,'33','2025-09-11 03:58:45','2025-09-11 03:58:45',1),(71,1,71,11,2,1,1,'34','2025-09-11 05:05:39','2025-09-11 05:05:39',1);
/*!40000 ALTER TABLE `session_class_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_translates`
--

DROP TABLE IF EXISTS `session_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `session_translates_session_id_foreign` (`session_id`),
  CONSTRAINT `session_translates_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_translates`
--

LOCK TABLES `session_translates` WRITE;
/*!40000 ALTER TABLE `session_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,'2025','2025-01-01','2025-12-31',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'2026','2026-01-01','2026-12-31',1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting_translates`
--

DROP TABLE IF EXISTS `setting_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setting_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `setting_id` bigint unsigned DEFAULT NULL,
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_settings',
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `setting_translates_setting_id_foreign` (`setting_id`),
  CONSTRAINT `setting_translates_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting_translates`
--

LOCK TABLES `setting_translates` WRITE;
/*!40000 ALTER TABLE `setting_translates` DISABLE KEYS */;
INSERT INTO `setting_translates` VALUES (1,1,'general_settings','en','application_name','Ischool Management System','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,1,'general_settings','bn','application_name','ওনেস্ট স্কুলড - স্কুল ম্যানেজমেন্ট সিস্টেম','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,6,'general_settings','en','footer_text','© 2025 Ischool. All rights reserved.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,6,'general_settings','bn','footer_text','© 2025 Ischool','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,2,'general_settings','en','address','Resemont Tower, House 148, Road 13/B, Block E Banani Dhaka 1213.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,2,'general_settings','bn','address','রেসিমন্ট টাওয়ার, হাউজ 148, রোড 13/বি, ব্লক ই বনানী ঢাকা 1213।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,35,'general_settings','en','timezone','America/New_York','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,35,'general_settings','bn','timezone','+৬২ ৮৭৮৭ ৮৭৮৭','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,5,'general_settings','en','school_about','School Management Software (SMS) is a digital solution designed to simplify and automate administrative, academic, and operational tasks in educational institutions. It serves as a centralized platform to manage activities such as student records, attendance, fee collection, staff management, academic scheduling, and communication with parents.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,5,'general_settings','bn','school_about','Ischool Management System স্কুল ম্যানেজমেন্ট সফটওয়্যার (এসএমএস) হল একটি ডিজিটাল সমাধান যা শিক্ষা প্রতিষ্ঠানগুলিতে প্রশাসনিক, একাডেমিক এবং পরিচালনামূলক কাজগুলিকে সহজ এবং স্বয়ংক্রিয় করার জন্য ডিজাইন করা হয়েছে। এটি শিক্ষার্থীদের রেকর্ড, উপস্থিতি, ফি সংগ্রহ, কর্মী ব্যবস্থাপনা, একাডেমিক সময়সূচী এবং অভিভাবকদের সাথে যোগাযোগের মতো কার্যকলাপ পরিচালনা করার জন্য একটি কেন্দ্রীভূত প্ল্যাটফর্ম হিসেবে কাজ করে।','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `setting_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'application_name','\"Ischool Management System\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'address','\"Resemont Tower, House 148, Road 13\\/B, Block E Banani Dhaka 1213.\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'phone','\"+62 8787 8787\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'email','\"info@school.test\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'school_about','\"School Management Software (SMS) is a digital solution designed to simplify and automate administrative, academic, and operational tasks in educational institutions. It serves as a centralized platform to manage activities such as student records, attendance, fee collection, staff management, academic scheduling, and communication with parents.\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'footer_text','\"\\u00a9 2025 Ischool. All rights reserved.\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'file_system','\"local\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'aws_access_key_id','\"AKIA3OGN2RWSJOR5UOTK\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'aws_secret_key','\"Vz18p5ELHI6BP9K7iZAzduu+sQCD\\/KkvbAwElmfX\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'aws_region','\"ap-southeast-1\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'aws_bucket','\"Ischool\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'aws_endpoint','\"https:\\/\\/s3.ap-southeast-1.amazonaws.com\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,'twilio_account_sid','\"AC246311d660594a872734080bbb03a18b\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,'twilio_auth_token','\"9e64cc0f85970ab0d0f055f541861742\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,'twilio_phone_number','\"+14422426457\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,'recaptcha_sitekey','\"6Lfn6nQhAAAAAKYauxvLddLtcqSn1yqn-HRn_CbN\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,'recaptcha_secret','\"6Lfn6nQhAAAAABOzRtEjhZYB49Dd4orv41thfh02\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,'recaptcha_status','\"0\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,'mail_drive','\"smtp\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,'mail_host','\"smtp.gmail.com\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,'mail_address','\"info@school.test\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,'from_name','\"Ischool - School Management System\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,'mail_username','\"onestdev103@gmail.com\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,'mail_password','\"eyJpdiI6IjNwZzc3OU13YWVuamtWUUErdTVyMGc9PSIsInZhbHVlIjoieDh5T3dhUEs2cCsydENiS2NiWHYxQ3lUTks2aThlSldJTmFMMnM2L1dtbz0iLCJtYWMiOiJjYWFjYmU0YjE2MmRlNzIxNDU2ZTA4YjMwOGI3OWI5Yzc4NzA5NWY2Mzc5YmM5MWRiNjk0MmE1NGIwMWVjZjFlIiwidGFnIjoiIn0=\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(25,'mail_port','\"587\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(26,'encryption','\"tls\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(27,'default_langauge','\"en\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(28,'light_logo','\"backend\\/uploads\\/settings\\/light.png\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(29,'dark_logo','\"backend\\/uploads\\/settings\\/dark.png\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(30,'favicon','\"backend\\/uploads\\/settings\\/favicon.png\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(31,'session','1','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(32,'currency_code','\"USD\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(33,'map_key','\"!1m18!1m12!1m3!1d3650.776241229233!2d90.40412657620105!3d23.790981078642808!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c72b14773d9d%3A0x21df6643cbfa879f!2sSookh!5e0!3m2!1sen!2sbd!4v1711600654298!5m2!1sen!2sbd\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(34,'country','\"United States of America\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(35,'timezone','\"America\\/New_York\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(36,'tax_percentage','5','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(37,'tax_income_head','\"Income Tax\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(38,'tax_min_amount','\"10000\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(39,'tax_max_amount','\"1000000\"','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(40,'early_payment_discount_applicable','0','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(41,'siblings_discount_applicable','0','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(42,'use_enhanced_fee_system','1',NULL,NULL,1);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shift_translates`
--

DROP TABLE IF EXISTS `shift_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shift_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `shift_translates_shift_id_foreign` (`shift_id`),
  CONSTRAINT `shift_translates_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shift_translates`
--

LOCK TABLES `shift_translates` WRITE;
/*!40000 ALTER TABLE `shift_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `shift_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,'Morning',1,'2025-08-31 09:16:19','2025-08-31 09:16:19',1),(2,'Afternoon',1,'2025-08-31 09:16:33','2025-08-31 09:16:33',1);
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sibling_fees_discounts`
--

DROP TABLE IF EXISTS `sibling_fees_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sibling_fees_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `discount_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `siblings_number` int DEFAULT NULL,
  `discount_percentage` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sibling_fees_discounts`
--

LOCK TABLES `sibling_fees_discounts` WRITE;
/*!40000 ALTER TABLE `sibling_fees_discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `sibling_fees_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slider_translates`
--

DROP TABLE IF EXISTS `slider_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slider_translates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slider_id` bigint unsigned DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `slider_translates_slider_id_foreign` (`slider_id`),
  CONSTRAINT `slider_translates_slider_id_foreign` FOREIGN KEY (`slider_id`) REFERENCES `sliders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slider_translates`
--

LOCK TABLES `slider_translates` WRITE;
/*!40000 ALTER TABLE `slider_translates` DISABLE KEYS */;
INSERT INTO `slider_translates` VALUES (1,1,'en','Let’s Build Your Future With Onest Shooled 1','Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 1.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,2,'en','Let’s Build Your Future With Onest Shooled 2','Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 2.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,3,'en','Let’s Build Your Future With Onest Shooled 3','Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 3.','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,1,'bn','আসুন Oneest Shooled 1 দিয়ে আপনার ভবিষ্যত গড়ে তুলি','চমৎকার পরিবেশ যেখানে শিশুরা শ্রমসাধ্য শারীরিক শিক্ষা গ্রহণ করে এবং বড় হয়। আমেট নরম, তারা কোথাও ছেড়ে যায় না, কিছু ব্যথা হতে দিন।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,2,'bn','আসুন Oneest Shooled 2 দিয়ে আপনার ভবিষ্যত গড়ে তুলি','চমৎকার পরিবেশ যেখানে শিশুরা শ্রমসাধ্য শারীরিক শিক্ষা গ্রহণ করে এবং বড় হয়। আমেত একটুও হাল ছাড়ে না, তারা কোথাও ছাড়ে না, কিছু ব্যথা থাকুক।','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,3,'bn','আসুন Oneest Shooled 3 দিয়ে আপনার ভবিষ্যত গড়ে তুলি','চমৎকার পরিবেশ যেখানে শিশুরা শ্রমসাধ্য শারীরিক শিক্ষা গ্রহণ করে এবং বড় হয়। তারা আমাকে একা ছেড়ে যায় না।','2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `slider_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sliders`
--

DROP TABLE IF EXISTS `sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sliders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `serial` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sliders_upload_id_foreign` (`upload_id`),
  CONSTRAINT `sliders_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
INSERT INTO `sliders` VALUES (1,'Let’s Build Your Future With Onest Shooled 1',11,'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 1.',0,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'Let’s Build Your Future With Onest Shooled 2',12,'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 2.',1,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'Let’s Build Your Future With Onest Shooled 3',13,'Wonderful environment where children undertakes laborious physical learn and grow. Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sin 3.',2,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_mail_logs`
--

DROP TABLE IF EXISTS `sms_mail_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_mail_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('mail','sms') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sms_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_type` enum('role','individual','class') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `role_id` int DEFAULT NULL,
  `individual_user_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `class_id` int DEFAULT NULL,
  `section_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_mail_logs`
--

LOCK TABLES `sms_mail_logs` WRITE;
/*!40000 ALTER TABLE `sms_mail_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_mail_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_mail_templates`
--

DROP TABLE IF EXISTS `sms_mail_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_mail_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('mail','sms') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` bigint unsigned DEFAULT NULL,
  `mail_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sms_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sms_mail_templates_attachment_foreign` (`attachment`),
  CONSTRAINT `sms_mail_templates_attachment_foreign` FOREIGN KEY (`attachment`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_mail_templates`
--

LOCK TABLES `sms_mail_templates` WRITE;
/*!40000 ALTER TABLE `sms_mail_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_mail_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `staff_id` int DEFAULT NULL,
  `role_id` bigint unsigned NOT NULL,
  `designation_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender_id` bigint unsigned NOT NULL,
  `dob` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `upload_id` bigint unsigned DEFAULT NULL,
  `current_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_salary` int DEFAULT NULL,
  `upload_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `staff_user_id_foreign` (`user_id`),
  KEY `staff_role_id_foreign` (`role_id`),
  KEY `staff_designation_id_foreign` (`designation_id`),
  KEY `staff_department_id_foreign` (`department_id`),
  KEY `staff_gender_id_foreign` (`gender_id`),
  KEY `staff_upload_id_foreign` (`upload_id`),
  CONSTRAINT `staff_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,87,1001,5,6,4,'Ahmed','Hassan','Hassan Abdi','Amina Ahmed','ahmed.hassan@school.edu.so',1,'1985-01-01','2025-09-01','252634001001','252634001001',1,1,NULL,'Jigiga Yare, Hargeisa','Jigiga Yare, Hargeisa',35000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(2,88,1002,5,6,4,'Fatima','Osman','Osman Mohamed','Khadija Ibrahim','fatima.osman@school.edu.so',2,'1985-01-01','2025-09-01','252634002002','252634002002',1,1,NULL,'Ahmed Dhagah, Hargeisa','Ahmed Dhagah, Hargeisa',32000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(3,89,1003,5,6,5,'Ibrahim','Ali','Ali Yusuf','Sahra Hassan','ibrahim.ali@school.edu.so',1,'1985-01-01','2025-09-01','252634003003','252634003003',1,1,NULL,'Ga\'an Libah, Burao','Ga\'an Libah, Burao',33000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(4,90,1004,5,6,6,'Amina','Mohamed','Mohamed Ismail','Halima Omar','amina.mohamed@school.edu.so',2,'1985-01-01','2025-09-01','252634004004','252634004004',1,1,NULL,'Masalaha, Berbera','Masalaha, Berbera',34000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(5,91,1005,5,6,7,'Omar','Abdi','Abdi Ahmed','Mariam Ali','omar.abdi@school.edu.so',1,'1985-01-01','2025-09-01','252634005005','252634005005',1,1,NULL,'Dilla, Borama','Dilla, Borama',31000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(6,92,1006,5,6,8,'Khadija','Yusuf','Yusuf Hassan','Habiba Mohamed','khadija.yusuf@school.edu.so',2,'1985-01-01','2025-09-01','252634006006','252634006006',1,1,NULL,'Sheikh Madar, Sheikh','Sheikh Madar, Sheikh',36000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(7,93,1007,5,6,4,'Yusuf','Ibrahim','Ibrahim Omar','Zeinab Ali','yusuf.ibrahim@school.edu.so',1,'1985-01-01','2025-09-01','252634007007','252634007007',1,1,NULL,'Wadajir, Hargeisa','Wadajir, Hargeisa',30000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(8,94,1008,5,6,4,'Saeed','Mohamed','Mohamed Hassan','Aisha Abdi','saeed.mohamed@school.edu.so',1,'1985-01-01','2025-09-01','252634008008','252634008008',1,1,NULL,'Gacan Libaax, Erigabo','Gacan Libaax, Erigabo',42000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(9,95,1009,5,6,8,'Hodan','Ahmed','Ahmed Ali','Fadumo Hassan','hodan.ahmed@school.edu.so',2,'1985-01-01','2025-09-01','252634009009','252634009009',1,1,NULL,'Taleex, Las Anod','Taleex, Las Anod',45000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(10,96,1010,5,6,6,'Hassan','Omar','Omar Ibrahim','Safia Mohamed','hassan.omar@school.edu.so',1,'1985-01-01','2025-09-01','252634010010','252634010010',1,1,NULL,'Port Area, Zeila','Port Area, Zeila',48000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(11,97,1011,5,6,6,'Mariam','Ismail','Ismail Yusuf','Naima Ahmed','mariam.ismail@school.edu.so',2,'1985-01-01','2025-09-01','252634011011','252634011011',1,1,NULL,'October, Hargeisa','October, Hargeisa',47000,'[]','2025-09-01 03:13:16','2025-09-01 03:13:16',1),(12,98,1012,5,6,6,'Abdi','Hassan','Hassan Mohamed','Asha Ibrahim','abdi.hassan@school.edu.so',1,'1985-01-01','2025-09-01','252634012012','252634012012',1,1,NULL,'New Hargeisa, Hargeisa','New Hargeisa, Hargeisa',46000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(13,99,1013,5,6,5,'Sahra','Ali','Ali Ahmed','Hawa Omar','sahra.ali@school.edu.so',2,'1985-01-01','2025-09-01','252634013013','252634013013',1,1,NULL,'Darasalaam, Borama','Darasalaam, Borama',38000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(14,100,1014,5,6,4,'Ismail','Abdi','Abdi Osman','Faduma Hassan','ismail.abdi@school.edu.so',1,'1985-01-01','2025-09-01','252634014014','252634014014',1,1,NULL,'Mohamed Moge, Hargeisa','Mohamed Moge, Hargeisa',35000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(15,101,1015,5,6,7,'Habiba','Omar','Omar Ali','Amran Mohamed','habiba.omar@school.edu.so',2,'1985-01-01','2025-09-01','252634015015','252634015015',1,1,NULL,'Shacabka, Burao','Shacabka, Burao',37000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(16,102,1016,5,6,7,'Ali','Mohamed','Mohamed Yusuf','Rukia Hassan','ali.mohamed@school.edu.so',1,'1985-01-01','2025-09-01','252634016016','252634016016',1,1,NULL,'Laas Geel, Berbera','Laas Geel, Berbera',36000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(17,103,1017,5,6,4,'Zeinab','Ahmed','Ahmed Hassan','Cawo Ali','zeinab.ahmed@school.edu.so',2,'1985-01-01','2025-09-01','252634017017','252634017017',1,1,NULL,'Salahley, Maroodi Jeeh','Salahley, Maroodi Jeeh',34000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(18,104,1018,2,7,9,'Mohamed','Hassan','Hassan Ali','Maryam Omar','mohamed.hassan@school.edu.so',1,'1985-01-01','2025-09-01','252634018018','252634018018',1,1,NULL,'Central District, Hargeisa','Central District, Hargeisa',65000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(19,105,1019,5,8,9,'Halima','Ibrahim','Ibrahim Ahmed','Shamis Hassan','halima.ibrahim@school.edu.so',2,'1985-01-01','2025-09-01','252634019019','252634019019',1,1,NULL,'University District, Hargeisa','University District, Hargeisa',55000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(20,106,1020,5,9,9,'Abdullahi','Osman','Osman Abdi','Hodan Mohamed','abdullahi.osman@school.edu.so',1,'1985-01-01','2025-09-01','252634020020','252634020020',1,1,NULL,'Jigjiga Yare, Hargeisa','Jigjiga Yare, Hargeisa',50000,'[]','2025-09-01 03:13:17','2025-09-01 03:13:17',1),(21,107,1021,2,2,9,'Sh.Xamse','saalax','abdilaahi','maryam','xamse@somaliland.school.edu.so',1,'1996-01-01','2020-06-01','0634040505','',1,1,NULL,'Hargeisa','Hargeisa,Sh.madar',750,'[]','2025-08-31 23:41:56','2025-09-01 01:01:01',1);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_absent_notifications`
--

DROP TABLE IF EXISTS `student_absent_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_absent_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notify_student` tinyint(1) NOT NULL DEFAULT '0',
  `notify_gurdian` tinyint(1) NOT NULL DEFAULT '1',
  `sending_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `notification_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_absent_notifications`
--

LOCK TABLES `student_absent_notifications` WRITE;
/*!40000 ALTER TABLE `student_absent_notifications` DISABLE KEYS */;
INSERT INTO `student_absent_notifications` VALUES (1,0,1,'10:00',1,'Hi [guardian_name] , your child [student_name] on class [class] - ([section]) Admission [admission_no] is [attendance_type] on [attendance_date]  . For more contact [school_name]','2025-06-03 07:04:07','2025-06-03 07:04:07',1);
/*!40000 ALTER TABLE `student_absent_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_categories`
--

DROP TABLE IF EXISTS `student_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_categories`
--

LOCK TABLES `student_categories` WRITE;
/*!40000 ALTER TABLE `student_categories` DISABLE KEYS */;
INSERT INTO `student_categories` VALUES (1,'Normal',1,'2025-08-31 22:48:04','2025-09-01 22:28:03',1),(2,'Scholership',1,'2025-08-31 22:48:48','2025-09-01 22:28:13',1);
/*!40000 ALTER TABLE `student_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_services`
--

DROP TABLE IF EXISTS `student_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `fee_type_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `amount` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT 'Custom amount - can override fees_types.amount',
  `due_date` date DEFAULT NULL COMMENT 'Calculated or custom due date for this service',
  `discount_type` enum('none','percentage','fixed','override') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none' COMMENT 'Type of discount applied to this service',
  `discount_value` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount amount or percentage value',
  `final_amount` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT 'Final calculated amount after applying discounts',
  `subscription_date` timestamp NULL DEFAULT NULL COMMENT 'When this service was assigned to the student',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether this service subscription is currently active',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Reason for discount, special conditions, or admin notes',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_student_services_student_type_year` (`student_id`,`fee_type_id`,`academic_year_id`),
  KEY `student_services_created_by_foreign` (`created_by`),
  KEY `student_services_updated_by_foreign` (`updated_by`),
  KEY `idx_student_services_student_year` (`student_id`,`academic_year_id`),
  KEY `idx_student_services_type_active` (`fee_type_id`,`is_active`),
  KEY `idx_student_services_active_due` (`is_active`,`due_date`),
  KEY `idx_student_services_year_active` (`academic_year_id`,`is_active`),
  KEY `idx_student_services_subscription_date` (`subscription_date`),
  CONSTRAINT `student_services_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_services_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_services_fee_type_id_foreign` FOREIGN KEY (`fee_type_id`) REFERENCES `fees_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_services_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_services_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_services`
--

LOCK TABLES `student_services` WRITE;
/*!40000 ALTER TABLE `student_services` DISABLE KEYS */;
INSERT INTO `student_services` VALUES (1,31,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(2,34,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(3,34,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(4,35,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(5,36,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(6,37,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(7,37,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(8,38,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(9,38,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(10,39,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(11,39,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(12,40,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 03:55:09',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 03:55:09'),(13,41,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(14,41,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(15,42,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(16,42,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(17,43,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(18,43,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(19,44,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(20,45,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(21,45,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(22,46,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(23,47,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(24,48,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(25,48,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(26,49,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(27,49,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(28,50,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(29,50,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(30,51,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(31,51,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(32,52,3,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(33,52,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 07:39:55',1,'Assigned based on existing fee types',1,NULL,'2025-09-10 07:39:55','2025-09-10 07:39:55'),(34,40,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 03:55:09',1,NULL,1,NULL,'2025-09-10 03:55:09','2025-09-10 03:55:09'),(35,60,1,1,30.00,'2025-10-10','none',0.00,30.00,'2025-09-10 04:00:52',1,NULL,1,NULL,'2025-09-10 04:00:52','2025-09-10 04:00:52'),(36,60,2,1,15.00,'2025-10-10','none',0.00,15.00,'2025-09-10 04:00:52',1,NULL,1,NULL,'2025-09-10 04:00:52','2025-09-10 04:00:52'),(37,62,3,1,15.00,'2025-10-11','none',0.00,15.00,'2025-09-11 01:04:15',1,'Automatically assigned mandatory service',1,NULL,'2025-09-10 22:48:27','2025-09-11 01:04:15'),(38,63,3,1,15.00,'2025-10-31','none',0.00,15.00,'2025-09-11 03:59:47',1,'Automatically assigned mandatory service',1,NULL,'2025-09-11 03:58:45','2025-09-11 03:59:47'),(54,71,3,1,15.00,'2025-10-31','none',0.00,15.00,'2025-09-11 05:05:39',1,'Automatically assigned mandatory service',1,NULL,'2025-09-11 05:05:39','2025-09-11 05:05:39'),(55,71,1,1,30.00,'2025-09-11','none',0.00,30.00,'2025-09-11 05:05:39',1,NULL,1,NULL,'2025-09-11 05:05:39','2025-09-11 05:05:39');
/*!40000 ALTER TABLE `student_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admission_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roll_no` int DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `student_category_id` bigint unsigned DEFAULT NULL,
  `religion_id` bigint unsigned DEFAULT NULL,
  `blood_group_id` bigint unsigned DEFAULT NULL,
  `gender_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `image_id` bigint unsigned DEFAULT NULL,
  `parent_guardian_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `upload_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `siblings_discount` tinyint NOT NULL DEFAULT '0',
  `previous_school` tinyint NOT NULL DEFAULT '0',
  `previous_school_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `previous_school_image_id` bigint unsigned DEFAULT NULL,
  `health_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_in_family` int NOT NULL DEFAULT '1',
  `siblings` int NOT NULL DEFAULT '0',
  `place_of_birth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpr_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spoken_lang_at_home` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residance_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_ar_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id_certificate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `students_religion_id_foreign` (`religion_id`),
  KEY `students_blood_group_id_foreign` (`blood_group_id`),
  KEY `students_gender_id_foreign` (`gender_id`),
  KEY `students_category_id_foreign` (`category_id`),
  KEY `students_image_id_foreign` (`image_id`),
  KEY `students_parent_guardian_id_foreign` (`parent_guardian_id`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_department_id_foreign` (`department_id`),
  KEY `students_previous_school_image_id_foreign` (`previous_school_image_id`),
  CONSTRAINT `students_blood_group_id_foreign` FOREIGN KEY (`blood_group_id`) REFERENCES `blood_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `students_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_parent_guardian_id_foreign` FOREIGN KEY (`parent_guardian_id`) REFERENCES `parent_guardians` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_previous_school_image_id_foreign` FOREIGN KEY (`previous_school_image_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (31,'20251001',1,'Ayan','Farah','+252 65 8349330','ayanfarah631@student.somaliland.edu','2015-07-16','2025-03-01',1,1,3,2,NULL,NULL,16,57,2,'[]',1,0,1,'Sheikh Technical School',NULL,'Excellent',2,4,'Las Anod, Somaliland','Somaliland','SL884121899','Somali','Kalabaydh District, Las Anod, Somaliland','2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,NULL,NULL,1),(32,'20252002',2,'Abdirashid','Abdullahi','+252 65 3165603','abdirashidabdullahi357@student.somaliland.edu','2014-10-16','2025-06-01',1,1,4,1,NULL,NULL,23,58,2,'[]',0,0,1,'Borama Community School',NULL,'Fair',3,2,'Zeila, Somaliland','Somaliland','SL299622582','Somali','Daami District, Zeila, Somaliland','2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,NULL,NULL,1),(33,'20253003',3,'Omar','Saeed','+252 65 7670477','omarsaeed858@student.somaliland.edu','2015-04-03','2025-07-01',1,1,5,1,NULL,NULL,16,59,2,'[]',0,0,0,'Sheikh Technical School',NULL,'Excellent',3,3,'Wajaale, Somaliland','Somaliland','SL234570606','Somali','Daami District, Wajaale, Somaliland','2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,NULL,NULL,1),(34,'20254004',4,'Mohamed','Saeed','+252 64 5863079','mohamedsaeed201@student.somaliland.edu','2015-05-02','2024-10-01',1,1,5,1,NULL,NULL,22,60,2,'[]',1,0,1,'Berbera Intermediate School',NULL,'Fair',1,2,'Erigavo, Somaliland','Somaliland','SL994794717','Somali','Daami District, Erigavo, Somaliland','2025-09-01 02:38:41','2025-09-01 02:38:41',NULL,NULL,NULL,NULL,1),(35,'20255005',5,'Khadija','Ibrahim','','khadijaibrahim809@student.somaliland.edu','2014-12-03','2024-12-01',1,1,1,2,NULL,NULL,21,61,2,'[]',1,0,1,'Berbera Intermediate School',NULL,'Good',5,4,'Zeila, Somaliland','Somaliland','SL174922166','Somali','Ibrahim Kodbuur District, Zeila, Somaliland','2025-09-01 02:38:42','2025-09-04 03:56:31',NULL,NULL,NULL,NULL,1),(36,'20256006',6,'Farah','Saeed','+252 64 1079143','farahsaeed360@student.somaliland.edu','2014-11-09','2025-04-01',1,1,3,1,NULL,NULL,14,62,2,'[]',1,0,1,'Hargeisa Primary School',NULL,'Good',5,3,'Berbera, Somaliland','Somaliland','SL435952407','Somali','Kalabaydh District, Berbera, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(37,'20257007',7,'Ahmed','Hussein','+252 64 2910472','ahmedhussein694@student.somaliland.edu','2015-07-14','2024-11-01',1,1,8,1,NULL,NULL,20,63,2,'[]',1,0,0,'Sheikh Technical School',NULL,'Excellent',2,2,'Hargeisa, Somaliland','Somaliland','SL695477610','Somali','October District, Hargeisa, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(38,'20258008',8,'Caasha','Mohamed','+252 64 2391537','caashamohamed175@student.somaliland.edu','2015-02-22','2024-10-01',2,1,3,2,NULL,NULL,17,64,2,'[]',1,0,0,'Berbera Intermediate School',NULL,'Fair',5,4,'Sheikh, Somaliland','Somaliland','SL934300884','Somali','Gacan Libaax District, Sheikh, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(39,'20259009',9,'Ismail','Ibrahim','+252 64 4494182','ismailibrahim765@student.somaliland.edu','2015-02-17','2025-05-01',1,1,7,1,NULL,NULL,21,65,2,'[]',1,0,0,'Ahmed Dhagah Elementary',NULL,'Fair',3,1,'Erigavo, Somaliland','Somaliland','SL886372883','Somali','Maroodi Jeex District, Erigavo, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(40,'202510010',10,'Ali','Saeed','','alisaeed695@student.somaliland.edu','2014-09-17','2024-11-01',1,1,5,1,NULL,NULL,24,66,2,'[]',1,0,0,NULL,NULL,'Fair',5,1,'Wajaale, Somaliland','Somaliland','SL490972109','Somali','Kalabaydh District, Wajaale, Somaliland','2025-09-01 02:38:42','2025-09-10 03:53:08',NULL,NULL,NULL,NULL,1),(41,'202511011',11,'Abdirashid','Ali','+252 64 3929976','abdirashidali813@student.somaliland.edu','2015-03-08','2025-04-01',1,1,4,1,NULL,NULL,21,67,2,'[]',1,0,0,'International School of Somaliland',NULL,'Excellent',5,3,'Burao, Somaliland','Somaliland','SL258073381','Somali','Ibrahim Kodbuur District, Burao, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(42,'202512012',12,'Saynab','Hussein','','saynabhussein746@student.somaliland.edu','2015-07-21','2025-03-01',1,1,3,2,NULL,NULL,18,68,2,'[]',1,0,1,'Burao Secondary School',NULL,'Excellent',1,3,'Caynabo, Somaliland','Somaliland','SL911728435','Somali','Kalabaydh District, Caynabo, Somaliland','2025-09-01 02:38:42','2025-09-05 23:11:46',NULL,NULL,NULL,NULL,1),(43,'202513013',13,'Dahir','Farah','+252 63 3924161','dahirfarah766@student.somaliland.edu','2015-04-17','2025-06-01',1,1,1,1,NULL,NULL,20,69,2,'[]',1,0,0,'International School of Somaliland',NULL,'Fair',4,4,'Caynabo, Somaliland','Somaliland','SL383788236','Somali','Gacan Libaax District, Caynabo, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(44,'202514014',14,'Maxamed','Ismail','','maxamedismail734@student.somaliland.edu','2015-05-05','2024-10-01',1,1,8,1,NULL,NULL,15,70,2,'[]',1,0,0,NULL,NULL,'Excellent',5,4,'Las Anod, Somaliland','Somaliland','SL649736765','Somali','Jigaale District, Las Anod, Somaliland','2025-09-01 02:38:42','2025-09-05 23:53:47',NULL,NULL,NULL,NULL,1),(45,'202515015',15,'Ikraan','Abdi','+252 63 9319043','ikraanabdi564@student.somaliland.edu','2015-06-10','2024-11-01',1,1,2,2,NULL,NULL,17,71,2,'[]',1,0,1,'Berbera Intermediate School',NULL,'Good',3,0,'Burao, Somaliland','Somaliland','SL578131717','Somali','Ibrahim Kodbuur District, Burao, Somaliland','2025-09-01 02:38:42','2025-09-01 02:38:42',NULL,NULL,NULL,NULL,1),(46,'202516016',16,'Jama','Omar','+252 64 1393935','jamaomar512@student.somaliland.edu','2015-08-26','2025-04-01',2,1,7,1,NULL,NULL,17,72,1,'[]',1,0,0,'Burao Secondary School',NULL,'Good',5,0,'Sheikh, Somaliland','Somaliland','SL654887145','Somali','Maroodi Jeex District, Sheikh, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(47,'202517017',17,'Yusuf','Dahir','+252 63 4229070','yusufdahir309@student.somaliland.edu','2014-12-13','2024-12-01',1,1,7,1,NULL,NULL,14,73,1,'[]',1,0,1,'International School of Somaliland',NULL,'Excellent',2,1,'Erigavo, Somaliland','Somaliland','SL176580763','Somali','Maxamed Haybe District, Erigavo, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(48,'202518018',18,'Abdirashid','Omar','+252 64 8539642','abdirashidomar148@student.somaliland.edu','2015-06-24','2024-09-01',2,1,3,1,NULL,NULL,13,74,1,'[]',1,0,0,'Ahmed Dhagah Elementary',NULL,'Excellent',1,2,'Hargeisa, Somaliland','Somaliland','SL558617848','Somali','Mohamed Moge District, Hargeisa, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(49,'202519019',19,'Ayan','Ismail','+252 64 9409863','ayanismail981@student.somaliland.edu','2015-02-13','2025-02-01',1,1,2,2,NULL,NULL,14,75,1,'[]',1,0,1,'Berbera Intermediate School',NULL,'Excellent',5,4,'Hargeisa, Somaliland','Somaliland','SL851784055','Somali','Gacan Libaax District, Hargeisa, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(50,'202520020',20,'Maryan','Ali','4445564','maryanali687@student.somaliland.edu','2014-11-13','2025-05-01',2,1,2,2,NULL,NULL,18,76,1,'[]',1,0,1,'Borama Community School',NULL,'Fair',2,2,'Hargeisa, Somaliland','Somaliland','SL537622885','Somali','Shacab District, Hargeisa, Somaliland','2025-09-01 02:38:43','2025-09-01 22:30:23',NULL,NULL,NULL,NULL,1),(51,'202521021',21,'Dahir','Hussein','','dahirhussein376@student.somaliland.edu','2014-11-09','2025-04-01',1,1,1,1,NULL,72,21,77,1,'[]',1,0,1,'Berbera Intermediate School',NULL,'Good',5,3,'Hargeisa, Somaliland','Somaliland','SL443421089','Somali','Jigaale District, Hargeisa, Somaliland','2025-09-01 02:38:43','2025-09-02 00:41:14',NULL,NULL,NULL,NULL,1),(52,'202522022',22,'Osman','Yusuf','+252 63 6798868','osmanyusuf622@student.somaliland.edu','2015-06-15','2024-09-01',1,1,6,1,NULL,NULL,20,78,1,'[]',1,0,0,'Burao Secondary School',NULL,'Fair',5,1,'Gabiley, Somaliland','Somaliland','SL780637475','Somali','Gacan Libaax District, Gabiley, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(53,'202523023',23,'Ali','Maxamed','+252 65 8068461','alimaxamed363@student.somaliland.edu','2015-08-12','2025-07-01',1,1,6,1,NULL,NULL,24,79,1,'[]',1,0,1,'Borama Community School',NULL,'Good',5,4,'Sheikh, Somaliland','Somaliland','SL437624653','Somali','Daami District, Sheikh, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(54,'202524024',24,'Saeed','Maxamed','','saeedmaxamed816@student.somaliland.edu','2015-01-05','2025-03-01',1,1,2,1,NULL,NULL,19,80,1,'[]',1,0,1,'Ahmed Dhagah Elementary',NULL,'Good',5,3,'Burao, Somaliland','Somaliland','SL235813101','Somali','Mohamed Moge District, Burao, Somaliland','2025-09-01 02:38:43','2025-09-11 04:47:22',NULL,NULL,NULL,NULL,1),(55,'202525025',25,'Axmed','Farah','+252 63 4563312','axmedfarah803@student.somaliland.edu','2015-08-30','2024-12-01',1,1,1,1,NULL,NULL,17,81,1,'[]',1,0,0,'Ahmed Dhagah Elementary',NULL,'Fair',4,2,'Wajaale, Somaliland','Somaliland','SL732102109','Somali','Jigaale District, Wajaale, Somaliland','2025-09-01 02:38:43','2025-09-01 02:38:43',NULL,NULL,NULL,NULL,1),(56,'202526026',26,'Abdullahi','Mohamed','','abdullahimohamed131@student.somaliland.edu','2015-01-09','2025-04-01',1,1,7,1,NULL,70,14,82,1,'[]',1,0,0,NULL,NULL,'Excellent',4,4,'Las Anod, Somaliland','Somaliland','SL888826945','Somali','Jigaale District, Las Anod, Somaliland','2025-09-01 02:38:44','2025-09-10 03:15:35',NULL,NULL,NULL,NULL,1),(57,'20251027',27,'Hodan','Ali','+252 63 3183997','hodanali207@student.somaliland.edu','2014-10-11','2025-01-01',1,1,7,2,NULL,NULL,15,83,1,'[]',1,0,0,'International School of Somaliland',NULL,'Excellent',5,1,'Gabiley, Somaliland','Somaliland','SL816008346','Somali','Ahmed Dhagah District, Gabiley, Somaliland','2025-09-01 02:38:44','2025-09-01 02:38:44',NULL,NULL,NULL,NULL,1),(58,'20252028',28,'Cali','Farah','','califarah885@student.somaliland.edu','2014-12-07','2025-05-01',1,1,8,1,NULL,71,18,84,1,'[]',1,0,0,NULL,NULL,'Good',4,1,'Berbera, Somaliland','Somaliland','SL319770467','Somali','Mohamed Moge District, Berbera, Somaliland','2025-09-01 02:38:44','2025-09-01 21:38:53',NULL,NULL,NULL,NULL,1),(59,'20253029',29,'Halima','Yusuf','+252 65 1066013','halimayusuf957@student.somaliland.edu','2015-01-29','2025-05-01',1,1,2,2,NULL,NULL,13,85,1,'[]',1,0,1,'Hargeisa Primary School',NULL,'Fair',1,3,'Borama, Somaliland','Somaliland','SL982437375','Somali','Shacab District, Borama, Somaliland','2025-09-01 02:38:44','2025-09-01 02:38:44',NULL,NULL,NULL,NULL,1),(60,'20254030',30,'Halima','Guuleed','0637260033','halimasaeed821@student.somaliland.edu','2014-11-26','2024-11-01',2,1,6,2,NULL,NULL,17,86,1,'[]',1,0,1,'Ahmed Dhagah Elementary',NULL,'Good',1,3,'Caynabo, Somaliland','Somaliland','SL714773542','Somali','Maroodi Jeex District, Caynabo, Somaliland','2025-09-01 02:38:44','2025-09-01 03:40:06',NULL,NULL,NULL,NULL,1),(61,'20254031',31,'Iksiir','Ali Jimcale','0639837847','iksiir@school.edu.so','2002-07-01','2025-09-01',1,1,1,1,NULL,NULL,13,108,2,'[]',1,0,0,NULL,NULL,'Fair',1,5,'','','','','','2025-09-01 00:32:41','2025-09-01 01:25:08',NULL,NULL,NULL,NULL,1),(62,'20254032',32,'Ridwan','Ahmed','0633353722','ridwan@gmail.com','2003-06-11','2025-09-11',1,1,5,1,NULL,NULL,19,109,2,'[]',1,0,0,NULL,NULL,'Good',1,0,'','Somaliland','SL714773542','','Hargeisa','2025-09-10 22:48:27','2025-09-11 01:03:29',NULL,NULL,NULL,NULL,1),(63,'20254033',33,'Hana','Yusuf','3234433','hana@gmail.com','2013-06-11','2025-09-11',1,1,2,2,NULL,NULL,17,110,2,'[]',1,0,0,NULL,NULL,'Good',1,0,'Hargeisa','Hargeisa','','','Hargeisa','2025-09-11 03:58:44','2025-09-11 03:59:47',NULL,NULL,NULL,NULL,1),(71,'20254034',34,'Nasriin','Ibrahim','','nasriin@gmail.com','2012-06-11','2025-09-11',1,1,5,2,NULL,NULL,15,118,2,'[]',1,0,0,'',NULL,'Good',1,0,'Hargeisa','','','','Hargeisa','2025-09-11 05:05:39','2025-09-11 05:05:39',NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_assign_childrens`
--

DROP TABLE IF EXISTS `subject_assign_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_assign_childrens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_assign_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `staff_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `subject_assign_childrens_subject_assign_id_foreign` (`subject_assign_id`),
  KEY `subject_assign_childrens_subject_id_foreign` (`subject_id`),
  KEY `subject_assign_childrens_staff_id_foreign` (`staff_id`),
  CONSTRAINT `subject_assign_childrens_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_assign_childrens_subject_assign_id_foreign` FOREIGN KEY (`subject_assign_id`) REFERENCES `subject_assigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_assign_childrens_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_assign_childrens`
--

LOCK TABLES `subject_assign_childrens` WRITE;
/*!40000 ALTER TABLE `subject_assign_childrens` DISABLE KEYS */;
INSERT INTO `subject_assign_childrens` VALUES (1,1,1,5,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(2,1,2,20,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(3,1,7,1,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(4,1,3,10,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(5,1,16,14,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(6,1,15,14,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(7,1,12,6,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(8,1,10,17,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(9,1,11,12,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(10,1,9,11,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(11,2,1,7,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(12,2,2,14,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(13,2,7,17,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(14,2,3,8,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(15,2,16,11,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(16,2,15,3,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(17,2,11,4,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(18,2,12,14,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(19,2,10,19,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1),(20,2,9,10,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1);
/*!40000 ALTER TABLE `subject_assign_childrens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_assigns`
--

DROP TABLE IF EXISTS `subject_assigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_assigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `classes_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `subject_assigns_session_id_foreign` (`session_id`),
  KEY `subject_assigns_classes_id_foreign` (`classes_id`),
  KEY `subject_assigns_section_id_foreign` (`section_id`),
  CONSTRAINT `subject_assigns_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_assigns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_assigns_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_assigns`
--

LOCK TABLES `subject_assigns` WRITE;
/*!40000 ALTER TABLE `subject_assigns` DISABLE KEYS */;
INSERT INTO `subject_assigns` VALUES (1,1,1,1,1,'2025-08-31 23:17:20','2025-08-31 23:17:20',1),(2,1,2,1,1,'2025-08-31 23:20:54','2025-08-31 23:20:54',1);
/*!40000 ALTER TABLE `subject_assigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_attendances`
--

DROP TABLE IF EXISTS `subject_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `classes_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `roll` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `attendance` tinyint DEFAULT '3' COMMENT '1=present, 2=late, 3=absent, 4=half_day, 5=Leave',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_attendances_session_id_foreign` (`session_id`),
  KEY `subject_attendances_student_id_foreign` (`student_id`),
  KEY `subject_attendances_classes_id_foreign` (`classes_id`),
  KEY `subject_attendances_section_id_foreign` (`section_id`),
  KEY `subject_attendances_subject_id_foreign` (`subject_id`),
  CONSTRAINT `subject_attendances_classes_id_foreign` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_attendances_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_attendances`
--

LOCK TABLES `subject_attendances` WRITE;
/*!40000 ALTER TABLE `subject_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `subject_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'English','101',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(2,'Arabic','102',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(3,'Islamic Studies','103',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(4,'Science','104',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(5,'Social Studies','105',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(6,'Mathematics','106',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(7,'Somali','107',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(8,'English','201',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(9,'Mathematics','202',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(10,'Chemistry','203',2,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(11,'Physics','204',2,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(12,'Biology','205',2,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(13,'Islamic Studies','206',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(14,'Somali','207',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(15,'History','208',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(16,'Geography','209',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1),(17,'Arabic','210',1,1,'2025-09-01 03:12:26','2025-09-01 03:12:26',1);
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribes`
--

DROP TABLE IF EXISTS `subscribes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribes_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribes`
--

LOCK TABLES `subscribes` WRITE;
/*!40000 ALTER TABLE `subscribes` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_type` enum('prepaid','postpaid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prepaid',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int DEFAULT NULL,
  `student_limit` int DEFAULT NULL,
  `staff_limit` int DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `trx_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features_name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0 = inactive, 1 = active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,'prepaid',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-06-03 07:04:08','2025-06-03 07:04:08',1);
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_notifications`
--

DROP TABLE IF EXISTS `system_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reciver_id` int unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_notifications`
--

LOCK TABLES `system_notifications` WRITE;
/*!40000 ALTER TABLE `system_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_schedules`
--

DROP TABLE IF EXISTS `time_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint NOT NULL COMMENT 'Class = 1, Exam = 2',
  `start_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_schedules`
--

LOCK TABLES `time_schedules` WRITE;
/*!40000 ALTER TABLE `time_schedules` DISABLE KEYS */;
INSERT INTO `time_schedules` VALUES (1,1,'06:30','07:15',1,'2025-08-31 23:32:43','2025-09-06 23:28:01',1),(2,1,'07:15','08:00',1,'2025-09-06 23:44:33','2025-09-06 23:44:33',1),(3,1,'08:00','08:45',1,'2025-09-06 23:45:07','2025-09-06 23:45:07',1),(4,1,'08:45','09:30',1,'2025-09-06 23:45:43','2025-09-06 23:45:43',1),(5,1,'09:30','10:15',1,'2025-09-06 23:46:18','2025-09-06 23:46:18',1),(6,1,'10:30','11:15',1,'2025-09-06 23:48:03','2025-09-06 23:48:03',1),(7,1,'11:15','00:00',1,'2025-09-06 23:48:42','2025-09-06 23:48:42',1);
/*!40000 ALTER TABLE `time_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uploads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--

LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
INSERT INTO `uploads` VALUES (1,'backend/uploads/users/user-icon-1.png','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(2,'backend/uploads/users/user-icon-2.png','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(3,'backend/uploads/users/user-icon-3.png','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(4,'backend/uploads/users/user-icon-4.png','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(5,'frontend/img/accreditation/accreditation.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(6,'frontend/img/banner/cta_bg.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(7,'frontend/img/explore/1.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(8,'frontend/img/icon/1.svg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(9,'frontend/img/icon/2.svg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(10,'frontend/img/icon/3.svg','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(11,'frontend/img/sliders/03.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(12,'frontend/img/sliders/02.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(13,'frontend/img/sliders/01.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(14,'frontend/img/counters/01.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(15,'frontend/img/counters/02.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(16,'frontend/img/counters/03.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(17,'frontend/img/counters/04.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(18,'frontend/img/counters/05.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(19,'frontend/img/blog/01.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(20,'frontend/img/blog/02.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(21,'frontend/img/blog/03.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(22,'frontend/img/blog/04.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(23,'frontend/img/blog/05.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(24,'frontend/img/blog/06.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(25,'frontend/img/blog/07.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(26,'frontend/img/blog/08.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(27,'frontend/img/blog/09.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(28,'frontend/img/blog/10.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(29,'frontend/img/blog/11.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(30,'frontend/img/blog/12.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(31,'frontend/img/blog/13.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(32,'frontend/img/gallery/1.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(33,'frontend/img/gallery/2.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(34,'frontend/img/gallery/3.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(35,'frontend/img/gallery/4.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(36,'frontend/img/gallery/5.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(37,'frontend/img/gallery/6.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(38,'frontend/img/gallery/7.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(39,'frontend/img/gallery/8.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(40,'frontend/img/gallery/9.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(41,'frontend/img/gallery/10.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(42,'frontend/img/gallery/11.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(43,'frontend/img/gallery/12.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(44,'frontend/img/gallery/13.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(45,'frontend/img/gallery/14.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(46,'frontend/img/gallery/15.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(47,'frontend/img/gallery/16.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(48,'frontend/img/gallery/17.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(49,'frontend/img/gallery/18.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(50,'frontend/img/gallery/19.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(51,'frontend/img/gallery/20.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(52,'frontend/img/gallery/21.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(53,'frontend/img/gallery/22.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(54,'frontend/img/gallery/23.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(55,'frontend/img/gallery/24.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(56,'frontend/img/contact/contact_1.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(57,'frontend/img/contact/contact_2.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(58,'frontend/img/contact/contact_3.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(59,'frontend/img/contact/contact_4.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(60,'frontend/img/contact/admission_1.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(61,'frontend/img/contact/admission_2.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(62,'frontend/img/contact/admission_3.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(63,'frontend/img/contact/admission_4.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(64,'frontend/img/about-gallery/1.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(65,'frontend/img/about-gallery/icon_1.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(66,'frontend/img/about-gallery/2.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(67,'frontend/img/about-gallery/icon_2.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(68,'frontend/img/about-gallery/3.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(69,'frontend/img/about-gallery/icon_3.webp','2025-06-03 07:04:08','2025-06-03 07:04:08',1),(70,'backend/uploads/students/1756727519O4ywCmxVw2.jpg','2025-09-01 04:10:07','2025-09-01 04:51:59',1),(71,'backend/uploads/students/1756787933Ine8151e2e.jpg','2025-09-01 21:38:53','2025-09-01 21:38:53',1),(72,'backend/uploads/students/1756798874CuJgrXQdoQ.jpg','2025-09-02 00:41:14','2025-09-02 00:41:14',1);
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'For student login',
  `date_of_birth` date DEFAULT NULL,
  `gender` tinyint NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'if null then verifield, not null then not verified',
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token for email/phone verification, if null then verifield, not null then not verified',
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  `upload_id` bigint unsigned DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `designation_id` bigint unsigned DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'device_token from firebase',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_password_otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_upload_id_foreign` (`upload_id`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_upload_id_foreign` FOREIGN KEY (`upload_id`) REFERENCES `uploads` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@telesom.com',NULL,'2022-09-07',1,'2025-06-03 07:04:09',NULL,'01811000000','$2y$10$XMdjEwXFwvUOs5uyOwZ4yu4O/UR1cVifPqvLaf9Vdk83wcjUERyOO','[\"counter_read\",\"fees_collesction_read\",\"revenue_read\",\"fees_collection_this_month_read\",\"income_expense_read\",\"upcoming_events_read\",\"attendance_chart_read\",\"calendar_read\",\"student_read\",\"student_create\",\"student_update\",\"student_delete\",\"student_category_read\",\"student_category_create\",\"student_category_update\",\"student_category_delete\",\"promote_students_read\",\"promote_students_create\",\"disabled_students_read\",\"disabled_students_create\",\"parent_read\",\"parent_create\",\"parent_update\",\"parent_delete\",\"admission_read\",\"admission_create\",\"admission_update\",\"admission_delete\",\"classes_read\",\"classes_create\",\"classes_update\",\"classes_delete\",\"section_read\",\"section_create\",\"section_update\",\"section_delete\",\"shift_read\",\"shift_create\",\"shift_update\",\"shift_delete\",\"class_setup_read\",\"class_setup_create\",\"class_setup_update\",\"class_setup_delete\",\"subject_read\",\"subject_create\",\"subject_update\",\"subject_delete\",\"subject_assign_read\",\"subject_assign_create\",\"subject_assign_update\",\"subject_assign_delete\",\"class_routine_read\",\"class_routine_create\",\"class_routine_update\",\"class_routine_delete\",\"time_schedule_read\",\"time_schedule_create\",\"time_schedule_update\",\"time_schedule_delete\",\"class_room_read\",\"class_room_create\",\"class_room_update\",\"class_room_delete\",\"fees_group_read\",\"fees_group_create\",\"fees_group_update\",\"fees_group_delete\",\"fees_type_read\",\"fees_type_create\",\"fees_type_update\",\"fees_type_delete\",\"fees_master_read\",\"fees_master_create\",\"fees_master_update\",\"fees_master_delete\",\"fees_assign_read\",\"fees_assign_create\",\"fees_assign_update\",\"fees_assign_delete\",\"fees_collect_read\",\"fees_collect_create\",\"fees_collect_update\",\"fees_collect_delete\",\"exam_type_read\",\"exam_type_create\",\"exam_type_update\",\"exam_type_delete\",\"marks_grade_read\",\"marks_grade_create\",\"marks_grade_update\",\"marks_grade_delete\",\"exam_assign_read\",\"exam_assign_create\",\"exam_assign_update\",\"exam_assign_delete\",\"exam_routine_read\",\"exam_routine_create\",\"exam_routine_update\",\"exam_routine_delete\",\"marks_register_read\",\"marks_register_create\",\"marks_register_update\",\"marks_register_delete\",\"homework_read\",\"homework_create\",\"homework_update\",\"homework_delete\",\"exam_setting_read\",\"exam_setting_update\",\"account_head_read\",\"account_head_create\",\"account_head_update\",\"account_head_delete\",\"income_read\",\"income_create\",\"income_update\",\"income_delete\",\"expense_read\",\"expense_create\",\"expense_update\",\"expense_delete\",\"attendance_read\",\"attendance_create\",\"report_marksheet_read\",\"report_merit_list_read\",\"report_progress_card_read\",\"report_due_fees_read\",\"report_fees_collection_read\",\"report_account_read\",\"report_class_routine_read\",\"report_exam_routine_read\",\"report_attendance_read\",\"language_read\",\"language_create\",\"language_update\",\"language_update_terms\",\"language_delete\",\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"department_read\",\"department_create\",\"department_update\",\"department_delete\",\"designation_read\",\"designation_create\",\"designation_update\",\"designation_delete\",\"page_sections_read\",\"page_sections_update\",\"slider_read\",\"slider_create\",\"slider_update\",\"slider_delete\",\"about_read\",\"about_create\",\"about_update\",\"about_delete\",\"counter_read\",\"counter_create\",\"counter_update\",\"counter_delete\",\"contact_info_read\",\"contact_info_create\",\"contact_info_update\",\"contact_info_delete\",\"dep_contact_read\",\"dep_contact_create\",\"dep_contact_update\",\"dep_contact_delete\",\"news_read\",\"news_create\",\"news_update\",\"news_delete\",\"event_read\",\"event_create\",\"event_update\",\"event_delete\",\"gallery_category_read\",\"gallery_category_create\",\"gallery_category_update\",\"gallery_category_delete\",\"gallery_read\",\"gallery_create\",\"gallery_update\",\"gallery_delete\",\"subscribe_read\",\"contact_message_read\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_update\",\"task_schedules_read\",\"task_schedules_update\",\"software_update_read\",\"software_update_update\",\"recaptcha_settings_read\",\"recaptcha_settings_update\",\"payment_gateway_settings_read\",\"payment_gateway_settings_update\",\"email_settings_read\",\"email_settings_update\",\"sms_settings_read\",\"sms_settings_update\",\"gender_read\",\"gender_create\",\"gender_update\",\"gender_delete\",\"religion_read\",\"religion_create\",\"religion_update\",\"religion_delete\",\"blood_group_read\",\"blood_group_create\",\"blood_group_update\",\"blood_group_delete\",\"session_read\",\"session_create\",\"session_update\",\"session_delete\",\"book_category_read\",\"book_category_create\",\"book_category_update\",\"book_category_delete\",\"book_read\",\"book_create\",\"book_update\",\"book_delete\",\"member_read\",\"member_create\",\"member_update\",\"member_delete\",\"member_category_read\",\"member_category_create\",\"member_category_update\",\"member_category_delete\",\"issue_book_read\",\"issue_book_create\",\"issue_book_update\",\"issue_book_delete\",\"online_exam_type_read\",\"online_exam_type_create\",\"online_exam_type_update\",\"online_exam_type_delete\",\"question_group_read\",\"question_group_create\",\"question_group_update\",\"question_group_delete\",\"question_bank_read\",\"question_bank_create\",\"question_bank_update\",\"question_bank_delete\",\"online_exam_read\",\"online_exam_create\",\"online_exam_update\",\"online_exam_delete\",\"id_card_read\",\"id_card_create\",\"id_card_update\",\"id_card_delete\",\"id_card_generate_read\",\"certificate_read\",\"certificate_create\",\"certificate_update\",\"certificate_delete\",\"certificate_generate_read\",\"homework_read\",\"homework_create\",\"homework_update\",\"homework_delete\",\"gmeet_read\",\"gmeet_create\",\"gmeet_update\",\"gmeet_delete\",\"notice_board_read\",\"notice_board_create\",\"notice_board_update\",\"notice_board_delete\",\"sms_mail_template_read\",\"sms_mail_template_create\",\"nsms_mail_templateupdate\",\"sms_mail_template_delete\",\"sms_mail_read\",\"sms_mail_send\"]',NULL,1,1,1,1,5,'331e4c52-9fba-4389-a12f-ce9f179b14b9',NULL,'JCOOvDG5V09Oq6fpD8qh1qg6LBg2JGjh5zxZWCksQNgBVOacIGDLMo0SB4aG',NULL,'2025-06-03 07:04:08','2025-09-11 08:21:42',NULL),(2,'noradin-sh.madar','noradin-shmadar@noradin.com',NULL,NULL,1,NULL,NULL,NULL,'$2y$10$KSWJRCQ17fFAFfnUicVzUel.JNGCYsmpGbZrbeEwT9fVxorqIvKc6',NULL,NULL,1,2,NULL,2,NULL,NULL,NULL,NULL,NULL,'2025-08-31 06:21:21','2025-08-31 06:21:21',NULL),(3,'Mohamed Ali','mohamedali463@parent.somaliland.edu',NULL,'1986-08-31',1,NULL,NULL,'+252 63 3925721','$2y$10$cduOBqTXZapkXKlCbkCdaOHvcA77TUozLojChKUqNbaD.TPNZrVWS',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:56','2025-08-31 13:37:56',NULL),(4,'Ismail Osman','ismailosman672@parent.somaliland.edu',NULL,'1981-08-31',1,NULL,NULL,'+252 63 3258021','$2y$10$SSDdihazXISrWxuB0Qja/O/sXYMZubIpMX1GRsPzi6jigD/h7GXLi',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:56','2025-08-31 13:37:56',NULL),(5,'Cumar Hussein','cumarhussein802@parent.somaliland.edu',NULL,'1992-08-31',1,NULL,NULL,'+252 65 7441157','$2y$10$bHk2fGanL3L9PZfQBEp3FOza6gZnlZ/FgdvgCmJp21w7gZGQyLnae',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:56','2025-08-31 13:37:56',NULL),(6,'Ali Abdullahi','aliabdullahi708@parent.somaliland.edu',NULL,'1992-08-31',1,NULL,NULL,'+252 63 4667467','$2y$10$11kELv9AWlzm3DZN89.F6uV21eTzoajBVnA82vkHqzJmHerzd26pe',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:56','2025-08-31 13:37:56',NULL),(7,'Ali Omar','aliomar837@parent.somaliland.edu',NULL,'1996-08-31',1,NULL,NULL,'+252 64 2336898','$2y$10$iPKWjoMrrbdTvHG3LHfLCei7dK4h1R8sjMLigfY2RcqTu2DT0NmZG',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(8,'Ali Ahmed','aliahmed616@parent.somaliland.edu',NULL,'1995-08-31',1,NULL,NULL,'+252 64 9810145','$2y$10$RcG57xdTh8xukKwyWXWl.eRkjEylxHx0oxibj.6/Crucny76vd1QK',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(9,'Saeed Mustafe','saeedmustafe288@parent.somaliland.edu',NULL,'1992-08-31',1,NULL,NULL,'+252 64 3760094','$2y$10$8Y1eJDQui/V6/bzPPM.SUub9PAi0MUn3V3O6bJtQxFx/aCfpbOnla',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(10,'Omar Ibrahim','omaribrahim748@parent.somaliland.edu',NULL,'2000-08-31',1,NULL,NULL,'+252 64 9163879','$2y$10$T.0V35XjIDBeQQLVSLPmP.mPoRpRTA9CongldHeTI/xF9op288LlG',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(11,'Yusuf Omar','yusufomar464@parent.somaliland.edu',NULL,'1987-08-31',1,NULL,NULL,'+252 64 4873808','$2y$10$Qh8aLr5YjUeh3o4SrVtLUebXDSUQkzAKQrGq/NL1VI1E.NnyrIJvq',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(12,'Jama Cabdi','jamacabdi143@parent.somaliland.edu',NULL,'1986-08-31',1,NULL,NULL,'+252 64 7550485','$2y$10$ju7om6SOPf1VRGawux7vmuE76NixDplJE0Ca4No0x7YITrhth6yc2',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(13,'Abdullahi Ahmed','abdullahiahmed979@parent.somaliland.edu',NULL,'1993-08-31',1,NULL,NULL,'+252 64 4578119','$2y$10$f6sfI6DPWi58PJB1AuG7feqgSJptwCLAeKDeVj62BtcCxEx0iG6jW',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(14,'Ibrahim Farah','ibrahimfarah672@parent.somaliland.edu',NULL,'1984-08-31',1,NULL,NULL,'+252 63 7120163','$2y$10$baxj84pgbltq8GRxlWv88uDhdX.kRKeUwT7xu6K7m3BbjxqEYDuSa',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(15,'Axmed Mustafe','axmedmustafe847@student.somaliland.edu',NULL,'2015-06-08',1,'2025-09-02 10:18:52',NULL,'+252 65 4664116','$2y$10$r12VBMLbLhlMEZfWkFWMbe/bLJnnzrti7jzFHEGTITgx4EJG5dgTK',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(16,'Yusuf Osman','yusufosman999@student.somaliland.edu',NULL,'2014-12-04',1,'2025-09-02 10:18:52',NULL,'+252 63 5113196','$2y$10$kqrpgKmfH1bDAQ/ZsPbhmONmoMzKFII85vuKM4L9U.R0FgWx2TMFa',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:57','2025-08-31 13:37:57',NULL),(17,'Yusuf Ali','yusufali128@student.somaliland.edu',NULL,'2015-05-17',1,'2025-09-02 10:18:52',NULL,'+252 63 7409875','$2y$10$1fx4v8By23FegDQ5w9yxOOgJQF9eOeStt7dsdbow13qGqdAHmGOgG',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(18,'Caasha Ibrahim','caashaibrahim778@student.somaliland.edu',NULL,'2015-06-14',1,'2025-09-02 10:18:52',NULL,'+252 65 5870284','$2y$10$2ws2ql35B8ll5YjPGqRvieEzsx8xZv54kE5pGCFtxAtPhHygirGfa',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(19,'Asli Jama','aslijama340@student.somaliland.edu',NULL,'2014-10-26',1,'2025-09-02 10:18:52',NULL,'+252 65 8990011','$2y$10$HuOU671BG8hYJrmtIOkSYuqWHdZG5x0qi0993cKcHSc.1yjt/KYQC',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(20,'Abdullahi Saeed','abdullahisaeed894@student.somaliland.edu',NULL,'2014-09-06',1,'2025-09-02 10:18:52',NULL,'+252 65 7028022','$2y$10$Evs9UlRHExhncrM5ikD7h.46Uab5uxmvG/mYl/64aIE27SKTSz7uO',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(21,'Faadumo Maxamed','faadumomaxamed494@student.somaliland.edu',NULL,'2014-09-26',1,'2025-09-02 10:18:52',NULL,'+252 64 4053892','$2y$10$nL.0RmG86VtqjC4IXaaV4OjrmYU7.rjI2Tn6U./BVRvaxFMdoTbzq',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(22,'Mohamed Mustafe','mohamedmustafe430@student.somaliland.edu',NULL,'2014-10-26',1,'2025-09-02 10:18:52',NULL,'+252 63 5433484','$2y$10$/Y/ngWCg6LnFoa0W8Js40OUZRZ3GUEB2wUidNTjiekLiohitiFCD.',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(23,'Caasha Ismail','caashaismail676@student.somaliland.edu',NULL,'2014-12-26',1,'2025-09-02 10:18:52',NULL,'+252 65 3698544','$2y$10$GnrT0zu06kyjjDO9fzDf8Ohwdh7enwN/oiecVRQzJh2/SDxlCaYKS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(24,'Ayan Hussein','ayanhussein735@student.somaliland.edu',NULL,'2014-10-23',1,'2025-09-02 10:18:52',NULL,'+252 65 8952443','$2y$10$pTR0Gkty.BoX0jzARiFl0ecm50V9y686qBeVHffVsA5QvzmwbzyyO',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(25,'Ayan Cabdi','ayancabdi823@student.somaliland.edu',NULL,'2014-12-28',1,'2025-09-02 10:18:52',NULL,'+252 64 8908574','$2y$10$4PHI3IGTUbLvh/3b8tq/heOHD3tvrOP8G/333aCD.D/SiONjyLuBS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(26,'Dahir Abdi','dahirabdi656@student.somaliland.edu',NULL,'2015-04-17',1,'2025-09-02 10:18:52',NULL,'+252 63 4703227','$2y$10$ZwU/5vPNy1erztX8f3XcWuCdlov/BUW0D6QiqA/vHhF/0UlZifCUW',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:58','2025-08-31 13:37:58',NULL),(27,'Canab Dahir','canabdahir300@student.somaliland.edu',NULL,'2014-11-13',1,'2025-09-02 10:18:52',NULL,'+252 65 9715933','$2y$10$iIjHU2KTdknLFmZMvIEyYOA.BePO2SJsTlwcz56TGkMaDPay0lSpC',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(28,'Sahra Abdullahi','sahraabdullahi910@student.somaliland.edu',NULL,'2015-05-28',1,'2025-09-02 10:18:52',NULL,'+252 65 1672239','$2y$10$yQsFFt44NhTOqnjPPOnI8uGHNFPaNzCwYoYiPO7jZht5fCeinJToi',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(29,'Ibrahim Hussein','ibrahimhussein441@student.somaliland.edu',NULL,'2015-04-26',1,'2025-09-02 10:18:52',NULL,'+252 65 6836165','$2y$10$nBqhA3zWx6SKJXU.jvG5FOAmRUt9Bv4zJ1vXAMNHQujQLvU9zESbu',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(30,'Asli Maxamed','aslimaxamed231@student.somaliland.edu',NULL,'2015-07-18',1,'2025-09-02 10:18:52',NULL,'+252 63 3106971','$2y$10$1JKw2QnFUaASFBYoDet7W..SNstK0T6CpCQsd4kN.gxYEaRPfs2a.',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(31,'Maxamed Jama','maxamedjama441@student.somaliland.edu',NULL,'2015-02-23',1,'2025-09-02 10:18:52',NULL,'+252 65 7966849','$2y$10$Xq7QlFb22G3XsU7HZTvkZO7Ej3vLniOG1jS.oEVNWqA35DMYoHQVa',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(32,'Maryan Jama','maryanjama444@student.somaliland.edu',NULL,'2015-08-08',1,'2025-09-02 10:18:52',NULL,'+252 65 6444919','$2y$10$IQhHbsnbgW5myp7jxBzmw.B0pYxjm2zfA6BK4NmVTZyGhQuNCWQ7.',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(33,'Omar Ahmed','omarahmed839@student.somaliland.edu',NULL,'2015-06-16',1,'2025-09-02 10:18:52',NULL,'+252 64 3428081','$2y$10$IcUL4ccX/2He6oT/rpTyqOVH8zQPPiORQdG4BrbcVIXMQGVup6/DG',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(34,'Warsan Ismail','warsanismail837@student.somaliland.edu',NULL,'2015-05-08',1,'2025-09-02 10:18:52',NULL,'+252 63 3284062','$2y$10$r358NIu.ZQzc2wmox2whguaa03detdLbjzjYOuvpUzGbZysq9Ti2i',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(35,'Faadumo Ismail','faadumoismail393@student.somaliland.edu',NULL,'2014-09-12',1,'2025-09-02 10:18:52',NULL,'+252 64 3851905','$2y$10$v4rV2p0iovdmF8lPAmq7HuJv6RfzYS/oIGTc1JQF3nBkaiayjJLmm',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(36,'Abdirashid Abdullahi','abdirashidabdullahi978@student.somaliland.edu',NULL,'2014-09-23',1,'2025-09-02 10:18:52',NULL,'+252 63 8981998','$2y$10$Zx2Grg4uOJH/JRCQlD51XekghTBzTIvZ4wlMF6pmy8VqOsrZgER.6',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(37,'Canab Jama','canabjama663@student.somaliland.edu',NULL,'2014-10-28',1,'2025-09-02 10:18:52',NULL,'+252 65 3506509','$2y$10$x7CgEbUuzvovWTuuRZLDNOsr9hnESiX.xwticOSwGNE.lLNjJnbeS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:37:59','2025-08-31 13:37:59',NULL),(38,'Omar Jama','omarjama148@student.somaliland.edu',NULL,'2014-09-21',1,'2025-09-02 10:18:52',NULL,'+252 64 6046458','$2y$10$VvZlgHpeIaFQNHKf2FMpnOGwippFwt3bTyNRBhjUeVrapg9RxA2Ja',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(39,'Saynab Jama','saynabjama688@student.somaliland.edu',NULL,'2015-02-03',1,'2025-09-02 10:18:52',NULL,'+252 64 1077268','$2y$10$r4g67of8DtFGtFeqO.RM8OwKMyqpPZRAsGntdlFgPRelFJF0PQD3q',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(40,'Ahmed Cabdi','ahmedcabdi947@student.somaliland.edu',NULL,'2015-07-06',1,'2025-09-02 10:18:52',NULL,'+252 63 4875364','$2y$10$fP3QcF4Frcj1MfszVWuLeuAsADk6FbSzWRyyhpwKSJz.9Au6W8y4C',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(41,'Ayan Omar','ayanomar787@student.somaliland.edu',NULL,'2015-05-29',1,'2025-09-02 10:18:52',NULL,'+252 63 7040060','$2y$10$1RUysI.U2z2MT8XNv6GBCeraVOFd0B7u4jUvRgpYMFQfIfR65YZqS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(42,'Maxamed Cabdi','maxamedcabdi467@student.somaliland.edu',NULL,'2015-02-05',1,'2025-09-02 10:18:52',NULL,'+252 65 4406026','$2y$10$41rGzn40cgkf9rldKO5DWOkPRGoYqy9/.s/5oiK7.jopjyAzOEouK',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(43,'Mustafe Abdullahi','mustafeabdullahi813@student.somaliland.edu',NULL,'2015-04-29',1,'2025-09-02 10:18:52',NULL,'+252 63 9062678','$2y$10$k9ZSEDSqAAJ3VCCUcVHRWOgKojHm6DR6G9kCV4EojJapFuL0rD7fq',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(44,'Faduma Hersi','fadumahersi689@student.somaliland.edu',NULL,'2015-04-09',1,'2025-09-02 10:18:52',NULL,'+252 63 2776983','$2y$10$sKrnsvT/biditRRrm06qNellBOgZH47Sw/ff8UPm0raDWWk8kwJAO',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-08-31 13:38:00','2025-08-31 13:38:00',NULL),(45,'Abdullahi Abdullahi','abdullahiabdullahi251@parent.somaliland.edu',NULL,'1985-09-01',1,NULL,NULL,'+252 64 3024195','$2y$10$Enlfk6DzLk9rp4CqMBc5pOWHe.ROD1cBoGCQT2zSqZvjVbMDkQrGy',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL),(46,'Axmed Ahmed','axmedahmed903@parent.somaliland.edu',NULL,'1993-09-01',1,NULL,NULL,'+252 65 8318416','$2y$10$EGftUou8LMmTCaxZ8iCIYOc7TRHKqZPfiDiYO7YinrrZbqnYe.pG6',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL),(47,'Ibrahim Ahmed','ibrahimahmed453@parent.somaliland.edu',NULL,'1990-09-01',1,'2025-09-03 05:16:50',NULL,'+252 64 3138086','$2y$10$xNb25uC3Ga0ON..q.vOeuugKvmK.i6ygsOX1Vatnorlmq1fa6K5Dy',NULL,NULL,1,1,NULL,7,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:40','2025-09-01 02:38:40','ibrahimahmed453@parent.somaliland.edu'),(48,'Yusuf Ismail','yusufismail219@parent.somaliland.edu',NULL,'1986-09-01',1,'2025-09-02 10:18:52',NULL,'+252 64 6734571','$2y$10$NqU5Sfp.fiyQfSNObhVFDeKEWoyvHf5HNLKjD6Qv2naWB4jQTRLz.',NULL,NULL,1,1,NULL,7,NULL,NULL,NULL,'SaN8ZcOqq2KWrFqW4gKZbZ6BrPWfBRTS6ZQatBp0LTy3f2x3bInws1RxcbYk',NULL,'2025-09-01 02:38:40','2025-09-01 02:38:40','yusufismail219@parent.somaliland.edu'),(49,'Yusuf Farah','yusuffarah300@parent.somaliland.edu',NULL,'1998-09-01',1,NULL,NULL,'+252 65 4642283','$2y$10$gBTHtFm4dgOXrWQ3B/yQOeagfbBX2CNAeYGi0OCL1PiV3uUEsSWMe',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL),(50,'Osman Ali','osmanali639@parent.somaliland.edu',NULL,'1986-09-01',1,NULL,NULL,'+252 64 9746860','$2y$10$Kmcs4VR8i5QrqYVUddTMKOhVt6z/to.zTGbki0PHotn/QxU5eew5u',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:40','2025-09-01 02:38:40',NULL),(51,'Ahmed Ali','ahmedali668@parent.somaliland.edu',NULL,'1983-09-01',1,NULL,NULL,'+252 64 9338957','$2y$10$RV2w/UXYlfN339m6kJoN..8rcuDILHzYS6OH4XuVZDkFy98YMTRkq',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(52,'Mohamed Abdi','mohamedabdi132@parent.somaliland.edu',NULL,'1989-09-01',1,NULL,NULL,'+252 65 8287573','$2y$10$s7xurB8a/l8iesT/tARnie1L2gH3.2VHMZJIWyXJLkZULoRCdsSl6',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(53,'Cabdi Osman','cabdiosman105@parent.somaliland.edu',NULL,'1984-09-01',1,NULL,NULL,'+252 63 3481122','$2y$10$Gz0sh/4gFxEEALfHsi1DzuG/ICQu9E2baIbPqli6l0Oe.k0PD2qQK',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(54,'Ibrahim Omar','ibrahimomar694@parent.somaliland.edu',NULL,'1999-09-01',1,NULL,NULL,'+252 63 2700334','$2y$10$5MCChslTYHh4NECP/RxlwuW4spy6x34D/zwqoK/fErpJ3KWShp4WC',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(55,'Abdirashid Hassan','abdirashidhassan487@parent.somaliland.edu',NULL,'1982-09-01',1,NULL,NULL,'+252 65 4074294','$2y$10$GY17nweWUcHt01PZtm7BDe/xRsMRM9ii16chmrhVwaWSREQUURuMW',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(56,'Abdirashid Ibrahim','abdirashidibrahim399@parent.somaliland.edu',NULL,'1985-09-01',1,NULL,NULL,'+252 65 3392014','$2y$10$OUYTs0jZXJhosspVoKHK5eFCv4OboHS3h/e6Z4MgpG5spjqAY8g8K',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(57,'Ayan Farah','ayanfarah631@student.somaliland.edu',NULL,'2015-07-16',1,'2025-09-02 10:18:52',NULL,'+252 65 8349330','$2y$10$VPyl87K7jwT3kRVL/tdJjOXZSmDvycN3OmU/pGe1dEI2PyG7j6W7C',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(58,'Abdirashid Abdullahi','abdirashidabdullahi357@student.somaliland.edu',NULL,'2014-10-16',1,'2025-09-02 10:18:52',NULL,'+252 65 3165603','$2y$10$5HwLTwWLz1petukRnDDYP.pQwNRk9xPciBW5N5IvQwAgXgKW.YWAS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(59,'Omar Saeed','omarsaeed858@student.somaliland.edu',NULL,'2015-04-03',1,'2025-09-02 10:18:52',NULL,'+252 65 7670477','$2y$10$hB8jmAQqzr299HgelVv57uIogbLAGC4bpcfWilirR9PygHKYVoDfW',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41','omarsaeed858@student.somaliland.edu'),(60,'Mohamed Saeed','mohamedsaeed201@student.somaliland.edu',NULL,'2015-05-02',1,'2025-09-02 10:18:52',NULL,'+252 64 5863079','$2y$10$LM4LvSVOkh7nJgRuUzHPh.hCTK/Hd4RFji7QdGkpOH3uT8e1CJFea',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:41','2025-09-01 02:38:41',NULL),(61,'Khadija Ibrahim','khadijaibrahim809@student.somaliland.edu','20255005','2014-12-03',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$2nC1zhWUkDCChkalVcKLw.ggYjTbUZEF0g3b0.jwmv0Kby4RO2LDe','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-04 03:56:31',''),(62,'Farah Saeed','farahsaeed360@student.somaliland.edu',NULL,'2014-11-09',1,'2025-09-02 10:18:52',NULL,'+252 64 1079143','$2y$10$53UxGdTMTCwKpZDgjFgia.e9TWaghJ3JEibO9pAJOtHRNvL/lM166',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(63,'Ahmed Hussein','ahmedhussein694@student.somaliland.edu',NULL,'2015-07-14',1,'2025-09-02 10:18:52',NULL,'+252 64 2910472','$2y$10$aImvt0PquwuBIKyaUa3E/.3PGmpvM83tC.Gw/s.Em//PMCsTjvEkm',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(64,'Caasha Mohamed','caashamohamed175@student.somaliland.edu',NULL,'2015-02-22',1,'2025-09-02 10:18:52',NULL,'+252 64 2391537','$2y$10$MNAib4IM2E/zc5wHb3ZUUe6TJwqtJzwBCdC0nc20tX4TL7QCu2ece',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(65,'Ismail Ibrahim','ismailibrahim765@student.somaliland.edu',NULL,'2015-02-17',1,'2025-09-02 10:18:52',NULL,'+252 64 4494182','$2y$10$BPKlF8Mdz2UdCJBr8BsM3un9d9/aNNYExMw4wkEG7m32pXjqWlkHq',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(66,'Ali Saeed','alisaeed695@student.somaliland.edu','202510010','2014-09-17',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$W2iB.CUe3mPeGBhqSigNXuCLmUgZpgCfqKKYTJ/BkpMxXkhtoPjvm','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-10 03:53:08',''),(67,'Abdirashid Ali','abdirashidali813@student.somaliland.edu',NULL,'2015-03-08',1,'2025-09-02 10:18:52',NULL,'+252 64 3929976','$2y$10$Whuoyu9Pl.T6RQWEbSoARul0P1DBI4XdbnQfvSj3Z8UlUN.FeT4r.',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(68,'Saynab Hussein','saynabhussein746@student.somaliland.edu','202512012','2015-07-21',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$5B.1V0eRKT2y1sTK5ELX2.lho4rgttZSZjqEUNf5yl6R7n/Yb39Wa','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-05 23:11:46',''),(69,'Dahir Farah','dahirfarah766@student.somaliland.edu',NULL,'2015-04-17',1,'2025-09-02 10:18:52',NULL,'+252 63 3924161','$2y$10$7TARwzsJ40VgAcJwXOLxre9UTmoGenZxUnJL4dJkQylzdLXbJ1bXS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(70,'Maxamed Ismail','maxamedismail734@student.somaliland.edu','202514014','2015-05-05',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$8mX9qBrkLyozxtUqwEO7HuCnW.VU2MNkyZJGQc7/DA2x.v4fmY8eO','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-05 23:53:47',''),(71,'Ikraan Abdi','ikraanabdi564@student.somaliland.edu',NULL,'2015-06-10',1,'2025-09-02 10:18:52',NULL,'+252 63 9319043','$2y$10$rGD.DgTZYJ1cqA368nB3ZuY8mS.6WsgJYsiosUKiQ6soNI0R.3qNC',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:42','2025-09-01 02:38:42',NULL),(72,'Jama Omar','jamaomar512@student.somaliland.edu',NULL,'2015-08-26',1,'2025-09-02 10:18:52',NULL,'+252 64 1393935','$2y$10$7Xyisqqyz7yYihYr2zWU/ut/XWSLhJ0hk4Jhn5vyJHe1z0cDRQVwi',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(73,'Yusuf Dahir','yusufdahir309@student.somaliland.edu',NULL,'2014-12-13',1,'2025-09-02 10:18:52',NULL,'+252 63 4229070','$2y$10$m0t.9OUDKakgm4oF8YIxaOvHV0ya4XG7zR9bK0KziMPorsTuu4fFG',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(74,'Abdirashid Omar','abdirashidomar148@student.somaliland.edu',NULL,'2015-06-24',1,'2025-09-02 10:18:52',NULL,'+252 64 8539642','$2y$10$E.MkYzY7/DIiE0PMMLZBeObRz4h/fMUtyc1mi.GsXX5kvqomZHDKK',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(75,'Ayan Ismail','ayanismail981@student.somaliland.edu',NULL,'2015-02-13',1,'2025-09-02 10:18:52',NULL,'+252 64 9409863','$2y$10$cLnRV4wqI5jYVrpWP1TFcuCweFaAY8mF5xfQbp6P4y3c4vS/Zxia6',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(76,'Maryan Ali','maryanali687@student.somaliland.edu','202520020','2014-11-13',1,'2025-09-02 10:18:52',NULL,'4445564','$2y$10$ypqPVpJ0VirDSdvDxQ.1eOd0sgj4TtELj8JsOvhtwB7LuPtg3H.qC','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 22:30:23',''),(77,'Dahir Hussein','dahirhussein376@student.somaliland.edu','202521021','2014-11-09',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$r10zelP8o53IXTsHyalUNOPbUN1uZiJsDWOo19oMcltbRHl49lFRS','[]',NULL,1,1,72,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-02 00:41:14','dahirhussein376@student.somaliland.edu'),(78,'Osman Yusuf','osmanyusuf622@student.somaliland.edu',NULL,'2015-06-15',1,'2025-09-02 10:18:52',NULL,'+252 63 6798868','$2y$10$gcazpz7MPBCXdbBJbYYGX.HVxXzF.tjMJYw999sVnva8/7XyLSevS',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(79,'Ali Maxamed','alimaxamed363@student.somaliland.edu',NULL,'2015-08-12',1,'2025-09-02 10:18:52',NULL,'+252 65 8068461','$2y$10$4Gy6vMsIbW5Anrj4/mejY.uKOnKkQInfBvXSAJQL13LmVkpxxSDD.',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(80,'Saeed Maxamed','saeedmaxamed816@student.somaliland.edu','202524024','2015-01-05',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$QF9E0GbbwbFC7AQbHWKoQ.ob14jLfsKSguDmrHEIzoN7Z80Yy81DG','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-11 04:47:22',''),(81,'Axmed Farah','axmedfarah803@student.somaliland.edu',NULL,'2015-08-30',1,'2025-09-02 10:18:52',NULL,'+252 63 4563312','$2y$10$nQefYTcYfw/Fulf24OmU4O9LatABqK/dS/LOdiNtiufRmNtgglMaG',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:43','2025-09-01 02:38:43',NULL),(82,'Abdullahi Mohamed','abdullahimohamed131@student.somaliland.edu','202526026','2015-01-09',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$2v10dvlXO/EDs5/knpxtxeXUi4f8jdqF5QwMRtLzDfrLVz3palTem','[]',NULL,1,1,70,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:44','2025-09-01 04:33:08','abdullahimohamed131@student.somaliland.edu'),(83,'Hodan Ali','hodanali207@student.somaliland.edu',NULL,'2014-10-11',1,'2025-09-02 10:18:52',NULL,'+252 63 3183997','$2y$10$NKwunAmiMsMN7Mx8BIMP1uXg67l2lLV4YtZftV.UCFf6MxMki8I6q',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:44','2025-09-01 02:38:44',NULL),(84,'Cali Farah','califarah885@student.somaliland.edu','20252028','2014-12-07',1,'2025-09-02 10:18:52',NULL,NULL,'$2y$10$K1Rhgk6rj9opRbDZ1o/WheS7zp/gxs1uOJnIy6aa6i6EnVRLBGIcO','[]',NULL,1,1,71,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:44','2025-09-01 21:38:53',''),(85,'Halima Yusuf','halimayusuf957@student.somaliland.edu',NULL,'2015-01-29',1,'2025-09-02 10:18:52',NULL,'+252 65 1066013','$2y$10$Cz4BF5GN5ilkaMj2ArEEru2xNmwrFkceulzNBq4.bGZiHtw2zJnBK',NULL,NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:44','2025-09-01 02:38:44',NULL),(86,'Halima Guuleed','halimasaeed821@student.somaliland.edu','20254030','2014-11-26',1,'2025-09-02 10:18:52',NULL,'0637260033','$2y$10$xVXqYXoNRV/ymjFKk27ScOt8ACN0xzufsqejANZsGO3PszWMjeqJi','[]',NULL,1,1,NULL,6,NULL,NULL,NULL,NULL,NULL,'2025-09-01 02:38:44','2025-09-01 03:40:06','halimasaeed821@student.somaliland.edu'),(87,'Ahmed Hassan','ahmed.hassan@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634001001','$2y$10$8zi07LFy34WAbtlNwyVzKOweG1uSe15J0WmFy.CHfT6tJngYNqfo.',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(88,'Fatima Osman','fatima.osman@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634002002','$2y$10$7R7zjlrbxx0TTaWLP8J30OWMSuRBip3Pxlx7D36VJLrvL3yW824OK',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(89,'Ibrahim Ali','ibrahim.ali@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634003003','$2y$10$3WZBw8kObFBAW5N/ZPlqS.7oPpP27CwASQi4EevRYq3nw96kwYBkO',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(90,'Amina Mohamed','amina.mohamed@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634004004','$2y$10$VGQPt9N.qaFtyKoAxkGCw.jZaof7ZRNjwouXvG1TxCQyoyjTqqpa.',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(91,'Omar Abdi','omar.abdi@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634005005','$2y$10$Ouakf2qq1ZuSVqmRmoBd/.bE8YT5AGjHQDi.h6kzrlj4nfrlkwAga',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(92,'Khadija Yusuf','khadija.yusuf@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634006006','$2y$10$H3yrZbe5oNkZOPR0FYl0Y.TUy8zaN6/Nu64FjBFe9dMdnXdE3FHX6',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(93,'Yusuf Ibrahim','yusuf.ibrahim@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634007007','$2y$10$Oz4NgijRZL0AXhIRTQ1JxeRMg2.7kc4KYJJS/lM0qATuvZdfQS/ga',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(94,'Saeed Mohamed','saeed.mohamed@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634008008','$2y$10$cGdUYqHw7YfFCUJiOOjH8OT5G2YAAkp.YbBhhhGkkiDgDUd1o/eb.',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(95,'Hodan Ahmed','hodan.ahmed@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634009009','$2y$10$x3zLbiaXH3Vpk3DBEgomB.V9puYAQJSrRc/uyfrdTbeQ8lWXnDKgC',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(96,'Hassan Omar','hassan.omar@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634010010','$2y$10$WNfsz5Big8qTue..ViRrk.dk9m4GshVzH7vN48r7jZFdszZ1r7qx2',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(97,'Mariam Ismail','mariam.ismail@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:16',NULL,'252634011011','$2y$10$4aOlebhecnLfy0FIGjO0d.EnJeJeIH/gxYjSlfeJe/uhGijvsYD8C',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:16','2025-09-01 03:13:16',NULL),(98,'Abdi Hassan','abdi.hassan@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634012012','$2y$10$LLxnlsJGjfI.L6LxWomNpewzzOGofCkQKEqMu.7fvRJe0ULMg4znu',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(99,'Sahra Ali','sahra.ali@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634013013','$2y$10$MfUKls4M2SNZBKCPzOiwAOd3kUq8eU6FZW6t6c5RSaXqZlmQgsFv.',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(100,'Ismail Abdi','ismail.abdi@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634014014','$2y$10$wNCcYtvvDhoIjC.ybIJayu6GGvFtymmpoJDPxfEd5XLIaLvmMMUe2',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(101,'Habiba Omar','habiba.omar@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634015015','$2y$10$44SCuNOTUxp.OiOk6vVC8OwfiSR8ueGfZhI9ptn/HKH/OK3kc0/4S',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(102,'Ali Mohamed','ali.mohamed@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634016016','$2y$10$ZTdH/Z0n.PDVaLtXMXtDYe6/u/IGDmmr4j7gMi8KZOrSYVP4H0dFm',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(103,'Zeinab Ahmed','zeinab.ahmed@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634017017','$2y$10$4AbzWvlCxJz9yUwiJG0zwuTtexGQWpevaMP2zh3Sb.Jhe2a7Wl2k.',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(104,'Mohamed Hassan','mohamed.hassan@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634018018','$2y$10$WtEuSzModGOxxEt0JOZlAucMuPpUZNlf0W7klC1k1ok5NntsuuFC2',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(105,'Halima Ibrahim','halima.ibrahim@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634019019','$2y$10$ba4av/17zKbHEkSfMTdrSeHnRonVleYGtcipqnPA0gqf19lPzVbwS',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(106,'Abdullahi Osman','abdullahi.osman@school.edu.so',NULL,NULL,1,'2025-09-01 03:13:17',NULL,'252634020020','$2y$10$HzpFuGWFUOni9thfA54dmeeaRafk9Le4gLBnXxyci.Sgma8Tx6NGa',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 03:13:17','2025-09-01 03:13:17',NULL),(107,'Sh.Xamse','xamse@somaliland.school.edu.so',NULL,NULL,1,'2025-08-31 23:41:56',NULL,'0634040505','$2y$10$v/xkvNpt1lWoFXJrmffnwuvRsXc1dQxFMCYSaF8ZaS5Xaip6yFiue','[\"user_read\",\"user_create\",\"user_update\",\"user_delete\",\"role_read\",\"role_create\",\"role_update\",\"role_delete\",\"language_read\",\"language_create\",\"language_update_terms\",\"general_settings_read\",\"general_settings_update\",\"storage_settings_read\",\"storage_settings_read\",\"recaptcha_settings_update\",\"email_settings_read\"]',NULL,1,1,NULL,2,NULL,'3b23074e-3675-4d22-a80f-cb67d121c532',NULL,NULL,NULL,'2025-08-31 23:41:56','2025-08-31 23:41:56','xamse@somaliland.school.edu.so'),(108,'Iksiir Ali Jimcale','iksiir@school.edu.so','20254031','2002-07-01',1,'2025-09-02 10:18:52',NULL,'0639837847','$2y$10$eGkTGR6ow1rKZ6DpaCi8iuDrFY.MQVCh3NR0AmH2s3MUBFF2uoSoC','[]',NULL,1,1,NULL,6,NULL,'2fccbb57-665a-44fd-b75d-1577e6dbb7ac',NULL,NULL,NULL,'2025-09-01 00:32:41','2025-09-01 01:25:08','iksiir@school.edu.so'),(109,'Ridwan Ahmed','ridwan@gmail.com','20254032','2003-06-11',1,'2025-09-10 22:48:27',NULL,'0633353722','$2y$10$1GUGR4Zknx5kST/INkuZJO2IrA3S3m6IN.rlaxu6gB5F4xBOJmqX6','[]',NULL,1,1,NULL,6,NULL,'b00625cb-435d-4a2e-80b1-7b1cc3c45b9d',NULL,NULL,NULL,'2025-09-10 22:48:27','2025-09-10 22:48:27','ridwan@gmail.com'),(110,'Hana Yusuf','hana@gmail.com','20254033','2013-06-11',1,'2025-09-11 03:58:44',NULL,'3234433','$2y$10$mQtQXgtqKVUQfdi3wl0E9.VAryZbDefPKfuqVfZC9wc1owH2KjSz.','[]',NULL,1,1,NULL,6,NULL,'6a652a24-d68e-42ee-b288-de416f8e903f',NULL,NULL,NULL,'2025-09-11 03:58:44','2025-09-11 03:58:44','hana@gmail.com'),(118,'Nasriin Ibrahim','nasriin@gmail.com','20254034','2012-06-11',1,'2025-09-11 05:05:39',NULL,NULL,'$2y$10$Rzool/wTKSKXxBCI/tBlzOvoQPu3Nkru5bjBfJzQmw/abvIKvIFSi','[]',NULL,1,1,NULL,6,NULL,'9cbda1e8-153f-47ff-8fd2-7ba96c1d9e1b',NULL,NULL,NULL,'2025-09-11 05:05:39','2025-09-11 05:05:39','nasriin@gmail.com');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-13  3:02:55
