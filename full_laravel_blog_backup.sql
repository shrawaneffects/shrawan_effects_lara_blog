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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `severity` varchar(255) NOT NULL DEFAULT 'info',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'login_success','User \'Alex Morgan\' (admin) signed in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','{\"email\":\"admin@blog.com\",\"role\":\"admin\"}','info','2026-08-27 15:00:49','2026-08-27 15:00:49'),(60,1,'logout','User \'Alex Morgan\' signed out','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"admin@blog.com\"}','info','2026-08-30 17:27:04','2026-08-30 17:27:04'),(61,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"attempts\":1}','warning','2026-08-30 17:30:11','2026-08-30 17:30:11'),(62,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"attempts\":2}','warning','2026-08-30 17:30:25','2026-08-30 17:30:25'),(63,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"attempts\":1}','warning','2026-08-30 17:34:31','2026-08-30 17:34:31'),(64,1,'login_success','User \'Alex Morgan\' (admin) signed in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"admin@blog.com\",\"role\":\"admin\"}','info','2026-08-30 17:34:52','2026-08-30 17:34:52'),(116,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\' (attempt 1/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":1}','warning','2026-09-04 07:01:58','2026-09-04 07:01:58'),(117,NULL,'login_failed','Failed login attempt for email \'info@shrawaneffects.com\' (attempt 1/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"info@shrawaneffects.com\",\"daily_attempts\":1}','warning','2026-09-04 07:02:18','2026-09-04 07:02:18'),(118,NULL,'login_otp_dispatched','Login 2FA verification code sent to \'testuser@example.com\'','127.0.0.1','Symfony','{\"email\":\"testuser@example.com\"}','info','2026-09-04 09:30:43','2026-09-04 09:30:43'),(119,NULL,'login_otp_dispatched','Login 2FA verification code sent to \'verifyfail@example.com\'','127.0.0.1','Symfony','{\"email\":\"verifyfail@example.com\"}','info','2026-09-04 09:30:44','2026-09-04 09:30:44'),(120,NULL,'login_otp_failed','Invalid OTP code submitted for \'verifyfail@example.com\' (attempt 1/5 today)','127.0.0.1','Symfony','{\"email\":\"verifyfail@example.com\",\"daily_attempts\":1}','warning','2026-09-04 09:30:44','2026-09-04 09:30:44'),(121,NULL,'login_otp_dispatched','Login 2FA verification code sent to \'successuser@example.com\'','127.0.0.1','Symfony','{\"email\":\"successuser@example.com\"}','info','2026-09-04 09:30:44','2026-09-04 09:30:44'),(122,NULL,'login_2fa_success','User \'Prof. Florencio Ebert\' (successuser@example.com) successfully authenticated via 2FA OTP','127.0.0.1','Symfony','{\"email\":\"successuser@example.com\",\"role\":\"user\"}','info','2026-09-04 09:30:44','2026-09-04 09:30:44'),(123,NULL,'login_daily_locked','Daily attempt limit reached for \'lockedout@example.com\'','127.0.0.1','Symfony','{\"email\":\"lockedout@example.com\"}','danger','2026-09-04 09:30:44','2026-09-04 09:30:44'),(124,NULL,'login_daily_locked','Daily attempt limit reached for \'lockedout@example.com\'','127.0.0.1','Symfony','{\"email\":\"lockedout@example.com\"}','danger','2026-09-04 09:31:13','2026-09-04 09:31:13'),(125,NULL,'account_registered','New user account registered and verified via email: \'John Signup\' (johnsignup@example.com)','127.0.0.1','Symfony','{\"email\":\"johnsignup@example.com\"}','info','2026-09-04 09:31:13','2026-09-04 09:31:13'),(155,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\' (attempt 1/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":1}','warning','2026-09-04 14:29:48','2026-09-04 14:29:48'),(163,111,'login_otp_dispatched','Login 2FA verification code sent to \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\"}','info','2026-09-04 14:41:34','2026-09-04 14:41:34'),(164,111,'login_otp_failed','Invalid OTP code submitted for \'shrawaneffects@gmail.com\' (attempt 1/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":1}','warning','2026-09-04 14:41:49','2026-09-04 14:41:49'),(165,111,'login_otp_dispatched','Login 2FA verification code sent to \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\"}','info','2026-09-04 14:42:17','2026-09-04 14:42:17'),(166,111,'login_2fa_success','User \'Shrawan Choudhary\' (shrawaneffects@gmail.com) successfully authenticated via 2FA OTP','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"role\":\"admin\"}','info','2026-09-04 14:42:24','2026-09-04 14:42:24'),(167,111,'profile_updated','User \'Shrawan Choudhary\' updated profile details','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','[]','info','2026-09-04 14:42:53','2026-09-04 14:42:53'),(168,111,'logout','User \'Shrawan Choudhary\' signed out','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\"}','info','2026-09-04 14:43:59','2026-09-04 14:43:59'),(169,2,'login_otp_dispatched','Login 2FA verification code sent to \'author@blog.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"author@blog.com\"}','info','2026-09-04 14:44:22','2026-09-04 14:44:22'),(170,2,'login_2fa_success','User \'Sarah Jenkins\' (author@blog.com) successfully authenticated via 2FA OTP','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"author@blog.com\",\"role\":\"author\"}','info','2026-09-04 14:44:29','2026-09-04 14:44:29'),(171,2,'profile_updated','User \'Sarah Jenkins\' updated profile details','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','[]','info','2026-09-04 14:45:05','2026-09-04 14:45:05'),(172,2,'logout','User \'Sarah Jenkins\' signed out','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"author@blog.com\"}','info','2026-09-04 14:45:12','2026-09-04 14:45:12'),(173,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\' (attempt 1/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":1}','warning','2026-09-09 13:12:29','2026-09-09 13:12:29'),(174,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\' (attempt 2/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":2}','warning','2026-09-09 13:12:40','2026-09-09 13:12:40'),(175,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\' (attempt 3/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":3}','warning','2026-09-09 13:12:54','2026-09-09 13:12:54'),(176,1,'login_otp_dispatched','Login 2FA verification code sent to \'admin@blog.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"admin@blog.com\"}','info','2026-09-09 13:15:04','2026-09-09 13:15:04'),(177,111,'login_otp_dispatched','Login 2FA verification code sent to \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\"}','info','2026-09-09 13:15:33','2026-09-09 13:15:33'),(178,NULL,'login_failed','Failed login attempt for email \'shrawaneffects@gmail.com\' (attempt 1/5 today)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\",\"daily_attempts\":1}','warning','2026-10-03 14:23:30','2026-10-03 14:23:30'),(179,111,'magic_link_dispatched','Magic sign-in link sent to \'shrawaneffects@gmail.com\'','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\"}','info','2026-10-05 02:18:08','2026-10-05 02:18:08'),(180,111,'magic_link_login_success','User \'Shrawan Choudhary\' (shrawaneffects@gmail.com) successfully authenticated via Magic Link','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','{\"email\":\"shrawaneffects@gmail.com\"}','info','2026-10-05 02:18:22','2026-10-05 02:18:22');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('shrawan_effects_cache_daily_limit_2026-09-04_login_6c5d2453b1f46ceea6ff06481da97783','i:10;',1788546599),('shrawan_effects_cache_daily_limit_2026-09-04_login_9b31f2aaa82c84e4479451bd6c9d1b55','i:1;',1788546599);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#0d6efd',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Laravel & PHP','laravel-php','In-depth tutorials, tips, and best practices for modern PHP and Laravel ecosystem.','https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=800&q=80','#ff2d20',1,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(2,'Web Development','web-development','Explore frontend, backend, APIs, and modern full-stack web engineering.','https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80','#0d6efd',1,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(3,'UI/UX & Design','ui-ux-design','Design systems, Bootstrap styling, user experience paradigms, and modern interfaces.','https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=800&q=80','#6f42c1',1,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(4,'AI & Machine Learning','ai-machine-learning','Insights into LLMs, neural networks, automation, and AI-driven development.','https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=800&q=80','#0dcaf0',1,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(5,'Cloud & DevOps','cloud-devops','Docker, CI/CD pipelines, Kubernetes, serverless, and deployment strategies.','https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?auto=format&fit=crop&w=800&q=80','#198754',1,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(6,'Career & Insights','career-insights','Productivity hacks, tech career growth, interviewing, and engineering leadership.','https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80','#fd7e14',1,'2026-08-22 04:19:09','2026-08-22 04:19:09');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('approved','pending','spam') NOT NULL DEFAULT 'approved',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_post_id_foreign` (`post_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,1,3,NULL,NULL,NULL,'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(2,1,1,1,NULL,NULL,'Thank you David! Glad you found the examples practical. More deep-dives coming soon!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(3,1,NULL,NULL,'Michael Scott','michael@dunder.com','Really enjoyed this article. Bookmarked for my team!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(4,2,3,NULL,NULL,NULL,'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(5,2,1,4,NULL,NULL,'Thank you David! Glad you found the examples practical. More deep-dives coming soon!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(6,2,NULL,NULL,'Michael Scott','michael@dunder.com','Really enjoyed this article. Bookmarked for my team!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(7,3,3,NULL,NULL,NULL,'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(8,3,1,7,NULL,NULL,'Thank you David! Glad you found the examples practical. More deep-dives coming soon!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(9,3,NULL,NULL,'Michael Scott','michael@dunder.com','Really enjoyed this article. Bookmarked for my team!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(10,4,3,NULL,NULL,NULL,'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(11,4,1,10,NULL,NULL,'Thank you David! Glad you found the examples practical. More deep-dives coming soon!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(12,4,NULL,NULL,'Michael Scott','michael@dunder.com','Really enjoyed this article. Bookmarked for my team!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(13,5,3,NULL,NULL,NULL,'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(14,5,1,13,NULL,NULL,'Thank you David! Glad you found the examples practical. More deep-dives coming soon!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(15,5,NULL,NULL,'Michael Scott','michael@dunder.com','Really enjoyed this article. Bookmarked for my team!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(16,6,3,NULL,NULL,NULL,'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(17,6,1,16,NULL,NULL,'Thank you David! Glad you found the examples practical. More deep-dives coming soon!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(18,6,NULL,NULL,'Michael Scott','michael@dunder.com','Really enjoyed this article. Bookmarked for my team!','approved',NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_verifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'login',
  `token` varchar(64) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_verifications_token_unique` (`token`),
  KEY `email_verifications_user_id_foreign` (`user_id`),
  KEY `email_verifications_email_index` (`email`),
  KEY `email_verifications_type_index` (`type`),
  KEY `email_verifications_expires_at_index` (`expires_at`),
  CONSTRAINT `email_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_verifications`
--

LOCK TABLES `email_verifications` WRITE;
/*!40000 ALTER TABLE `email_verifications` DISABLE KEYS */;
INSERT INTO `email_verifications` VALUES (1,NULL,'newuser_KwtSa@shrawaneffects.com','789123','register','HDPvnx2IfLTqT21s326uYmrYAAGXCEv5u7UhHf6732RxCBa1WshergMGhObG09XR','{\"name\":\"Shrawan Test User\",\"password\":\"$2y$12$a15b9S5g8RdsCNh7A3H2auNdnQj4hfncQBCTy\\/x5BWi3ZAu4d9Tiy\"}',0,'2026-09-04 07:02:15','2026-09-04 06:52:15','2026-09-04 06:52:15'),(2,NULL,'newuser_HEu4e@shrawaneffects.com','789123','register','B437uGrxs6m2kba028xZN5AoomWtP9p13BvMs3FBEmBDrkt9kx2jsmwdkneXdFGR','{\"name\":\"Shrawan Test User\",\"password\":\"$2y$12$YBWyuqgXDizcXRu2kyU8F.tBA9CHl3ZbIhMH\\/aWNzDsH3YsuFFuZS\"}',0,'2026-09-04 07:02:33','2026-09-04 06:52:33','2026-09-04 06:52:33'),(3,90,'newuser_HEu4e@shrawaneffects.com','456789','login','VfQ2MU7CJYodmyKJtpkZExttXkxyIwOHhVkgU7qYRZLbUFCXV4g0QdiNMhBMNge8',NULL,0,'2026-09-04 07:02:33','2026-09-04 06:52:33','2026-09-04 06:52:33'),(30,1,'admin@blog.com','418410','login','VpSXV0KJRdcqgwRu2VsVBrlcrKso0k0ISG1eMRqAbnUdsPzuVOmJJ9a6MZecZtC0',NULL,0,'2026-09-09 13:35:01','2026-09-09 13:15:01','2026-09-09 13:15:01'),(31,111,'shrawaneffects@gmail.com','542814','login','386Q63C58F5HehIz3Pom3kPRmXQc8tX87YVY3A1hZiL0rt6RCwj2CeWPB5hOY6nC',NULL,0,'2026-09-09 13:35:30','2026-09-09 13:15:30','2026-09-09 13:15:30');
/*!40000 ALTER TABLE `email_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_verifications`
--

DROP TABLE IF EXISTS `login_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_verifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `token` varchar(100) NOT NULL,
  `remember` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_verifications_token_unique` (`token`),
  KEY `login_verifications_user_id_foreign` (`user_id`),
  CONSTRAINT `login_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_verifications`
--

LOCK TABLES `login_verifications` WRITE;
/*!40000 ALTER TABLE `login_verifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `mime_type` varchar(255) NOT NULL,
  `media_type` enum('image','video','audio','document','other') NOT NULL DEFAULT 'other',
  `file_size` bigint(20) unsigned NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_user_id_foreign` (`user_id`),
  KEY `media_media_type_index` (`media_type`),
  KEY `media_mime_type_index` (`mime_type`),
  KEY `media_created_at_index` (`created_at`),
  CONSTRAINT `media_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL DEFAULT 'header',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `target` varchar(255) NOT NULL DEFAULT '_self',
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_parent_id_foreign` (`parent_id`),
  CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (32,'header',NULL,'Home','/','bi bi-house-door','_self',1,1,'2026-11-09 08:07:35','2026-11-09 08:08:07'),(33,'header',NULL,'Services','/services','bi bi-briefcase','_self',2,1,'2026-11-09 08:07:35','2026-11-09 08:08:07'),(34,'header',33,'Web Development','/services#web-development','bi bi-code-slash','_self',1,1,'2026-11-09 08:07:35','2026-11-09 08:08:07'),(35,'header',34,'Laravel & PHP Systems','/services#laravel','bi bi-terminal','_self',1,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(36,'header',34,'Frontend & Vue Apps','/services#frontend','bi bi-cpu','_self',2,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(37,'header',33,'AI & Automation','/services#ai-solutions',NULL,'_self',2,1,'2026-11-09 08:07:35','2026-11-09 08:08:26'),(38,'header',33,'Cloud & DevOps','/services#cloud-devops','bi bi-cloud-arrow-up','_self',3,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(39,'header',NULL,'OpenSEO Suite','/openseo','bi bi-radar','_self',3,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(40,'header',39,'Domain Overview','/openseo','bi bi-speedometer2','_self',1,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(41,'header',39,'Keyword Research','/openseo/keywords','bi bi-key','_self',2,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(42,'header',39,'Rank Tracker','/openseo/rank-tracking','bi bi-graph-up-arrow','_self',3,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(43,'header',39,'Competitor Insights','/openseo/competitors','bi bi-crosshair','_self',4,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(44,'header',NULL,'Articles','/blog','bi bi-grid','_self',4,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(45,'header',NULL,'FAQ','/faq','bi bi-question-circle','_self',5,1,'2026-11-09 08:07:35','2026-11-09 08:07:35'),(46,'header',NULL,'Contact Us','/contact','bi bi-envelope','_self',6,1,'2026-11-09 08:07:35','2026-11-09 08:07:35');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_19_132347_create_categories_table',1),(5,'2026_08_19_132347_create_tags_table',1),(6,'2026_08_19_132348_create_posts_table',1),(7,'2026_08_19_132349_create_comments_table',1),(8,'2026_08_19_132349_create_contacts_table',1),(9,'2026_08_19_132349_create_newsletter_subscribers_table',1),(10,'2026_08_19_132350_create_post_tag_table',1),(11,'2026_08_19_134441_create_settings_table',1),(12,'2026_08_19_155225_add_toc_and_schema_to_posts_table',1),(13,'2026_08_19_160127_add_faqs_to_posts_table',1),(14,'2026_08_19_161149_create_pages_table',1),(15,'2026_08_19_164405_create_menu_items_table',1),(16,'2026_08_19_170232_create_audit_logs_table',1),(17,'2026_08_19_180000_create_seo_tables',1),(18,'2026_08_30_000001_add_social_and_login_verifications',2),(19,'2026_08_31_223000_create_media_table',3),(20,'2026_09_04_000001_create_email_verifications_table',4),(21,'2026_10_05_000001_create_services_table',5),(23,'2026_11_09_000001_create_openseo_tables',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_subscribers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscribers`
