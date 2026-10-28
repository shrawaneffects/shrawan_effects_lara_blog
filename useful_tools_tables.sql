-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: laravel_blog
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `useful_tool_projects`
--

DROP TABLE IF EXISTS `useful_tool_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('business_scraper','seo_analyzer') NOT NULL DEFAULT 'business_scraper',
  `target_domain` varchar(255) DEFAULT NULL,
  `target_country` varchar(10) NOT NULL DEFAULT 'IN',
  `target_location` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_projects_user_id_type_index` (`user_id`,`type`),
  CONSTRAINT `useful_tool_projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_projects`
--

LOCK TABLES `useful_tool_projects` WRITE;
/*!40000 ALTER TABLE `useful_tool_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_scraping_jobs`
--

DROP TABLE IF EXISTS `useful_tool_scraping_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_scraping_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `provider` varchar(50) NOT NULL DEFAULT 'google_places',
  `query_keyword` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'India',
  `max_results` int(11) NOT NULL DEFAULT 50,
  `status` enum('pending','running','completed','partially_completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `total_found` int(11) NOT NULL DEFAULT 0,
  `total_collected` int(11) NOT NULL DEFAULT 0,
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_scraping_jobs_project_id_foreign` (`project_id`),
  KEY `useful_tool_scraping_jobs_user_id_status_index` (`user_id`,`status`),
  KEY `useful_tool_scraping_jobs_provider_index` (`provider`),
  CONSTRAINT `useful_tool_scraping_jobs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `useful_tool_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `useful_tool_scraping_jobs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_scraping_jobs`
--

LOCK TABLES `useful_tool_scraping_jobs` WRITE;
/*!40000 ALTER TABLE `useful_tool_scraping_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_scraping_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_businesses`
--

DROP TABLE IF EXISTS `useful_tool_businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_businesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `job_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `source_platform` varchar(50) NOT NULL,
  `source_record_id` varchar(255) DEFAULT NULL,
  `normalized_identifier` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(30) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `listing_url` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `review_count` int(11) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `data_quality_status` enum('verified','partial','unverified') NOT NULL DEFAULT 'partial',
  `first_discovered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_checked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_businesses_job_id_foreign` (`job_id`),
  KEY `useful_tool_businesses_project_id_foreign` (`project_id`),
  KEY `useful_tool_businesses_user_id_city_index` (`user_id`,`city`),
  KEY `useful_tool_businesses_source_platform_source_record_id_index` (`source_platform`,`source_record_id`),
  KEY `useful_tool_businesses_source_record_id_index` (`source_record_id`),
  KEY `useful_tool_businesses_normalized_identifier_index` (`normalized_identifier`),
  KEY `useful_tool_businesses_city_index` (`city`),
  CONSTRAINT `useful_tool_businesses_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `useful_tool_scraping_jobs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `useful_tool_businesses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `useful_tool_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `useful_tool_businesses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_businesses`
--

LOCK TABLES `useful_tool_businesses` WRITE;
/*!40000 ALTER TABLE `useful_tool_businesses` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_businesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_tracked_keywords`
--

DROP TABLE IF EXISTS `useful_tool_tracked_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_tracked_keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `domain` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `country` varchar(10) NOT NULL DEFAULT 'IN',
  `location` varchar(100) DEFAULT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `device` enum('desktop','mobile') NOT NULL DEFAULT 'desktop',
  `search_engine` varchar(50) NOT NULL DEFAULT 'google',
  `current_position` int(11) DEFAULT NULL,
  `previous_position` int(11) DEFAULT NULL,
  `position_change` int(11) NOT NULL DEFAULT 0,
  `best_position` int(11) DEFAULT NULL,
  `ranking_url` text DEFAULT NULL,
  `search_volume` int(11) DEFAULT NULL,
  `keyword_difficulty` int(11) DEFAULT NULL,
  `cpc` decimal(8,2) DEFAULT NULL,
  `search_intent` varchar(50) DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_tracked_keywords_project_id_foreign` (`project_id`),
  KEY `useful_tool_tracked_keywords_user_id_domain_index` (`user_id`,`domain`),
  KEY `useful_tool_tracked_keywords_domain_current_position_index` (`domain`,`current_position`),
  KEY `useful_tool_tracked_keywords_domain_index` (`domain`),
  KEY `useful_tool_tracked_keywords_keyword_index` (`keyword`),
  CONSTRAINT `useful_tool_tracked_keywords_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `useful_tool_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `useful_tool_tracked_keywords_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_tracked_keywords`
--

LOCK TABLES `useful_tool_tracked_keywords` WRITE;
/*!40000 ALTER TABLE `useful_tool_tracked_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_tracked_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_rankings`
--

DROP TABLE IF EXISTS `useful_tool_rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_rankings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `keyword_id` bigint(20) unsigned NOT NULL,
  `request_uid` varchar(64) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `is_found` tinyint(1) NOT NULL DEFAULT 0,
  `ranking_url` text DEFAULT NULL,
  `previous_position` int(11) DEFAULT NULL,
  `position_change` int(11) NOT NULL DEFAULT 0,
  `checked_date` date NOT NULL,
  `provider` varchar(50) NOT NULL,
  `provider_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`provider_metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `useful_tool_rankings_request_uid_unique` (`request_uid`),
  KEY `useful_tool_rankings_keyword_id_checked_date_index` (`keyword_id`,`checked_date`),
  KEY `useful_tool_rankings_checked_date_index` (`checked_date`),
  CONSTRAINT `useful_tool_rankings_keyword_id_foreign` FOREIGN KEY (`keyword_id`) REFERENCES `useful_tool_tracked_keywords` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_rankings`
--

LOCK TABLES `useful_tool_rankings` WRITE;
/*!40000 ALTER TABLE `useful_tool_rankings` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_rankings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_competitors`
--

DROP TABLE IF EXISTS `useful_tool_competitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_competitors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `main_domain` varchar(255) NOT NULL,
  `competitor_domain` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_competitors_project_id_foreign` (`project_id`),
  KEY `useful_tool_competitors_user_id_main_domain_index` (`user_id`,`main_domain`),
  KEY `useful_tool_competitors_main_domain_index` (`main_domain`),
  KEY `useful_tool_competitors_competitor_domain_index` (`competitor_domain`),
  CONSTRAINT `useful_tool_competitors_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `useful_tool_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `useful_tool_competitors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_competitors`
--

LOCK TABLES `useful_tool_competitors` WRITE;
/*!40000 ALTER TABLE `useful_tool_competitors` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_competitors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_traffic_snapshots`
--

DROP TABLE IF EXISTS `useful_tool_traffic_snapshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_traffic_snapshots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `domain` varchar(255) NOT NULL,
  `data_type` enum('estimated_third_party','gsc_first_party','ga4_first_party') NOT NULL DEFAULT 'estimated_third_party',
  `provider` varchar(50) NOT NULL,
  `reporting_period` varchar(30) NOT NULL DEFAULT 'last_30_days',
  `monthly_visits` bigint(20) DEFAULT NULL,
  `unique_visitors` bigint(20) DEFAULT NULL,
  `organic_traffic` bigint(20) DEFAULT NULL,
  `paid_traffic` bigint(20) DEFAULT NULL,
  `direct_traffic` bigint(20) DEFAULT NULL,
  `referral_traffic` bigint(20) DEFAULT NULL,
  `social_traffic` bigint(20) DEFAULT NULL,
  `bounce_rate` decimal(5,2) DEFAULT NULL,
  `avg_visit_duration` int(11) DEFAULT NULL,
  `pages_per_visit` decimal(4,2) DEFAULT NULL,
  `top_countries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`top_countries`)),
  `top_pages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`top_pages`)),
  `traffic_growth_pct` decimal(5,2) DEFAULT NULL,
  `snapshot_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_traffic_snapshots_user_id_domain_data_type_index` (`user_id`,`domain`,`data_type`),
  KEY `useful_tool_traffic_snapshots_domain_index` (`domain`),
  KEY `useful_tool_traffic_snapshots_snapshot_date_index` (`snapshot_date`),
  CONSTRAINT `useful_tool_traffic_snapshots_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_traffic_snapshots`
--

LOCK TABLES `useful_tool_traffic_snapshots` WRITE;
/*!40000 ALTER TABLE `useful_tool_traffic_snapshots` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_traffic_snapshots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_exports`
--

DROP TABLE IF EXISTS `useful_tool_exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_exports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tool_type` enum('business_scraper','seo_keywords','seo_rankings','seo_traffic') NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` text NOT NULL,
  `format` enum('csv','xlsx','json') NOT NULL DEFAULT 'csv',
  `total_records` int(11) NOT NULL DEFAULT 0,
  `file_size_bytes` bigint(20) NOT NULL DEFAULT 0,
  `download_token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `useful_tool_exports_download_token_unique` (`download_token`),
  KEY `useful_tool_exports_user_id_tool_type_index` (`user_id`,`tool_type`),
  KEY `useful_tool_exports_expires_at_index` (`expires_at`),
  CONSTRAINT `useful_tool_exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_exports`
--

LOCK TABLES `useful_tool_exports` WRITE;
/*!40000 ALTER TABLE `useful_tool_exports` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_exports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useful_tool_usage_logs`
--

DROP TABLE IF EXISTS `useful_tool_usage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useful_tool_usage_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tool_type` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `operation` varchar(100) NOT NULL,
  `query_string` varchar(255) DEFAULT NULL,
  `records_count` int(11) NOT NULL DEFAULT 0,
  `credits_used` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `estimated_cost_usd` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `response_code` int(11) DEFAULT NULL,
  `execution_time_ms` int(11) DEFAULT NULL,
  `is_success` tinyint(1) NOT NULL DEFAULT 1,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useful_tool_usage_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `useful_tool_usage_logs_provider_operation_index` (`provider`,`operation`),
  KEY `useful_tool_usage_logs_provider_index` (`provider`),
  CONSTRAINT `useful_tool_usage_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useful_tool_usage_logs`
--

LOCK TABLES `useful_tool_usage_logs` WRITE;
/*!40000 ALTER TABLE `useful_tool_usage_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `useful_tool_usage_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-28 17:44:54
