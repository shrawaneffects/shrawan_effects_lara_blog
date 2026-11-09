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
