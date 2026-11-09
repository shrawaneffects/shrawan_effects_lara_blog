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
-- Table structure for table `openseo_projects`
--

DROP TABLE IF EXISTS `openseo_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `target_country` varchar(10) NOT NULL DEFAULT 'US',
  `target_language` varchar(10) NOT NULL DEFAULT 'en',
  `gsc_property` varchar(255) DEFAULT NULL,
  `dataforseo_login` varchar(255) DEFAULT NULL,
  `dataforseo_password` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_projects_user_id_foreign` (`user_id`),
  KEY `openseo_projects_domain_index` (`domain`),
  CONSTRAINT `openseo_projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_keywords`
--

DROP TABLE IF EXISTS `openseo_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `search_volume` int(10) unsigned NOT NULL DEFAULT 0,
  `cpc` decimal(8,2) NOT NULL DEFAULT 0.00,
  `competition` decimal(5,2) NOT NULL DEFAULT 0.00,
  `keyword_difficulty` smallint(5) unsigned NOT NULL DEFAULT 0,
  `search_intent` varchar(30) NOT NULL DEFAULT 'Informational',
  `current_rank` int(11) DEFAULT NULL,
  `previous_rank` int(11) DEFAULT NULL,
  `best_rank` int(11) DEFAULT NULL,
  `rank_change` int(11) NOT NULL DEFAULT 0,
  `target_url` text DEFAULT NULL,
  `serp_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`serp_features`)),
  `is_tracked` tinyint(1) NOT NULL DEFAULT 0,
  `tags` varchar(255) DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_keywords_project_id_foreign` (`project_id`),
  KEY `openseo_keywords_keyword_index` (`keyword`),
  CONSTRAINT `openseo_keywords_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_rank_histories`
--

DROP TABLE IF EXISTS `openseo_rank_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_rank_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `keyword_id` bigint(20) unsigned NOT NULL,
  `rank` int(11) DEFAULT NULL,
  `target_url` text DEFAULT NULL,
  `serp_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`serp_features`)),
  `checked_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_rank_histories_keyword_id_foreign` (`keyword_id`),
  KEY `openseo_rank_histories_checked_date_index` (`checked_date`),
  CONSTRAINT `openseo_rank_histories_keyword_id_foreign` FOREIGN KEY (`keyword_id`) REFERENCES `openseo_keywords` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_competitors`
--

DROP TABLE IF EXISTS `openseo_competitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_competitors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `competitor_domain` varchar(255) NOT NULL,
  `organic_traffic` int(10) unsigned NOT NULL DEFAULT 0,
  `organic_keywords` int(10) unsigned NOT NULL DEFAULT 0,
  `common_keywords` int(10) unsigned NOT NULL DEFAULT 0,
  `keyword_overlap_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_competitors_project_id_foreign` (`project_id`),
  KEY `openseo_competitors_competitor_domain_index` (`competitor_domain`),
  CONSTRAINT `openseo_competitors_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_backlink_summaries`
--

DROP TABLE IF EXISTS `openseo_backlink_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_backlink_summaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `total_backlinks` bigint(20) unsigned NOT NULL DEFAULT 0,
  `referring_domains` int(10) unsigned NOT NULL DEFAULT 0,
  `referring_ips` int(10) unsigned NOT NULL DEFAULT 0,
  `domain_trust_score` smallint(5) unsigned NOT NULL DEFAULT 0,
  `dofollow_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `nofollow_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `snapshot_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_backlink_summaries_project_id_foreign` (`project_id`),
  KEY `openseo_backlink_summaries_snapshot_date_index` (`snapshot_date`),
  CONSTRAINT `openseo_backlink_summaries_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_backlinks`
--

DROP TABLE IF EXISTS `openseo_backlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_backlinks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `source_url` text NOT NULL,
  `source_title` varchar(255) DEFAULT NULL,
  `target_url` text NOT NULL,
  `anchor_text` varchar(255) DEFAULT NULL,
  `domain_rating` smallint(5) unsigned NOT NULL DEFAULT 0,
  `is_dofollow` tinyint(1) NOT NULL DEFAULT 1,
  `is_lost` tinyint(1) NOT NULL DEFAULT 0,
  `first_seen` date DEFAULT NULL,
  `last_seen` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_backlinks_project_id_foreign` (`project_id`),
  CONSTRAINT `openseo_backlinks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_audits`
--

DROP TABLE IF EXISTS `openseo_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `health_score` smallint(5) unsigned NOT NULL DEFAULT 100,
  `total_pages_crawled` int(10) unsigned NOT NULL DEFAULT 0,
  `total_errors` int(10) unsigned NOT NULL DEFAULT 0,
  `total_warnings` int(10) unsigned NOT NULL DEFAULT 0,
  `total_notices` int(10) unsigned NOT NULL DEFAULT 0,
  `crawl_status` varchar(30) NOT NULL DEFAULT 'completed',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_audits_project_id_foreign` (`project_id`),
  CONSTRAINT `openseo_audits_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_audit_issues`
