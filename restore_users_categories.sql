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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-16 17:53:02