--

LOCK TABLES `newsletter_subscribers` WRITE;
/*!40000 ALTER TABLE `newsletter_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_ai_visibilities`
--

LOCK TABLES `openseo_ai_visibilities` WRITE;
/*!40000 ALTER TABLE `openseo_ai_visibilities` DISABLE KEYS */;
INSERT INTO `openseo_ai_visibilities` VALUES (1,1,'Shrawan Effects','Who is the best fullstack developer in Faridabad?','ChatGPT',1,1,'Positive','https://shrawaneffects.com','Shrawan Effects (shrawaneffects.com), led by Shrawan Choudhary in Faridabad, is highly rated for modern fullstack software, Laravel applications, and custom web platforms.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'Shrawan Effects','Top software and web development services in Haryana','Gemini',1,2,'Positive','https://shrawaneffects.com','Prominent software development service providers in Faridabad include Shrawan Effects, known for high-concurrency systems and secure web portal architectures.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'Shrawan Effects','Where can I hire an experienced Laravel engineer in NCR?','Perplexity',1,1,'Positive','https://shrawaneffects.com','Shrawan Effects based at House No. 1049, Jeevan Nagar, Gounchhi, Faridabad offers specialized Laravel 12 backend engineering, REST API development, and custom ERP systems.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'Shrawan Effects','Recommended custom website development agency','Claude',1,1,'Positive','https://shrawaneffects.com','Shrawan Effects builds tailored web architectures with modern cyber aesthetics and robust security protections.','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_ai_visibilities` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_audit_issues`
--

LOCK TABLES `openseo_audit_issues` WRITE;
/*!40000 ALTER TABLE `openseo_audit_issues` DISABLE KEYS */;
INSERT INTO `openseo_audit_issues` VALUES (1,1,'https://shrawaneffects.com/broken-demo-link','broken_link_404','high','Internal link returns HTTP 404 Not Found error.','Replace link target with active URL or configure a 301 redirect.',404,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'https://shrawaneffects.com/services/custom-software','missing_meta_desc','medium','Meta description tag is missing or shorter than 70 characters.','Add an engaging 150-160 character meta description containing primary keywords.',200,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'https://shrawaneffects.com/contact','missing_og_image','medium','og:image meta tag not defined for rich social link previews.','Add an explicit <meta property=\"og:image\"> tag pointing to a 1200x630px image.',200,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'https://shrawaneffects.com/terms','low_text_ratio','low','HTML-to-text ratio is below 15% (thin page warning).','Add descriptive text or add a noindex tag if strictly administrative.',200,'2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_audit_issues` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_audits`
--

LOCK TABLES `openseo_audits` WRITE;
/*!40000 ALTER TABLE `openseo_audits` DISABLE KEYS */;
INSERT INTO `openseo_audits` VALUES (1,1,94,36,1,4,6,'completed','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_audits` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_backlink_summaries`
--

LOCK TABLES `openseo_backlink_summaries` WRITE;
/*!40000 ALTER TABLE `openseo_backlink_summaries` DISABLE KEYS */;
INSERT INTO `openseo_backlink_summaries` VALUES (1,1,2840,215,168,68,2340,500,'2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_backlink_summaries` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_backlinks`
--

LOCK TABLES `openseo_backlinks` WRITE;
/*!40000 ALTER TABLE `openseo_backlinks` DISABLE KEYS */;
INSERT INTO `openseo_backlinks` VALUES (1,1,'https://github.com/shrawaneffects','GitHub - Shrawan Effects Developer Portfolio','https://shrawaneffects.com/','Shrawan Effects Official',96,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'https://techdirectory.in/software-companies-faridabad','Top IT & Software Developers in Faridabad','https://shrawaneffects.com/services','Shrawan Effects Web Services',58,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'https://indiamart.com/shrawaneffects','Shrawan Effects B2B Listings','https://shrawaneffects.com/contact','Website & Software Developer',84,0,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'https://dev.to/shrawanchoudhary/building-cyberpunk-hud-laravel','Building Cyberpunk HUD Interfaces with Laravel 12','https://shrawaneffects.com/blog','Fullstack Architecture Guide',89,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(5,1,'https://startupindia.gov.in/profiles/shrawan-effects','Recognized Web & Digital Solutions Provider','https://shrawaneffects.com/','Shrawan Effects',74,1,0,'2026-08-09','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_backlinks` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_competitors`
--

LOCK TABLES `openseo_competitors` WRITE;
/*!40000 ALTER TABLE `openseo_competitors` DISABLE KEYS */;
INSERT INTO `openseo_competitors` VALUES (1,1,'delhiwebstudios.com',28400,1420,84,24.50,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'techsolutionsfbd.in',19200,980,62,18.20,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'ncrsoftwarelabs.com',34500,2100,115,31.00,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'haryanadigital.org',12800,740,45,14.80,'Regional organic competitor in web & software development services.','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_competitors` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_keywords`
--

LOCK TABLES `openseo_keywords` WRITE;
/*!40000 ALTER TABLE `openseo_keywords` DISABLE KEYS */;
INSERT INTO `openseo_keywords` VALUES (1,1,'Fullstack Website Developer in Faridabad',2400,3.20,0.45,28,'Commercial',1,2,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:24','2026-11-09 06:33:24','2026-11-09 06:33:24'),(2,1,'Software Development Services Faridabad',1900,4.10,0.52,34,'Commercial',2,3,2,1,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'Laravel Web Development Agency India',3600,5.40,0.65,42,'Transactional',4,6,3,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'Custom ERP and CRM Development',2900,6.20,0.58,48,'Commercial',5,4,4,-1,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(5,1,'Best Website Developer Near Me',8800,2.80,0.72,38,'Navigational',3,5,3,2,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(6,1,'Full Stack Application Architecture',1400,3.90,0.40,25,'Informational',2,2,1,0,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(7,1,'Shrawan Effects Faridabad',850,1.50,0.15,8,'Navigational',1,1,1,0,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25'),(8,1,'Modern Cyberpunk Web Design Services',1200,3.10,0.35,22,'Commercial',1,3,1,2,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\",\"Local Pack\"]',1,'core,services,branding','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_keywords` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_local_trackers`
--

LOCK TABLES `openseo_local_trackers` WRITE;
/*!40000 ALTER TABLE `openseo_local_trackers` DISABLE KEYS */;
INSERT INTO `openseo_local_trackers` VALUES (1,1,'Shrawan Effects','Fullstack Website Development Service in Faridabad','House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004',28.3685000,77.2950000,'3x3',4.67,22.22,'[{\"point\":1,\"latitude\":28.3535,\"longitude\":77.28,\"rank\":7},{\"point\":2,\"latitude\":28.3535,\"longitude\":77.295,\"rank\":5},{\"point\":3,\"latitude\":28.3535,\"longitude\":77.31,\"rank\":6},{\"point\":4,\"latitude\":28.3685,\"longitude\":77.28,\"rank\":3},{\"point\":5,\"latitude\":28.3685,\"longitude\":77.295,\"rank\":1},{\"point\":6,\"latitude\":28.3685,\"longitude\":77.31,\"rank\":4},{\"point\":7,\"latitude\":28.3835,\"longitude\":77.28,\"rank\":6},{\"point\":8,\"latitude\":28.3835,\"longitude\":77.295,\"rank\":5},{\"point\":9,\"latitude\":28.3835,\"longitude\":77.31,\"rank\":5}]','2026-11-09 06:33:25','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_local_trackers` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_projects`
--

LOCK TABLES `openseo_projects` WRITE;
/*!40000 ALTER TABLE `openseo_projects` DISABLE KEYS */;
INSERT INTO `openseo_projects` VALUES (1,1,'Shrawan Effects','shrawaneffects.com','IN','en','sc-domain:shrawaneffects.com',NULL,NULL,1,'2026-11-09 06:33:24','2026-11-09 06:33:24');
/*!40000 ALTER TABLE `openseo_projects` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_rank_histories`
--

LOCK TABLES `openseo_rank_histories` WRITE;
/*!40000 ALTER TABLE `openseo_rank_histories` DISABLE KEYS */;
INSERT INTO `openseo_rank_histories` VALUES (1,1,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:24','2026-11-09 06:33:24'),(2,1,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:24','2026-11-09 06:33:24'),(3,1,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:24','2026-11-09 06:33:24'),(4,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:24','2026-11-09 06:33:24'),(5,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:24','2026-11-09 06:33:24'),(6,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(7,1,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(8,2,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(9,2,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(10,2,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(11,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(12,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(13,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(14,2,2,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(15,3,6,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(16,3,6,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(17,3,6,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(18,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(19,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(20,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(21,3,4,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(22,4,7,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(23,4,7,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(24,4,7,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(25,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(26,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(27,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(28,4,5,'https://shrawaneffects.com/services','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(29,5,5,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(30,5,5,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(31,5,5,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(32,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(33,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(34,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(35,5,3,'https://shrawaneffects.com/contact','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(36,6,4,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(37,6,4,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(38,6,4,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(39,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(40,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(41,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(42,6,2,'https://shrawaneffects.com/blog','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(43,7,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(44,7,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(45,7,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(46,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(47,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(48,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(49,7,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25'),(50,8,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-03','2026-11-09 06:33:25','2026-11-09 06:33:25'),(51,8,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-04','2026-11-09 06:33:25','2026-11-09 06:33:25'),(52,8,3,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-05','2026-11-09 06:33:25','2026-11-09 06:33:25'),(53,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-06','2026-11-09 06:33:25','2026-11-09 06:33:25'),(54,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-07','2026-11-09 06:33:25','2026-11-09 06:33:25'),(55,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-08','2026-11-09 06:33:25','2026-11-09 06:33:25'),(56,8,1,'https://shrawaneffects.com/','[\"Featured Snippet\",\"People Also Ask\"]','2026-11-09','2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_rank_histories` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_reports`
--

LOCK TABLES `openseo_reports` WRITE;
/*!40000 ALTER TABLE `openseo_reports` DISABLE KEYS */;
INSERT INTO `openseo_reports` VALUES (1,1,1,'Comprehensive OpenSEO Performance & Audit Report','executive_audit','{\"project_name\":\"Shrawan Effects\",\"domain\":\"shrawaneffects.com\",\"target_country\":\"IN\",\"authority_score\":68,\"organic_traffic\":3222,\"total_keywords\":8,\"tracked_keywords\":8,\"total_backlinks\":2840,\"referring_domains\":215,\"health_score\":94,\"total_audit_issues\":4,\"generated_at\":\"2026-11-09 12:03:25\"}',NULL,'hDY9e4Oxx8u8NvzHZeDMNTf4AzA1iT2W',1,'2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_reports` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `openseo_search_performances`
--

LOCK TABLES `openseo_search_performances` WRITE;
/*!40000 ALTER TABLE `openseo_search_performances` DISABLE KEYS */;
INSERT INTO `openseo_search_performances` VALUES (1,1,'2026-11-08','shrawan effects','https://shrawaneffects.com/',450,1200,37.50,1.10,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(2,1,'2026-11-08','website development services in faridabad','https://shrawaneffects.com/services',310,2800,11.07,2.40,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(3,1,'2026-11-08','fullstack software developer faridabad','https://shrawaneffects.com/',240,1950,12.31,1.80,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(4,1,'2026-11-08','laravel expert developer haryana','https://shrawaneffects.com/services',190,1600,11.88,3.20,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(5,1,'2026-11-08','custom erp and crm development','https://shrawaneffects.com/services',165,2200,7.50,4.10,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(6,1,'2026-11-08','best website developer near me','https://shrawaneffects.com/contact',140,3100,4.52,5.20,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(7,1,'2026-11-08','software development company faridabad','https://shrawaneffects.com/',135,2400,5.63,3.90,'2026-11-09 06:33:25','2026-11-09 06:33:25'),(8,1,'2026-11-08','shrawan choudhary developer','https://shrawaneffects.com/about-us',120,480,25.00,1.00,'2026-11-09 06:33:25','2026-11-09 06:33:25');
/*!40000 ALTER TABLE `openseo_search_performances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `show_in_navbar` tinyint(1) NOT NULL DEFAULT 0,
  `show_in_footer` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'About Us','about-us','<h2>Our Mission & Vision</h2><p class=\"lead\">We are a passionate community of software engineers, architects, and open-source contributors dedicated to crafting high-quality, practical web development insights.</p><h3>What We Cover</h3><p>From full-stack Laravel 12 architecture and reactive UI patterns to cloud scalability and AI developer tooling, our editorial team produces deeply researched tutorials and engineering breakdowns.</p><h3>Join Our Community</h3><p>Interested in contributing or joining our writing team? Reach out through our contact page or connect with our editorial staff.</p>','https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80','About Our Platform - Mission and Community','Learn about our technical publication, mission, editorial values, and contributor community.',NULL,'published',1,1,1,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(2,'Privacy Policy','privacy-policy','<h2>Privacy Policy</h2><p class=\"lead\">Your privacy is important to us. This policy outlines how we collect, protect, and respect your personal information.</p><h3>1. Information Collection</h3><p>We only collect information necessary to provide our services, such as email addresses for newsletter subscribers and account registration credentials for authors and commenters.</p><h3>2. Data Security</h3><p>We employ modern encryption standards, secure session management, and automated database backups to safeguard your information at all times.</p><h3>3. Cookies and Analytics</h3><p>We use minimal session cookies strictly necessary for site functionality, dark/light theme persistence, and authentication.</p>',NULL,'Privacy Policy - Data Security and Protection','Our commitment to data privacy, secure communications, and transparent user controls.',NULL,'published',0,1,2,'2026-08-22 04:19:09','2026-08-22 04:19:09'),(3,'Terms of Service','terms-of-service','<h2>Terms of Service</h2><p class=\"lead\">By accessing and using our publication platform, you agree to comply with and be bound by the following terms and conditions.</p><h3>1. Content License</h3><p>All tutorials, open-source code snippets, and architectural guides are provided for educational and development purposes. Code snippets are available under MIT terms unless specified otherwise.</p><h3>2. Community Guidelines</h3><p>We maintain an inclusive, respectful commenting and discussion space. Spam, harassment, or unlawful content will be moderated and removed immediately.</p>',NULL,'Terms of Service - Platform Usage Guidelines','Review the terms and conditions governing the use of our publication and community platform.',NULL,'published',0,1,3,'2026-08-22 04:19:09','2026-08-22 04:19:09');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_seo`
--

DROP TABLE IF EXISTS `post_seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_seo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `primary_keyword` varchar(255) DEFAULT NULL,
  `search_intent` varchar(255) NOT NULL DEFAULT 'informational',
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots_index` tinyint(1) NOT NULL DEFAULT 1,
  `robots_follow` tinyint(1) NOT NULL DEFAULT 1,
  `include_in_sitemap` tinyint(1) NOT NULL DEFAULT 1,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `twitter_card` varchar(255) NOT NULL DEFAULT 'summary_large_image',
  `schema_type` varchar(255) NOT NULL DEFAULT 'BlogPosting',
  `seo_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `seo_status` varchar(255) NOT NULL DEFAULT 'needs_review',
  `last_audited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_seo_post_id_unique` (`post_id`),
  KEY `post_seo_primary_keyword_index` (`primary_keyword`),
  KEY `post_seo_seo_score_index` (`seo_score`),
  KEY `post_seo_seo_status_index` (`seo_status`),
  CONSTRAINT `post_seo_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_seo`
--

LOCK TABLES `post_seo` WRITE;
/*!40000 ALTER TABLE `post_seo` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_seo_entities`
--

DROP TABLE IF EXISTS `post_seo_entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_seo_entities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_seo_id` bigint(20) unsigned NOT NULL,
  `entity` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'General',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_seo_entities_post_seo_id_type_index` (`post_seo_id`,`type`),
  KEY `post_seo_entities_entity_index` (`entity`),
  CONSTRAINT `post_seo_entities_post_seo_id_foreign` FOREIGN KEY (`post_seo_id`) REFERENCES `post_seo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_seo_entities`
--

LOCK TABLES `post_seo_entities` WRITE;
/*!40000 ALTER TABLE `post_seo_entities` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_seo_entities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_seo_keywords`
--

DROP TABLE IF EXISTS `post_seo_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_seo_keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_seo_id` bigint(20) unsigned NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'secondary',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_seo_keywords_post_seo_id_type_index` (`post_seo_id`,`type`),
  KEY `post_seo_keywords_keyword_index` (`keyword`),
  CONSTRAINT `post_seo_keywords_post_seo_id_foreign` FOREIGN KEY (`post_seo_id`) REFERENCES `post_seo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_seo_keywords`
--

LOCK TABLES `post_seo_keywords` WRITE;
/*!40000 ALTER TABLE `post_seo_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_seo_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_tag`
--

DROP TABLE IF EXISTS `post_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_tag_post_id_tag_id_unique` (`post_id`,`tag_id`),
  KEY `post_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `post_tag_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_tag`
--

LOCK TABLES `post_tag` WRITE;
/*!40000 ALTER TABLE `post_tag` DISABLE KEYS */;
INSERT INTO `post_tag` VALUES (1,1,1,NULL,NULL),(2,1,2,NULL,NULL),(3,1,5,NULL,NULL),(4,1,13,NULL,NULL),(5,2,3,NULL,NULL),(6,2,12,NULL,NULL),(7,3,6,NULL,NULL),(8,3,5,NULL,NULL),(9,3,11,NULL,NULL),(10,4,10,NULL,NULL),(11,4,2,NULL,NULL),(12,4,1,NULL,NULL),(13,4,13,NULL,NULL),(14,5,8,NULL,NULL),(15,5,7,NULL,NULL),(16,5,1,NULL,NULL),(17,6,11,NULL,NULL),(18,7,2,NULL,NULL),(19,7,1,NULL,NULL);
/*!40000 ALTER TABLE `post_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `views_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `reading_time` int(11) NOT NULL DEFAULT 1,
  `enable_toc` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `schema_type` varchar(255) NOT NULL DEFAULT 'BlogPosting',
  `custom_schema` longtext DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`),
  KEY `posts_user_id_foreign` (`user_id`),
  KEY `posts_category_id_foreign` (`category_id`),
  CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,1,'Getting Started with Laravel 12: Modern Architecture and Key Innovations','getting-started-with-laravel-12-modern-architecture','Discover the standout features and structural improvements in Laravel 12 that supercharge full-stack PHP development.','<p class=\"lead\">Laravel 12 brings a streamlined development workflow, refined application skeleton, advanced concurrency utilities, and unprecedented performance tuning out of the box.</p>\n                <h2>Why Laravel 12 is a Game Changer</h2>\n                <p>The latest iteration of the framework reinforces simplicity without sacrificing enterprise-grade capabilities. With lean configuration defaults and improved routing engines, bootstrapping new web applications is faster than ever.</p>\n                <div class=\"p-4 my-4 bg-light rounded-3 border-start border-primary border-4\">\n                    <h5 class=\"fw-bold mb-2 text-primary\"><i class=\"bi bi-lightbulb me-2\"></i>Key Takeaway</h5>\n                    <p class=\"mb-0\">Laravel 12 continues the elegant evolution of modern PHP, pairing minimal boilerplate with robust Eloquent features and zero-friction deployments.</p>\n                </div>\n                <h3>1. Streamlined Project Core</h3>\n                <p>Laravel consolidates configuration and service providers into intuitive registration hooks, cutting down directory clutter while keeping complete customization power at your fingertips.</p>\n                <pre class=\"bg-dark text-white p-3 rounded\"><code>// Bootstrap application routes and middleware seamlessly\nreturn Application::configure(basePath: dirname(__DIR__))\n    ->withRouting(\n        web: __DIR__.\'/../routes/web.php\',\n        commands: __DIR__.\'/../routes/console.php\',\n        health: \'/up\',\n    )\n    ->withMiddleware(function (Middleware $middleware) {\n        // Custom global or group middleware\n    })\n    ->create();</code></pre>\n                <h3>2. High-Performance Eloquent Queries</h3>\n                <p>Eloquent in Laravel 12 features smarter query compiling, automatic eager load safeguards, and optimized JSON column query transformations for MySQL and PostgreSQL.</p>\n                <h3>Conclusion</h3>\n                <p>Whether you are building a personal publication or an enterprise SaaS, Laravel 12 is equipped to deliver speed, reliability, and delight to developers worldwide.</p>','https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80','published',1,1,1423,4,1,'Getting Started with Laravel 12 - Complete Guide','A comprehensive deep dive into Laravel 12 features, performance updates, and best practices.',NULL,'BlogPosting',NULL,'[{\"question\":\"What are the minimum PHP requirements for Laravel 12?\",\"answer\":\"Laravel 12 requires PHP 8.2 or higher, providing access to typed properties, readonly classes, and improved performance.\"},{\"question\":\"How does Laravel 12 handle application configuration?\",\"answer\":\"Laravel 12 streamlines setup via bootstrap\\/app.php using a fluent configuration builder for routes, middleware, and exception handling.\"},{\"question\":\"Can I upgrade existing Laravel 11 applications to Laravel 12?\",\"answer\":\"Yes! The upgrade process is straightforward following Laravel Shift or the official upgrade documentation.\"}]','2026-08-19 04:19:09','2026-08-22 04:19:09','2026-10-03 14:58:59'),(2,2,3,'Mastering Modern Web UI with Bootstrap 5.3 and Theme Tokens','mastering-modern-web-ui-with-bootstrap-5','How to leverage Bootstrap 5.3 CSS variables, dark mode color modes, and utilities to craft stunning interfaces.','<p class=\"lead\">Bootstrap 5.3 makes building responsive, dark-mode ready web interfaces simpler than ever with native color modes and semantic utility tokens.</p>\n                <h2>Native Dark Mode Support</h2>\n                <p>By simply using the <code>data-bs-theme=\"dark\"</code> attribute, you can toggle themes dynamically across your entire application or isolate it to specific cards or navigation bars.</p>\n                <pre class=\"bg-dark text-white p-3 rounded\"><code>&lt;html lang=\"en\" data-bs-theme=\"auto\"&gt;\n  &lt;!-- Navbar with native dark mode --&gt;\n  &lt;nav class=\"navbar navbar-expand-lg bg-body-tertiary\"&gt;\n    &lt;div class=\"container\"&gt;\n      &lt;a class=\"navbar-brand\" href=\"#\"&gt;Brand&lt;/a&gt;\n    &lt;/div&gt;\n  &lt;/nav&gt;\n&lt;/html&gt;</code></pre>\n                <h3>Clean Typography and Card Systems</h3>\n                <p>Pairing Bootstrap with clean Google Fonts like <em>Outfit</em> or <em>Inter</em> gives your blog a polished, editorial look that engages readers.</p>\n                <div class=\"row g-3 my-4\">\n                    <div class=\"col-md-6\">\n                        <div class=\"card h-100 border-0 shadow-sm p-3\">\n                            <h5 class=\"text-primary\"><i class=\"bi bi-palette me-2\"></i>Semantic Palette</h5>\n                            <p class=\"text-muted small\">Standardized color variables adapt automatically between light and dark themes.</p>\n                        </div>\n                    </div>\n                    <div class=\"col-md-6\">\n                        <div class=\"card h-100 border-0 shadow-sm p-3\">\n                            <h5 class=\"text-success\"><i class=\"bi bi-phone me-2\"></i>Fully Responsive</h5>\n                            <p class=\"text-muted small\">Mobile first grid ensures perfect display across phones, tablets, and 4K displays.</p>\n                        </div>\n                    </div>\n                </div>','https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80','published',1,0,981,3,1,'Mastering Modern Web UI with Bootstrap 5.3','Learn how to utilize Bootstrap 5 color modes and component utilities for beautiful blog layouts.',NULL,'BlogPosting',NULL,NULL,'2026-08-17 04:19:09','2026-08-22 04:19:09','2026-08-30 15:16:23'),(3,1,4,'The Rise of Autonomous AI Agents in Full-Stack Engineering','the-rise-of-autonomous-ai-agents','How agentic workflows and tool-calling models are reshaping software development, test automation, and deployment.','<p class=\"lead\">AI is shifting from passive code completions to proactive agentic assistants capable of executing terminal tasks, verifying code, and managing end-to-end architectures.</p>\n                <h2>From Autocomplete to Autonomous Pair Programmers</h2>\n                <p>Engineers today collaborate with agentic systems that understand project context, run test suites, inspect database queries, and fix bugs iteratively.</p>\n                <blockquote>\n                    <p class=\"fst-italic text-secondary\">\"The greatest productivity multiplier in modern software is an agent that can plan, execute, and verify its own output safely.\"</p>\n                </blockquote>\n                <h3>Core Pillars of Effective Agents</h3>\n                <ul>\n                    <li><strong>Tool Integration:</strong> Direct access to filesystem, compilers, linters, and APIs.</li>\n                    <li><strong>Self-Correction:</strong> Ability to read compiler errors and re-evaluate solutions in real-time.</li>\n                    <li><strong>Context Awareness:</strong> Analyzing full repository graphs and dependencies.</li>\n                </ul>','https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=1200&q=80','published',0,1,2150,5,1,'The Rise of Autonomous AI Agents','Explore how AI agents are transforming modern development teams and workflows.',NULL,'BlogPosting',NULL,NULL,'2026-08-20 04:19:09','2026-08-22 04:19:09','2026-08-22 04:19:09'),(4,2,2,'10 Essential Security Practices Every Backend Developer Must Know','10-essential-security-practices-backend-development','Protect your applications against SQL injection, CSRF, XSS, rate-limiting abuse, and authentication exploits.','<p class=\"lead\">Security should never be an afterthought. Here are fundamental practices to harden your backend APIs and web applications against common vectors.</p>\n                <h2>1. Parameterized Queries & ORM Protection</h2>\n                <p>Never concatenate raw SQL strings. Using Eloquent or PDO prepared statements prevents SQL injection vulnerabilities completely.</p>\n                <h2>2. CSRF Token Verification</h2>\n                <p>Always ensure state-changing POST, PUT, and DELETE routes are protected by CSRF tokens or signed tokens.</p>\n                <h2>3. Secure Password Hashing</h2>\n                <p>Employ Bcrypt or Argon2id with adequate cost factors to keep user passwords safely hashed.</p>\n                <h2>4. Rate Limiting</h2>\n                <p>Implement aggressive rate limiting on authentication and API endpoints to thwart brute-force and credential stuffing attacks.</p>','https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80','published',0,0,642,4,1,'10 Essential Backend Security Practices','Learn top security practices to safeguard web applications and APIs.',NULL,'BlogPosting',NULL,NULL,'2026-08-15 04:19:09','2026-08-22 04:19:09','2026-10-03 14:52:20'),(5,1,5,'Containerizing Laravel Applications with Docker and Nginx in Production','containerizing-laravel-with-docker-and-nginx','A step-by-step walkthrough of creating lightweight, production-ready multi-stage Docker builds for Laravel.','<p class=\"lead\">Containerization ensures consistency across local development, staging environments, and production Kubernetes clusters.</p>\n                <h2>Optimizing Multi-Stage Dockerfile</h2>\n                <p>By separating composer build steps from the final PHP-FPM runtime image, you can reduce image sizes from 1GB+ to under 120MB.</p>\n                <pre class=\"bg-dark text-white p-3 rounded\"><code># Build stage\nFROM composer:2 AS vendor\nWORKDIR /app\nCOPY composer.json composer.lock ./\nRUN composer install --no-dev --optimize-autoloader --no-scripts\n\n# Production runtime\nFROM php:8.2-fpm-alpine\nWORKDIR /var/www/html\nCOPY . .\nCOPY --from=vendor /app/vendor ./vendor\nRUN chown -R www-data:www-data storage bootstrap/cache</code></pre>','https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?auto=format&fit=crop&w=1200&q=80','published',0,1,870,4,1,'Dockerizing Laravel Applications','Master multi-stage Docker builds for high performance Laravel deployments.',NULL,'BlogPosting',NULL,NULL,'2026-08-18 04:19:09','2026-08-22 04:19:09','2026-08-22 04:19:09'),(6,2,6,'How to Build an Impactful Tech Portfolio and Land Senior Roles','how-to-build-an-impactful-tech-portfolio','Stand out in engineering hiring cycles by showcasing problem-solving ability, code quality, and system architecture.','<p class=\"lead\">Senior engineering recruiters look for how you solve real business problems, make trade-offs, and mentor others.</p>\n                <h2>Focus on Case Studies Over Generic Clones</h2>\n                <p>Instead of generic to-do apps, showcase real projects with architecture diagrams, performance benchmarks, and lessons learned.</p>','https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80','published',0,0,510,3,1,'Building an Impactful Tech Portfolio','Key strategies to build an impressive developer portfolio.',NULL,'BlogPosting',NULL,NULL,'2026-08-16 04:19:09','2026-08-22 04:19:09','2026-08-22 04:19:09'),(7,1,1,'Upcoming Trends in PHP 8.4 and Beyond','upcoming-trends-in-php-8-4','Draft exploration of property hooks, asymmetric visibility, and newly planned core features.','<p>Draft preview of PHP language enhancements including property hooks and standard library upgrades.</p>','https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80','draft',0,0,0,2,1,'PHP 8.4 Preview','Draft article on PHP 8.4 features.',NULL,'BlogPosting',NULL,NULL,NULL,'2026-08-22 04:19:09','2026-08-22 04:19:09');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_audits`
--

DROP TABLE IF EXISTS `seo_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'needs_review',
  `results_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`results_json`)),
  `audited_by` bigint(20) unsigned DEFAULT NULL,
  `audited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `seo_audits_audited_by_foreign` (`audited_by`),
  KEY `seo_audits_post_id_index` (`post_id`),
  KEY `seo_audits_audited_at_index` (`audited_at`),
  CONSTRAINT `seo_audits_audited_by_foreign` FOREIGN KEY (`audited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `seo_audits_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_audits`
--

LOCK TABLES `seo_audits` WRITE;
/*!40000 ALTER TABLE `seo_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_redirects`
--

DROP TABLE IF EXISTS `seo_redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_redirects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `old_url` varchar(255) NOT NULL,
  `new_url` varchar(255) NOT NULL,
  `status_code` smallint(5) unsigned NOT NULL DEFAULT 301,
  `hits` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seo_redirects_old_url_unique` (`old_url`),
  KEY `seo_redirects_old_url_index` (`old_url`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_redirects`
--

LOCK TABLES `seo_redirects` WRITE;
/*!40000 ALTER TABLE `seo_redirects` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_redirects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(100) DEFAULT 'bi-code-slash',
  `short_description` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `technologies` varchar(255) DEFAULT NULL,
  `pricing_starts_at` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Fullstack Website Development','fullstack-website-development','bi-laptop','High-performance, SEO-optimized custom web applications built with Laravel, PHP, React, Vue, and modern responsive architectures.','<p>At <strong>Shrawan Effects</strong>, we engineer tailored web applications designed for scalability, speed, and conversion. Based in Faridabad, Haryana, our full-stack engineering team builds robust applications using the Laravel framework, modern front-end libraries, and clean architectural design patterns.</p><h4>What We Deliver</h4><ul><li>Custom responsive web application tailored to your exact business workflow.</li><li>Clean, modular, and maintainable MVC codebase adhering to PSR standards.</li><li>High-contrast UI designed for optimal user experience across mobile, tablet, and desktop.</li><li>Database architecture and migration pipelines optimized for query speed.</li><li>Search engine friendly HTML5 semantic layout with schema structured data.</li><li>Full deployment to your production server with SSL and domain configuration.</li></ul>','[\"Custom Fullstack Architecture (Laravel & Modern JS)\",\"Responsive UI\\/UX with High-Speed Rendering\",\"Database Modeling & Query Optimization\",\"Secure Authentication & Role-Based Access\",\"Production Deployment & Server Hardening\"]','Laravel, PHP 8.3, React, Bootstrap 5.3, MySQL, Redis','Starting from ₹40,000 / project','https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80',1,1,1,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(2,'Custom Software & SaaS Architecture','custom-software-saas-development','bi-cpu','Scalable multi-tenant enterprise software, management portals, ERPs, CRM systems, and subscription billing engines.','<p>Turn your complex operational workflows into seamless cloud software. Shrawan Effects specializes in building mission-critical enterprise systems, subscription SaaS platforms, and internal business management portals that automate operations and maximize team productivity.</p><h4>Core Capabilities</h4><ul><li>Multi-tenant SaaS data isolation and tenant onboarding workflows.</li><li>Granular Role-Based Access Control (RBAC) and security audit logs.</li><li>Automated recurring billing, invoicing, and subscription tier management.</li><li>Background job queues and asynchronous task processing.</li><li>Real-time telemetry dashboards and operational analytics.</li></ul>','[\"Multi-Tenant SaaS Architecture\",\"Enterprise Role-Based Access Control (RBAC)\",\"Automated Subscription & Invoice Pipelines\",\"Interactive Real-Time Analytics Dashboards\",\"Background Queue Processing & Scheduled Jobs\"]','Laravel, Livewire, Redis, PostgreSQL, Docker, AWS','Custom Scope Quote','https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',2,1,1,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(3,'E-Commerce Solutions & Online Stores','ecommerce-solutions','bi-cart3','High-conversion digital storefronts with automated inventory, instant payment gateways, order management, and customer analytics.','<p>Launch an online storefront that converts visitors into recurring customers. We develop secure, fast, and feature-complete e-commerce platforms with smooth checkout experiences, real-time inventory tracking, and complete payment gateway integrations.</p><h4>Included Capabilities</h4><ul><li>Fast, frictionless mobile checkout experience.</li><li>Seamless integration with Razorpay, Stripe, Paytm, and Cashfree.</li><li>Automated tax calculation, shipping rate estimators, and invoice generation.</li><li>Coupon codes, promotional discounting, and referral mechanisms.</li><li>Order status tracking, SMS/Email notifications, and customer self-service portal.</li></ul>','[\"Seamless Checkout & Cart Optimization\",\"Razorpay, Stripe & UPI Payment Gateways\",\"Inventory Management & Real-Time Stock Tracking\",\"Coupon & Promotional Campaign Engines\",\"Automated Invoicing & Shipping Label Integration\"]','Laravel, WooCommerce, MySQL, Tailwind CSS, Razorpay, Stripe','Starting from ₹35,000 / project','https://images.unsplash.com/photo-1556742049-0a67c5574f73?auto=format&fit=crop&w=1200&q=80',3,1,1,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(4,'REST API Engineering & System Integration','api-development-system-integration','bi-diagram-3','Robust RESTful and GraphQL APIs, webhook engines, third-party ERP/CRM connections, and automated microservice communication.','<p>Connect your disparate applications, mobile front-ends, and external vendor partners with secure, high-performance API backends designed for high throughput and military-grade reliability.</p><h4>Integration Specializations</h4><ul><li>RESTful & GraphQL API architecture with JSON schema validation.</li><li>Token authentication (Bearer, Sanctum, JWT, OAuth2).</li><li>Webhook listeners and automated outgoing event dispatchers.</li><li>Integration with third-party APIs (WhatsApp Business, payment gateways, ERPs, CRM).</li><li>Comprehensive OpenAPI / Swagger interactive documentation.</li></ul>','[\"High-Throughput RESTful & GraphQL Endpoints\",\"OAuth2, JWT & Laravel Sanctum Authentication\",\"Automated Webhook Engines & Event Streaming\",\"Third-Party CRM \\/ ERP Data Synchronization\",\"Interactive OpenAPI \\/ Swagger Documentation\"]','REST, GraphQL, Laravel Sanctum, Redis, Postman, Webhooks','Starting from ₹20,000 / project','https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1200&q=80',4,1,0,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(5,'Technical SEO & Organic Search Growth','seo-performance-optimization','bi-graph-up-arrow','Deep technical on-page SEO, Google Business local ranking for Faridabad & NCR, Schema markup, Core Web Vitals speed tuning.','<p>Dominate search results with data-driven technical search engine optimization. We address Core Web Vitals, site speed, indexation health, and schema markup to rank your business higher in search engines.</p><h4>SEO Implementation Suite</h4><ul><li>Complete technical SEO audit and architectural crawl analysis.</li><li>Core Web Vitals acceleration targeting 90+ mobile Lighthouse scores.</li><li>Schema.org JSON-LD structured data for rich snippets (LocalBusiness, Articles, FAQs).</li><li>Local Google Business Profile ranking optimization in Faridabad and NCR.</li><li>Canonical URLs, automated sitemap.xml generators, and robots.txt rules.</li></ul>','[\"Core Web Vitals & PageSpeed 95+ Tuning\",\"Schema.org JSON-LD Structured Data Implementation\",\"Local Google Business Optimization (Faridabad & NCR)\",\"Automated XML Sitemaps & Search Console Configuration\",\"Keyword Strategy & Content Architecture\"]','Google Search Console, Lighthouse, Schema.org, SEMrush','Starting from ₹10,000 / month','https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',5,1,1,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(6,'Cloud DevOps, Containerization & Maintenance','cloud-devops-infrastructure','bi-cloud-check','Automated CI/CD pipelines, Docker containerization, VPS & Dedicated server configuration, SSL, zero-downtime deployments.','<p>Eliminate downtime, automate deployment workflows, and ensure your infrastructure scales effortlessly under traffic surges. Shrawan Effects provides complete cloud system administration and DevOps management.</p><h4>Cloud Services</h4><ul><li>VPS and Dedicated Server provisioning (Linux / Ubuntu, Nginx, PHP-FPM, MySQL).</li><li>Docker containerization for reproducible development and production environments.</li><li>Automated CI/CD deployment pipelines using GitHub Actions.</li><li>Automated daily off-site encrypted database backups.</li><li>Security hardening: UFW firewall, fail2ban, SSL certificates, brute-force shield.</li></ul>','[\"Nginx, Apache & PHP-FPM Performance Hardening\",\"Docker Containerization & Multi-Environment Setup\",\"CI\\/CD Automated Deployments via GitHub Actions\",\"Automated Daily Off-Site Encrypted Backups\",\"24\\/7 Server Telemetry Monitoring & Uptime Protection\"]','Docker, Nginx, Ubuntu Linux, GitHub Actions, AWS, DigitalOcean','Starting from ₹15,000 / setup','https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&w=1200&q=80',6,1,0,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(7,'Mobile Application Development (Android & iOS)','mobile-app-development','bi-phone','Cross-platform iOS and Android mobile apps with slick UI, offline caching, push notifications, and seamless cloud syncing.','<p>Deliver a world-class mobile app to your users. We engineer high-performance mobile applications that run smoothly on both Android and iOS devices from a unified codebase, saving development costs and time-to-market.</p><h4>Mobile App Deliverables</h4><ul><li>Cross-platform Flutter / React Native architecture.</li><li>Biometric authentication (Fingerprint, Face ID).</li><li>Push notification integration via Firebase Cloud Messaging (FCM).</li><li>Offline-first data caching with local SQLite database synchronization.</li><li>App Store and Google Play Store submission and release management.</li></ul>','[\"Cross-Platform iOS & Android Native Performance\",\"Firebase Push Notifications & Real-Time Sync\",\"Biometric Security & Social Sign-In Support\",\"Offline-First Data Storage & Sync Engine\",\"App Store & Play Store Publishing Management\"]','Flutter, Dart, React Native, Firebase, REST APIs, SQLite','Starting from ₹45,000 / project','https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1200&q=80',7,1,0,'2026-10-05 01:38:24','2026-10-05 01:38:24'),(8,'Online Promotion & Digital Visibility','online-promotions-digital-marketing','bi-megaphone','Targeted business promotion, lead generation funnels, conversion rate optimization, and brand authority building.','<p>Drive qualified traffic, acquire high-value leads, and amplify your digital brand presence with targeted promotion campaigns and conversion-focused marketing funnels.</p><h4>Promotional Strategies</h4><ul><li>Targeted lead generation funnels with dedicated landing pages.</li><li>Meta (Facebook/Instagram) and Google Ads campaign architecture.</li><li>Conversion Rate Optimization (CRO) and user journey audits.</li><li>Email marketing automation and customer re-engagement sequences.</li></ul>','[\"High-Converting Landing Page Design & Testing\",\"Targeted Lead Generation & Ad Campaign Setup\",\"Conversion Rate Optimization (CRO)\",\"Automated Email Marketing & Lead Nurturing\",\"Comprehensive Traffic & ROI Analytics\"]','Google Ads, Meta Business Suite, Google Analytics 4, Automation','Starting from ₹5,000 / month','https://images.unsplash.com/photo-1533750349088-cd871a92f312?auto=format&fit=crop&w=1200&q=80',8,1,0,'2026-10-05 01:38:24','2026-10-05 01:38:24');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('3yf6fzU4myo5j0YPwG0IeUdmrqcwCvCRKYJhlMJx',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY2ozRmI3Nm1JMmJPRW9EenBGb1NrOThMRmRFbnZ2eDZaVTRXdEtiMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdXRoL2dvb2dsZSI7czo1OiJyb3V0ZSI7czoxNToic29jaWFsLnJlZGlyZWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1OiJzdGF0ZSI7czo0MDoiRkxTSWNmdUQ1NURvdnlBM3R4cnZobGppeEJsbTRha0M1QUdmUEZKVSI7fQ==',1788111840),('hz2xbB8nPdTQJ9fMdtlJlgOKzJAPwASj3W83h3sT',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWFZdDJHZzRTTHh2NGZvV2dIcjZnOVBtcmN1UnFoRWdRTlpIc05VNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1788505339),('XPLDES1STSjVPb7VFeamE0OsmFfoQQts8TsZKXEV',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWWtaQ2NsWW1nNllYMkg1dVpYT2F3ZWVOOHphTVN3clF5bjNuM0l4eCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9zZXR0aW5ncyI7czo1OiJyb3V0ZSI7czoyMDoiYWRtaW4uc2V0dGluZ3MuaW5kZXgiO31zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1788111814);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Shrawan Effects','2026-08-27 15:55:53','2026-08-28 02:12:47'),(2,'site_tagline','Fullstack Website & Software Development Service Provider in Faridabad','2026-08-27 15:55:53','2026-10-05 01:35:31'),(3,'timezone','Asia/Kolkata','2026-08-27 15:55:53','2026-08-27 15:55:53'),(4,'homepage_type','default','2026-08-27 15:55:54','2026-08-27 15:55:54'),(5,'homepage_category_id',NULL,'2026-08-27 15:55:54','2026-08-27 15:55:54'),(6,'homepage_post_id',NULL,'2026-08-27 15:55:54','2026-08-27 15:55:54'),(7,'homepage_page_id',NULL,'2026-08-27 15:55:54','2026-08-27 15:55:54'),(8,'contact_email','shrawaneffects@gmail.com','2026-08-27 15:55:54','2026-08-28 02:12:47'),(9,'footer_text','Shrawan Effects - Fullstack Website & Software Development Service Provider in Faridabad. Building scalable, high-performance web applications, enterprise software, and futuristic digital experiences.','2026-08-27 15:55:54','2026-10-05 01:35:31'),(10,'twitter_url','https://twitter.com','2026-08-27 15:55:54','2026-08-27 15:55:54'),(11,'github_url','https://github.com','2026-08-27 15:55:54','2026-08-27 15:55:54'),(12,'linkedin_url','https://linkedin.com','2026-08-27 15:55:54','2026-08-27 15:55:54'),(13,'disable_right_click','1','2026-08-27 15:55:54','2026-08-27 15:55:54'),(14,'disable_inspect','1','2026-08-27 15:55:54','2026-08-27 15:55:54'),(15,'disable_screenshot','1','2026-08-27 15:55:54','2026-08-27 15:55:54'),(16,'ads_enabled','0','2026-08-27 15:55:54','2026-08-28 02:24:10'),(17,'ads_placeholders','0','2026-08-27 15:55:54','2026-08-28 02:24:10'),(18,'adsense_publisher_id','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(19,'ad_slot_header','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(20,'ad_slot_home_middle','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(21,'ad_slot_post_top','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(22,'ad_slot_post_middle','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(23,'ad_slot_post_bottom','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(24,'ad_slot_sidebar','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(25,'ad_slot_feed','','2026-08-27 15:55:54','2026-08-27 15:55:54'),(26,'site_logo','settings/AzrK4SnV57fhqmoycGFSYCcbbuLQqdqgNGcXls7c.png','2026-08-27 15:55:54','2026-08-27 15:55:54'),(27,'site_favicon','settings/MiTaq6S6lOl9JeZTYEea8PdD1VBKyzIEZ1sN8zLP.png','2026-08-27 15:55:54','2026-08-27 15:55:54'),(34,'homepage_meta_title','Shrawan Effects - Tech update available here','2026-08-30 16:55:54','2026-08-30 16:55:54'),(35,'homepage_meta_description','Shrawan Effects is a website where you can always find tech-relevant content. Fresh News and updates from tech companies and techies.','2026-08-30 16:55:54','2026-08-30 16:55:54'),(36,'homepage_meta_keywords','','2026-08-30 16:55:54','2026-08-30 16:55:54'),(37,'contact_address','House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004','2026-10-05 01:35:31','2026-10-05 01:35:31');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'Laravel','laravel','2026-08-22 04:19:09','2026-08-22 04:19:09'),(2,'PHP','php','2026-08-22 04:19:09','2026-08-22 04:19:09'),(3,'Bootstrap 5','bootstrap-5','2026-08-22 04:19:09','2026-08-22 04:19:09'),(4,'JavaScript','javascript','2026-08-22 04:19:09','2026-08-22 04:19:09'),(5,'Architecture','architecture','2026-08-22 04:19:09','2026-08-22 04:19:09'),(6,'AI Agents','ai-agents','2026-08-22 04:19:09','2026-08-22 04:19:09'),(7,'DevOps','devops','2026-08-22 04:19:09','2026-08-22 04:19:09'),(8,'Docker','docker','2026-08-22 04:19:09','2026-08-22 04:19:09'),(9,'Database','database','2026-08-22 04:19:09','2026-08-22 04:19:09'),(10,'Security','security','2026-08-22 04:19:09','2026-08-22 04:19:09'),(11,'Productivity','productivity','2026-08-22 04:19:09','2026-08-22 04:19:09'),(12,'CSS','css','2026-08-22 04:19:09','2026-08-22 04:19:09'),(13,'Clean Code','clean-code','2026-08-22 04:19:09','2026-08-22 04:19:09');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `provider_name` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `provider_token` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_provider_id_index` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Alex Morgan','admin@blog.com',NULL,'$2y$12$Pc0Dlcquejk0iloTRmxnVujGRcyCZdGqKTq2t5ZmrpH5CTdFBnv7y','admin',NULL,NULL,NULL,NULL,'Senior Full-Stack Architect & Tech Evangelist with over 10 years of experience building high-scale web platforms.',1,'8RcJnYTideqCJFv2ZheQdQjxBC3gAgKV9u2VxlCJW6rZGDnCD8vkZ6UB9iKF','2026-08-22 04:19:08','2026-08-22 04:19:08'),(2,'Sarah Jenkins','author@blog.com',NULL,'$2y$12$9N79brZM0GRSh3ekXW71sO9jV4bc43RPXhdOOVyfRfzk3I9xsDb1y','author',NULL,NULL,NULL,'avatars/9IjGtUVJTCT2RXG7eEDVofda2Eg4BkFVMNC9vt2G.png','UI/UX specialist, design systems advocate, and frontend developer passionate about clean interfaces and web accessibility.',1,NULL,'2026-08-22 04:19:08','2026-09-04 14:45:05'),(3,'David Chen','user@blog.com',NULL,'$2y$12$kuZkn3ZPlXH5L4brXkOyL.o2ekyeZX9Mpeb0jN01dPdkKgYDGIixm','user',NULL,NULL,NULL,NULL,'Curious software engineer and tech reader exploring new web ecosystems.',1,NULL,'2026-08-22 04:19:08','2026-08-22 04:19:08'),(89,'Shrawan Test User','newuser_KwtSa@shrawaneffects.com',NULL,'$2y$12$a15b9S5g8RdsCNh7A3H2auNdnQj4hfncQBCTy/x5BWi3ZAu4d9Tiy','user',NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-09-04 06:52:16','2026-09-04 06:52:16'),(90,'Shrawan Test User','newuser_HEu4e@shrawaneffects.com','2026-09-04 06:52:33','$2y$12$YBWyuqgXDizcXRu2kyU8F.tBA9CHl3ZbIhMH/aWNzDsH3YsuFFuZS','user',NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-09-04 06:52:33','2026-09-04 06:52:33'),(111,'Shrawan Choudhary','shrawaneffects@gmail.com',NULL,'$2y$04$G1Xe9YzSlDReEHsJYokpsOBZv6JHEJXx1m.EM6CPkbA3pvZHuXBma','admin',NULL,NULL,NULL,'avatars/cGeDTMATsdEeBIm1r8lD7PoWMDYcNdDA6jc9ceAU.png','Senior Full-Stack Architect & Founder at Shrawan Effects with extensive experience building modern web platforms.',1,'J4B3Ba4PFDrwgSVh1tMx8uTXiXPZEsyiVcOQMTlQzHoLGboymJwWduPXD101','2026-09-04 09:33:53','2026-09-04 14:42:53');
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

-- Dump completed on 2026-11-16 17:53:33