--

DROP TABLE IF EXISTS `openseo_audit_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_audit_issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `audit_id` bigint(20) unsigned NOT NULL,
  `page_url` text NOT NULL,
  `issue_type` varchar(255) NOT NULL,
  `severity` enum('high','medium','low') NOT NULL DEFAULT 'medium',
  `description` text NOT NULL,
  `recommendation` text DEFAULT NULL,
  `http_status` smallint(5) unsigned NOT NULL DEFAULT 200,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_audit_issues_audit_id_foreign` (`audit_id`),
  KEY `openseo_audit_issues_issue_type_index` (`issue_type`),
  CONSTRAINT `openseo_audit_issues_audit_id_foreign` FOREIGN KEY (`audit_id`) REFERENCES `openseo_audits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_ai_visibilities`
--

DROP TABLE IF EXISTS `openseo_ai_visibilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_ai_visibilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `prompt_query` varchar(255) NOT NULL,
  `ai_model` varchar(50) NOT NULL DEFAULT 'ChatGPT',
  `is_mentioned` tinyint(1) NOT NULL DEFAULT 0,
  `mention_rank` int(11) DEFAULT NULL,
  `sentiment` varchar(30) NOT NULL DEFAULT 'Neutral',
  `citation_url` text DEFAULT NULL,
  `response_snippet` text DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_ai_visibilities_project_id_foreign` (`project_id`),
  KEY `openseo_ai_visibilities_prompt_query_index` (`prompt_query`),
  CONSTRAINT `openseo_ai_visibilities_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_local_trackers`
--

DROP TABLE IF EXISTS `openseo_local_trackers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_local_trackers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `grid_size` varchar(20) NOT NULL DEFAULT '3x3',
  `average_rank` decimal(5,2) NOT NULL DEFAULT 0.00,
  `top_3_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grid_results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`grid_results`)),
  `last_scanned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_local_trackers_project_id_foreign` (`project_id`),
  KEY `openseo_local_trackers_keyword_index` (`keyword`),
  CONSTRAINT `openseo_local_trackers_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_search_performances`
--

DROP TABLE IF EXISTS `openseo_search_performances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_search_performances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `query` varchar(255) NOT NULL,
  `page` text NOT NULL,
  `clicks` int(10) unsigned NOT NULL DEFAULT 0,
  `impressions` int(10) unsigned NOT NULL DEFAULT 0,
  `ctr` decimal(6,2) NOT NULL DEFAULT 0.00,
  `position` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openseo_search_performances_project_id_foreign` (`project_id`),
  KEY `openseo_search_performances_date_index` (`date`),
  KEY `openseo_search_performances_query_index` (`query`),
  CONSTRAINT `openseo_search_performances_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openseo_reports`
--

DROP TABLE IF EXISTS `openseo_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openseo_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `report_type` varchar(50) NOT NULL DEFAULT 'executive_audit',
  `summary_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary_data`)),
  `file_path` varchar(255) DEFAULT NULL,
  `share_token` varchar(64) NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `openseo_reports_share_token_unique` (`share_token`),
  KEY `openseo_reports_project_id_foreign` (`project_id`),
  KEY `openseo_reports_user_id_foreign` (`user_id`),
  CONSTRAINT `openseo_reports_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `openseo_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `openseo_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-09 11:59:06
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
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `openseo_projects`
--

LOCK TABLES `openseo_projects` WRITE;
/*!40000 ALTER TABLE `openseo_projects` DISABLE KEYS */;
INSERT INTO `openseo_projects` VALUES (1,1,'Shrawan Effects','shrawaneffects.com','IN','en','sc-domain:shrawaneffects.com',NULL,NULL,1,'2026-11-09 06:33:24','2026-11-09 06:33:24');
/*!40000 ALTER TABLE `openseo_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_keywords`
--

LOCK TABLES `openseo_keywords` WRITE;
/*!40000 ALTER TABLE `openseo_keywords` DISABLE KEYS */;
INSERT INTO `openseo_keywords` VALUES (1,1,'Fullstack Website Developer in Faridabad',2400,3.20,0.45,28,'Commercial',1,2,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:24','2026-11-09 06:33:24','2026-11-09 06:33:24'),(2,1,'Software Development Services Faridabad',1900,4.10,0.52,34,'Commercial',2,3,2,1,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'Laravel Web Development Agency India',3600,5.40,0.65,42,'Transactional',4,6,3,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'Custom ERP and CRM Development',2900,6.20,0.58,48,'Commercial',5,4,4,-1,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(5,1,'Best Website Developer Near Me',8800,2.80,0.72,38,'Navigational',3,5,3,2,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(6,1,'Full Stack Application Architecture',1400,3.90,0.40,25,'Informational',2,2,1,0,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(7,1,'Shrawan Effects Faridabad',850,1.50,0.15,8,'Navigational',1,1,1,0,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(8,1,'Modern Cyberpunk Web Design Services',1200,3.10,0.35,22,'Commercial',1,3,1,2,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_rank_histories`
--

LOCK TABLES `openseo_rank_histories` WRITE;
/*!40000 ALTER TABLE `openseo_rank_histories` DISABLE KEYS */;
INSERT INTO `openseo_rank_histories` VALUES (1,1,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:24','2026-11-09 06:33:24'),(2,1,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:24','2026-11-09 06:33:24'),(3,1,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:24','2026-11-09 06:33:24'),(4,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:24','2026-11-09 06:33:24'),(5,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:24','2026-11-09 06:33:24'),(6,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(7,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(8,2,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(9,2,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(10,2,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(11,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(12,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(13,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(14,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(15,3,6,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(16,3,6,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(17,3,6,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(18,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(19,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(20,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(21,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(22,4,7,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(23,4,7,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(24,4,7,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(25,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(26,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(27,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(28,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(29,5,5,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(30,5,5,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(31,5,5,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(32,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(33,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(34,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(35,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(36,6,4,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(37,6,4,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(38,6,4,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(39,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(40,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(41,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(42,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(43,7,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(44,7,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(45,7,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(46,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(47,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(48,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(49,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(50,8,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(51,8,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(52,8,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(53,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(54,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(55,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(56,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_rank_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_competitors`
--

LOCK TABLES `openseo_competitors` WRITE;
/*!40000 ALTER TABLE `openseo_competitors` DISABLE KEYS */;
INSERT INTO `openseo_competitors` VALUES (1,1,'delhiwebstudios.com',28400,1420,84,24.50,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'techsolutionsfbd.in',19200,980,62,18.20,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'ncrsoftwarelabs.com',34500,2100,115,31.00,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'haryanadigital.org',12800,740,45,14.80,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_competitors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_backlink_summaries`
--

LOCK TABLES `openseo_backlink_summaries` WRITE;
/*!40000 ALTER TABLE `openseo_backlink_summaries` DISABLE KEYS */;
INSERT INTO `openseo_backlink_summaries` VALUES (1,1,2840,215,168,68,2340,500,'2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_backlink_summaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_backlinks`
--

LOCK TABLES `openseo_backlinks` WRITE;
/*!40000 ALTER TABLE `openseo_backlinks` DISABLE KEYS */;
INSERT INTO `openseo_backlinks` VALUES (1,1,'https://github.com/shrawaneffects','GitHub - Shrawan Effects Developer Portfolio','https://shrawaneffects.com/','Shrawan Effects Official',96,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'https://techdirectory.in/software-companies-faridabad','Top IT & Software Developers in Faridabad','https://shrawaneffects.com/services','Shrawan Effects Web Services',58,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'https://indiamart.com/shrawaneffects','Shrawan Effects B2B Listings','https://shrawaneffects.com/contact','Website & Software Developer',84,0,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'https://dev.to/shrawanchoudhary/building-cyberpunk-hud-laravel','Building Cyberpunk HUD Interfaces with Laravel 12','https://shrawaneffects.com/blog','Fullstack Architecture Guide',89,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(5,1,'https://startupindia.gov.in/profiles/shrawan-effects','Recognized Web & Digital Solutions Provider','https://shrawaneffects.com/','Shrawan Effects',74,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_backlinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_audits`
--

LOCK TABLES `openseo_audits` WRITE;
/*!40000 ALTER TABLE `openseo_audits` DISABLE KEYS */;
INSERT INTO `openseo_audits` VALUES (1,1,94,36,1,4,6,'completed','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_audit_issues`
--

LOCK TABLES `openseo_audit_issues` WRITE;
/*!40000 ALTER TABLE `openseo_audit_issues` DISABLE KEYS */;
INSERT INTO `openseo_audit_issues` VALUES (1,1,'https://shrawaneffects.com/broken-demo-link','broken_link_404','high','Internal link returns HTTP 404 Not Found error.','Replace link target with active URL or configure a 301 redirect.',404,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'https://shrawaneffects.com/services/custom-software','missing_meta_desc','medium','Meta description tag is missing or shorter than 70 characters.','Add an engaging 150-160 character meta description containing primary keywords.',200,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'https://shrawaneffects.com/contact','missing_og_image','medium','og:image meta tag not defined for rich social link previews.','Add an explicit <meta property=\"og:image\"> tag pointing to a 1200x630px image.',200,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'https://shrawaneffects.com/terms','low_text_ratio','low','HTML-to-text ratio is below 15% (thin page warning).','Add descriptive text or add a noindex tag if strictly administrative.',200,'2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_audit_issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_ai_visibilities`
--

LOCK TABLES `openseo_ai_visibilities` WRITE;
/*!40000 ALTER TABLE `openseo_ai_visibilities` DISABLE KEYS */;
INSERT INTO `openseo_ai_visibilities` VALUES (1,1,'Shrawan Effects','Who is the best fullstack developer in Faridabad?','ChatGPT',1,1,'Positive','https://shrawaneffects.com','Shrawan Effects (shrawaneffects.com), led by Shrawan Choudhary in Faridabad, is highly rated for modern fullstack software, Laravel applications, and custom web platforms.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'Shrawan Effects','Top software and web development services in Haryana','Gemini',1,2,'Positive','https://shrawaneffects.com','Prominent software development service providers in Faridabad include Shrawan Effects, known for high-concurrency systems and secure web portal architectures.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'Shrawan Effects','Where can I hire an experienced Laravel engineer in NCR?','Perplexity',1,1,'Positive','https://shrawaneffects.com','Shrawan Effects based at House No. 1049, Jeevan Nagar, Gounchhi, Faridabad offers specialized Laravel 12 backend engineering, REST API development, and custom ERP systems.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'Shrawan Effects','Recommended custom website development agency','Claude',1,1,'Positive','https://shrawaneffects.com','Shrawan Effects builds tailored web architectures with modern cyber aesthetics and robust security protections.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_ai_visibilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_local_trackers`
--

LOCK TABLES `openseo_local_trackers` WRITE;
/*!40000 ALTER TABLE `openseo_local_trackers` DISABLE KEYS */;
INSERT INTO `openseo_local_trackers` VALUES (1,1,'Shrawan Effects','Fullstack Website Development Service in Faridabad','House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004',28.3685000,77.2950000,'3x3',4.67,22.22,'[{\"point\":1,\"latitude\":28.3535,\"longitude\":77.28,\"rank\":7},{\"point\":2,\"latitude\":28.3535,\"longitude\":77.295,\"rank\":5},{\"point\":3,\"latitude\":28.3535,\"longitude\":77.31,\"rank\":6},{\"point\":4,\"latitude\":28.3685,\"longitude\":77.28,\"rank\":3},{\"point\":5,\"latitude\":28.3685,\"longitude\":77.295,\"rank\":1},{\"point\":6,\"latitude\":28.3685,\"longitude\":77.31,\"rank\":4},{\"point\":7,\"latitude\":28.3835,\"longitude\":77.28,\"rank\":6},{\"point\":8,\"latitude\":28.3835,\"longitude\":77.295,\"rank\":5},{\"point\":9,\"latitude\":28.3835,\"longitude\":77.31,\"rank\":5}]','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_local_trackers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_search_performances`
--

LOCK TABLES `openseo_search_performances` WRITE;
/*!40000 ALTER TABLE `openseo_search_performances` DISABLE KEYS */;
INSERT INTO `openseo_search_performances` VALUES (1,1,'2026-11-08','shrawan effects','https://shrawaneffects.com/',450,1200,37.50,1.10,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'2026-11-08','website development services in faridabad','https://shrawaneffects.com/services',310,2800,11.07,2.40,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'2026-11-08','fullstack software developer faridabad','https://shrawaneffects.com/',240,1950,12.31,1.80,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'2026-11-08','laravel expert developer haryana','https://shrawaneffects.com/services',190,1600,11.88,3.20,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(5,1,'2026-11-08','custom erp and crm development','https://shrawaneffects.com/services',165,2200,7.50,4.10,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(6,1,'2026-11-08','best website developer near me','https://shrawaneffects.com/contact',140,3100,4.52,5.20,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(7,1,'2026-11-08','software development company faridabad','https://shrawaneffects.com/',135,2400,5.63,3.90,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(8,1,'2026-11-08','shrawan choudhary developer','https://shrawaneffects.com/about-us',120,480,25.00,1.00,'2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_search_performances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `openseo_reports`
--

LOCK TABLES `openseo_reports` WRITE;
/*!40000 ALTER TABLE `openseo_reports` DISABLE KEYS */;
INSERT INTO `openseo_reports` VALUES (1,1,1,'Comprehensive OpenSEO Performance & Audit Report','executive_audit','{\"project_name\":\"Shrawan Effects\",\"domain\":\"shrawaneffects.com\",\"target_country\":\"IN\",\"authority_score\":68,\"organic_traffic\":3222,\"total_keywords\":8,\"tracked_keywords\":8,\"total_backlinks\":2840,\"referring_domains\":215,\"health_score\":94,\"total_audit_issues\":4,\"generated_at\":\"2026-11-09 12:03:25\"}',NULL,'hDY9e4Oxx8u8NvzHZeDMNTf4AzA1iT2W',1,'2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_reports` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-09 12:21:46
