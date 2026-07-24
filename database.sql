-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: dan_creatives_db
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
-- Current Database: `dan_creatives_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dan_creatives_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `dan_creatives_db`;

--
-- Table structure for table `about_content`
--

DROP TABLE IF EXISTS `about_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `about_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `youtube_channel_url` varchar(500) DEFAULT NULL,
  `youtube_thumbnail` varchar(500) DEFAULT NULL,
  `youtube_badge_text` varchar(100) DEFAULT NULL,
  `channel_description` text DEFAULT NULL,
  `instructor_name` varchar(100) DEFAULT NULL,
  `instructor_title` varchar(200) DEFAULT NULL,
  `instructor_bio` text DEFAULT NULL,
  `instructor_image` varchar(500) DEFAULT NULL,
  `youtube_video_1_url` varchar(500) DEFAULT NULL,
  `youtube_video_1_thumbnail` varchar(500) DEFAULT NULL,
  `youtube_video_1_title` varchar(200) DEFAULT NULL,
  `youtube_video_1_views` varchar(50) DEFAULT NULL,
  `youtube_video_2_url` varchar(500) DEFAULT NULL,
  `youtube_video_2_thumbnail` varchar(500) DEFAULT NULL,
  `youtube_video_2_title` varchar(200) DEFAULT NULL,
  `youtube_video_2_views` varchar(50) DEFAULT NULL,
  `youtube_video_3_url` varchar(500) DEFAULT NULL,
  `youtube_video_3_thumbnail` varchar(500) DEFAULT NULL,
  `youtube_video_3_title` varchar(200) DEFAULT NULL,
  `youtube_video_3_views` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_content`
--

LOCK TABLES `about_content` WRITE;
/*!40000 ALTER TABLE `about_content` DISABLE KEYS */;
INSERT INTO `about_content` VALUES (1,'https://www.youtube.com/@DanGraphics1','images/logo.jpg','YouTube Channel','Learn graphics design through engaging video tutorials','Dan','Founder & Lead Instructor','Passionate graphics designer and educator with years of industry experience. Dedicated to helping aspiring creators master design through practical, easy-to-follow lessons.','images/dani.jpg','https://youtu.be/3L3xbppfDns','https://img.youtube.com/vi/3L3xbppfDns/hqdefault.jpg','ßìûßê╡ßë░ßê¡ ßï▓ßï¢ßï¡ßèò ßèáßê░ßê½ßê¡ | Melat Perfume Poster Design Tutorial','19K+ views','https://youtu.be/nM-QB5y4zrw','https://img.youtube.com/vi/nM-QB5y4zrw/hqdefault.jpg','YouTube Thumbnail Design | Click-Worthy Thumbnail Tutorial','8.5K+ views','https://youtu.be/P4M1bzeE_cI','https://img.youtube.com/vi/P4M1bzeE_cI/hqdefault.jpg','Photo Editing in Amharic | Photoshop Tutorial for Beginners','19K+ views','2026-03-27 16:17:56');
/*!40000 ALTER TABLE `about_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$y6KguGwmqLLx2fyVTfJws.sfOdXRfzyOZWxWBtIMx4Hg11IhlB632','2026-03-27 10:52:17');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_conversations`
--

DROP TABLE IF EXISTS `ai_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) NOT NULL,
  `visitor_name` varchar(100) DEFAULT NULL,
  `visitor_phone` varchar(50) DEFAULT NULL,
  `visitor_telegram` varchar(100) DEFAULT NULL,
  `source_page` varchar(100) DEFAULT NULL,
  `status` enum('active','escalated','resolved') DEFAULT 'active',
  `escalation_reason` varchar(255) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_message_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_conversations`
--

LOCK TABLES `ai_conversations` WRITE;
/*!40000 ALTER TABLE `ai_conversations` DISABLE KEYS */;
INSERT INTO `ai_conversations` VALUES (1,'dai-1784493782875-ttws45wl',NULL,NULL,NULL,'index.php','escalated','AI assistant is not configured yet','2026-07-20 13:55:46','2026-07-20 14:40:28');
/*!40000 ALTER TABLE `ai_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_messages`
--

DROP TABLE IF EXISTS `ai_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `role` enum('user','assistant','system') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  CONSTRAINT `ai_messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_messages`
--

LOCK TABLES `ai_messages` WRITE;
/*!40000 ALTER TABLE `ai_messages` DISABLE KEYS */;
INSERT INTO `ai_messages` VALUES (1,1,'user','hi','2026-07-20 13:55:46'),(2,1,'assistant','Thanks for reaching out! Our assistant is still warming up ΓÇö I\'ve notified our team and they\'ll reply to you personally very soon ≡ƒÖî','2026-07-20 13:55:46'),(3,1,'user','i want yo ask one question','2026-07-20 13:56:14'),(4,1,'assistant','Thanks for reaching out! Our assistant is still warming up ΓÇö I\'ve notified our team and they\'ll reply to you personally very soon ≡ƒÖî','2026-07-20 13:56:14'),(5,1,'user','i want to ask one queston','2026-07-20 13:56:27'),(6,1,'assistant','Thanks for reaching out! Our assistant is still warming up ΓÇö I\'ve notified our team and they\'ll reply to you personally very soon ≡ƒÖî','2026-07-20 13:56:27'),(7,1,'user','hi','2026-07-20 13:59:06'),(8,1,'assistant','Thanks for reaching out! Our assistant is still warming up ΓÇö I\'ve notified our team and they\'ll reply to you personally very soon ≡ƒÖî','2026-07-20 13:59:06'),(9,1,'user','human','2026-07-20 14:40:28'),(10,1,'assistant','Thanks for reaching out! Our assistant is still warming up ΓÇö I\'ve notified our team and they\'ll reply to you personally very soon ≡ƒÖî','2026-07-20 14:40:28');
/*!40000 ALTER TABLE `ai_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_settings`
--

DROP TABLE IF EXISTS `ai_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `provider` varchar(20) DEFAULT 'gemini',
  `api_key` varchar(255) DEFAULT NULL,
  `model` varchar(100) DEFAULT 'gemini-2.5-flash-lite',
  `system_prompt` text DEFAULT NULL,
  `welcome_message` varchar(500) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'not_configured',
  `last_error` text DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_settings`
--

LOCK TABLES `ai_settings` WRITE;
/*!40000 ALTER TABLE `ai_settings` DISABLE KEYS */;
INSERT INTO `ai_settings` VALUES (1,'gemini',NULL,'gemini-2.5-flash-lite',NULL,NULL,1,'not_configured',NULL,NULL,'2026-07-20 09:43:13');
/*!40000 ALTER TABLE `ai_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `start_date` varchar(100) DEFAULT NULL,
  `icon_class` varchar(50) DEFAULT 'fas fa-palette',
  `status` enum('active','coming_soon') DEFAULT 'active',
  `badge_text` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,'Full Graphics Design','Complete graphics design course covering Photoshop, Illustrator, typography, logo design, branding, and more. Perfect for beginners wanting to become professional designers.','2,990 Birr','8 Weeks','June 12, 2025','fas fa-palette','active','Most Popular','2026-03-27 11:01:12','2026-03-27 11:24:12'),(2,'YouTube Thumbnail Mastery','Learn to create click-worthy thumbnails that get millions of views. Master Photoshop, color grading, face expressions, and CTR psychology.','1,500 Birr','2 Weeks','coming soon','fas fa-video','active','best of the best','2026-03-27 11:01:12','2026-04-02 10:05:25');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_items`
--

DROP TABLE IF EXISTS `portfolio_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(200) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `media_url` varchar(500) NOT NULL,
  `media_type` enum('image','video') DEFAULT 'image',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `portfolio_items_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_items`
--

LOCK TABLES `portfolio_items` WRITE;
/*!40000 ALTER TABLE `portfolio_items` DISABLE KEYS */;
INSERT INTO `portfolio_items` VALUES (9,13,'Brand Design','Ethiopian Coffee Brand Identity','Complete brand identity package including logo, packaging, and marketing materials for a premium Ethiopian coffee brand.','https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=600','image',1,'active','2026-04-01 08:42:44'),(10,13,'Brand Design','Modern Tech Startup Brand','Minimalist branding with bold colors for a tech startup. Includes logo, business cards, and social media assets.','https://images.unsplash.com/photo-1541701494587-cb58502866ab?w=600','image',2,'active','2026-04-01 08:42:44'),(11,13,'Brand Design','Luxury Fashion Brand','Elegant and sophisticated brand identity for a high-end fashion label. Gold accents and premium typography.','https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600','image',3,'active','2026-04-01 08:42:44'),(12,13,'Brand Design','Organic Food Brand','Natural and earthy brand design for an organic food company. Warm colors and hand-drawn elements.','https://images.unsplash.com/photo-1542838132-92c53300491e?w=600','image',4,'active','2026-04-01 08:42:44'),(13,13,'Brand Design','Creative Agency Branding','Dynamic and creative brand identity for a digital agency. Vibrant colors and modern design elements.','https://images.unsplash.com/photo-1557804506-669a67965ba0?w=600','image',5,'active','2026-04-01 08:42:44'),(14,13,'Brand Design','Fitness Brand Identity','Bold and energetic branding for a fitness center. Strong typography and dynamic visual elements.','https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600','image',6,'active','2026-04-01 08:42:44'),(15,13,'Brand Design','Beauty & Cosmetics Brand','Soft and feminine brand design for a beauty product line. Pastel colors and elegant typography.','https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600','image',7,'active','2026-04-01 08:42:44'),(16,14,'Logo Design','Minimalist Geometric Logo','Clean geometric logo design for a modern architecture firm. Simple yet powerful.','https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=600','image',1,'active','2026-04-01 08:42:44'),(17,14,'Logo Design','Abstract Creative Logo','Unique abstract logo with vibrant colors for a creative studio.','https://images.unsplash.com/photo-1626785774573-4b799315345d?w=600','image',2,'active','2026-04-01 08:42:44'),(18,14,'Logo Design','Vintage & Retro Logo','Classic vintage style logo with distressed textures for a coffee shop.','https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=600','image',3,'active','2026-04-01 08:42:44'),(19,14,'Logo Design','Elegant Script Logo','Beautiful script logo for a wedding planning business. Elegant and feminine.','https://images.unsplash.com/photo-1579783901586-d88db74b4fe4?w=600','image',4,'active','2026-04-01 08:42:44'),(20,14,'Logo Design','Corporate Logo','Professional and trustworthy logo for a financial services company.','https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?w=600','image',5,'active','2026-04-01 08:42:44'),(21,14,'Logo Design','Mascot Logo','Fun and engaging mascot logo for a sports team or brand.','https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600','image',6,'active','2026-04-01 08:42:44'),(22,14,'Logo Design','3D Modern Logo','Dynamic 3D logo with depth and dimension for a tech company.','https://images.unsplash.com/photo-1604963232800-8a6a8a1eef8b?w=600','image',7,'active','2026-04-01 08:42:44'),(23,14,'Logo Design','Handmade Logo','Hand-drawn organic logo for a craft beer brand.','https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=600','image',8,'active','2026-04-01 08:42:44'),(24,14,'Logo Design','Luxury Logo Design','Premium logo with metallic effects for a luxury brand.','https://images.unsplash.com/photo-1548001927-15bff4662a6d?w=600','image',9,'active','2026-04-01 08:42:44'),(25,14,'Logo Design','Letter Mark Logo','Simple and memorable letter-based logo for a tech startup.','https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600','image',10,'active','2026-04-01 08:42:44'),(26,15,'Social Media Posters','Instagram Story Templates','Engaging Instagram story templates for daily posts and promotions.','https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600','image',1,'active','2026-04-01 08:42:44'),(27,15,'Social Media Posters','Facebook Post Designs','Professional Facebook post layouts for brand awareness.','https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=600','image',2,'active','2026-04-01 08:42:44'),(28,15,'Social Media Posters','LinkedIn Banner Design','Corporate LinkedIn banners for professional profiles and company pages.','https://images.unsplash.com/photo-1614027164844-1e2f2e5a3e6d?w=600','image',3,'active','2026-04-01 08:42:44'),(29,15,'Social Media Posters','Twitter Header Design','Custom Twitter headers that make your profile stand out.','https://images.unsplash.com/photo-1611605698335-8b1569810432?w=600','image',4,'active','2026-04-01 08:42:44'),(30,15,'Social Media Posters','Pinterest Pin Templates','Eye-catching Pinterest pins that drive traffic and engagement.','https://images.unsplash.com/photo-1611162618071-b39a2ec055fb?w=600','image',5,'active','2026-04-01 08:42:44'),(31,15,'Social Media Posters','Instagram Feed Post','Beautiful Instagram feed posts with modern design trends.','https://images.unsplash.com/photo-1611162616475-46b635cb6868?w=600','image',6,'active','2026-04-01 08:42:44'),(32,15,'Social Media Posters','YouTube Channel Art','Professional YouTube banner and channel art designs.','https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600','image',7,'active','2026-04-01 08:42:44'),(33,15,'Social Media Posters','Promotional Flyers','Digital flyers for events, sales, and promotions.','https://images.unsplash.com/photo-1574717024653-61fd2cf4d7d5?w=600','image',8,'active','2026-04-01 08:42:44'),(34,15,'Social Media Posters','Quote Graphics','Inspirational quote graphics for social media engagement.','https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600','image',9,'active','2026-04-01 08:42:44'),(35,15,'Social Media Posters','Product Showcase','Creative product showcase designs for e-commerce.','https://images.unsplash.com/photo-1601924582970-9238bcb495d9?w=600','image',10,'active','2026-04-01 08:42:44'),(36,16,'Banner Design','Web Banner Ads','Responsive web banner designs for Google Ads and social media.','https://images.unsplash.com/photo-1545235617-9465d2a55698?w=600','image',1,'active','2026-04-01 08:42:44'),(37,16,'Banner Design','Event Banner','Large format banners for conferences, trade shows, and events.','https://images.unsplash.com/photo-1531058020387-3be344556be6?w=600','image',2,'active','2026-04-01 08:42:44'),(38,16,'Banner Design','Roll-up Banner','Professional roll-up banners for exhibitions and events.','https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600','image',3,'active','2026-04-01 08:42:44'),(39,16,'Banner Design','Billboard Design','High-impact billboard designs for outdoor advertising.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',4,'active','2026-04-01 08:42:44'),(40,16,'Banner Design','Shop Banner','Custom banners for storefronts and retail displays.','https://images.unsplash.com/photo-1600881333168-2ef49b341f30?w=600','image',5,'active','2026-04-01 08:42:44'),(41,16,'Banner Design','Email Header Banner','Professional email header designs for newsletters.','https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600','image',6,'active','2026-04-01 08:42:44'),(42,16,'Banner Design','Website Hero Banner','Modern hero section banners for websites.','https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600','image',7,'active','2026-04-01 08:42:44'),(43,16,'Banner Design','Promotional Banner','Seasonal and promotional banner designs.','https://images.unsplash.com/photo-1545235617-9465d2a55698?w=600','image',8,'active','2026-04-01 08:42:44'),(44,16,'Banner Design','Social Media Cover','Cover photos for Facebook, LinkedIn, and YouTube.','https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600','image',9,'active','2026-04-01 08:42:44'),(45,17,'Certificate Design','Course Certificate','Professional course completion certificates with elegant design.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',1,'active','2026-04-01 08:42:44'),(46,17,'Certificate Design','Award Certificate','Recognition awards and achievement certificates.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',2,'active','2026-04-01 08:42:44'),(47,17,'Certificate Design','Training Certificate','Corporate training program certificates.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',3,'active','2026-04-01 08:42:44'),(48,17,'Certificate Design','Academic Diploma','Formal academic diploma designs for institutions.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',4,'active','2026-04-01 08:42:44'),(49,17,'Certificate Design','Event Certificate','Special event participation certificates.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',5,'active','2026-04-01 08:42:44'),(50,17,'Certificate Design','Membership Certificate','Professional membership and association certificates.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',6,'active','2026-04-01 08:42:44'),(51,17,'Certificate Design','Custom Certificate','Fully customized certificate designs for any purpose.','https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=600','image',7,'active','2026-04-01 08:42:44'),(52,18,'Thumbnail Design','Gaming Thumbnail','Click-worthy gaming thumbnails with bold text and effects.','https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600','image',1,'active','2026-04-01 08:42:44'),(53,18,'Thumbnail Design','Vlog Thumbnail','Professional vlog thumbnail designs with clean layout.','https://i.ibb.co/sJtyRdRT/1.jpg','image',2,'active','2026-04-01 08:42:44'),(54,18,'Thumbnail Design','Tech Review Thumbnail','Modern tech review thumbnails with product highlights.','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=600','image',3,'active','2026-04-01 08:42:44'),(55,18,'Thumbnail Design','Educational Thumbnail','Clean and professional educational content thumbnails.','https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600','image',4,'active','2026-04-01 08:42:44'),(56,18,'Thumbnail Design','Comedy Thumbnail','Fun and engaging thumbnails for comedy content.','https://images.unsplash.com/photo-1516245834210-c4c142785335?w=600','image',5,'active','2026-04-01 08:42:44'),(57,18,'Thumbnail Design','Music Thumbnail','Creative thumbnails for music videos and podcasts.','https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=600','image',6,'active','2026-04-01 08:42:44'),(58,18,'Thumbnail Design','Fitness Thumbnail','Energetic thumbnails for fitness and workout videos.','https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600','image',7,'active','2026-04-01 08:42:44'),(59,18,'Thumbnail Design','Travel Vlog Thumbnail','Beautiful travel thumbnails with stunning locations.','https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600','image',8,'active','2026-04-01 08:42:44'),(60,18,'Thumbnail Design','Food Thumbnail','Delicious food thumbnails for cooking channels.','https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600','image',9,'active','2026-04-01 08:42:44'),(61,18,'Thumbnail Design','Reaction Thumbnail','High-conversion reaction video thumbnails.','https://images.unsplash.com/photo-1518674660708-0e2c0473e68e?w=600','image',10,'active','2026-04-01 08:42:44');
/*!40000 ALTER TABLE `portfolio_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_orders`
--

DROP TABLE IF EXISTS `product_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_telegram` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `message` text DEFAULT NULL,
  `status` enum('pending','processing','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_orders`
--

LOCK TABLES `product_orders` WRITE;
/*!40000 ALTER TABLE `product_orders` DISABLE KEYS */;
INSERT INTO `product_orders` VALUES (12,NULL,'Custom Neon Light','tesfahun','tesfahunbaye6@gmail.com','0909090909','shetesfa',3,'','completed','2026-03-27 14:42:37'),(14,NULL,'Custom Printed Hoodie','tesfahunbayihks','tesfahunbayin@gmil.com','0912121212','shetesfa',4,'test','completed','2026-03-27 14:49:24'),(15,NULL,'Custom Water Bottle','tefdf','reter@tesfs.com','0912121212','shetesfa',4,'','completed','2026-03-27 15:04:28');
/*!40000 ALTER TABLE `product_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_template_orders`
--

DROP TABLE IF EXISTS `product_template_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_template_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) DEFAULT NULL,
  `template_id` int(11) NOT NULL,
  `template_name` varchar(200) NOT NULL,
  `template_image` varchar(500) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_telegram` varchar(100) DEFAULT NULL,
  `quantity` varchar(50) DEFAULT '1',
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `status` enum('pending','processing','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `product_template_orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_template_orders_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `product_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_template_orders`
--

LOCK TABLES `product_template_orders` WRITE;
/*!40000 ALTER TABLE `product_template_orders` DISABLE KEYS */;
INSERT INTO `product_template_orders` VALUES (1,1,'Premium Custom T-Shirt',1,'Minimalist Logo Design','https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400','jljfsl','wefaef@twe.com','0909090909','sgdsdf','4','L','white','sDFSDGFSDGG','completed','2026-03-30 13:55:23'),(2,5,'Custom Gift Box',22,'Pattern Wrap','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400','dhfsdfgdfg','sswdgdf@wrest.com','5688687567567','sdffsdf','1','M','color','asdasfsfdzsfd','pending','2026-03-30 14:42:44'),(57,2,'Custom Printed Hoodie',8,'Full Front with Hood','https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=400','wefesdcz','esfdcd@wef.com','0909090909','asfsdgff','10','M','black','weoifdsncx wpo','pending','2026-03-30 15:11:25'),(58,6,'Custom Neon Light',27,'Shape Neon Sign','https://images.unsplash.com/photo-1563089145-599997674d42?w=400','4wetrfsfs','ewfsd@srf.com','0912233445','sffsdfs','10','XL','white','wefojdslkcm.wei0pojsd;klm','pending','2026-03-30 15:12:33'),(69,5,'Custom Gift Box',22,'Pattern Wrap','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400','ridfykc','tshgx@yjh.com','0909090990','sdvxds','4','XXL','black','wesdzxcsd','pending','2026-03-30 15:53:25');
/*!40000 ALTER TABLE `product_template_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_templates`
--

DROP TABLE IF EXISTS `product_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_templates_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_templates`
--

LOCK TABLES `product_templates` WRITE;
/*!40000 ALTER TABLE `product_templates` DISABLE KEYS */;
INSERT INTO `product_templates` VALUES (1,1,'Minimalist Logo Design','Clean and simple logo placement on chest. Perfect for corporate and professional look.','https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400',1,'active','2026-03-30 13:40:03'),(2,1,'Full Front Print','Large artwork covering the entire front. Great for artistic and creative designs.','https://images.unsplash.com/photo-1503342394128-c104d54dba01?w=400',2,'active','2026-03-30 13:40:03'),(3,1,'Back Print Design','Large design on the back with small logo on front pocket area.','https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=400',3,'active','2026-03-30 13:40:03'),(4,1,'Sleeve Design','Unique design printed on sleeves. Modern and stylish look.','https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=400',4,'active','2026-03-30 13:40:03'),(5,1,'All-Over Print','Full coverage design from collar to hem. Maximum visual impact.','https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=400',5,'active','2026-03-30 13:40:03'),(6,2,'Front Pocket Design','Design placed on front pocket area. Casual and comfortable look.','https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400',1,'active','2026-03-30 13:40:03'),(7,2,'Hood Print','Large design on the hood area. Perfect for streetwear style.','https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400',2,'active','2026-03-30 13:40:03'),(8,2,'Full Front with Hood','Design covering entire front including hood area.','https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=400',3,'active','2026-03-30 13:40:03'),(9,2,'Minimalist Back','Simple design on back with small logo on front.','https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400',4,'active','2026-03-30 13:40:03'),(10,2,'Zipper Design','Design integrated with zipper area. Sleek and modern.','https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400',5,'active','2026-03-30 13:40:03'),(11,3,'Full Wrap Design','Design wraps completely around the mug. 360-degree visibility.','https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400',1,'active','2026-03-30 13:40:03'),(12,3,'Front Center Design','Design centered on one side. Classic and simple look.','https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400',2,'active','2026-03-30 13:40:03'),(13,3,'Two-Sided Design','Different designs on opposite sides of the mug.','https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400',3,'active','2026-03-30 13:40:03'),(14,3,'Handle Design','Design integrated with handle area. Unique placement.','https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400',4,'active','2026-03-30 13:40:03'),(15,3,'Bottom Design','Design visible when mug is empty or upside down.','https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400',5,'active','2026-03-30 13:40:03'),(16,4,'Full Wrap Label','Design wraps completely around the bottle.','https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400',1,'active','2026-03-30 13:40:03'),(17,4,'Front Logo','Simple logo placement on front of bottle.','https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400',2,'active','2026-03-30 13:40:03'),(18,4,'Cap Design','Design printed on bottle cap. Unique touch.','https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400',3,'active','2026-03-30 13:40:03'),(19,4,'Vertical Strip','Vertical design running down the bottle length.','https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400',4,'active','2026-03-30 13:40:03'),(20,4,'Gradient Effect','Color gradient design from top to bottom.','https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400',5,'active','2026-03-30 13:40:03'),(21,5,'Elegant Ribbon Design','Classic gift box with ribbon and bow design.','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400',1,'active','2026-03-30 13:40:03'),(22,5,'Pattern Wrap','Repeating pattern covering entire box.','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400',2,'active','2026-03-30 13:40:03'),(23,5,'Custom Text','Personalized message or name on box.','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400',3,'active','2026-03-30 13:40:03'),(24,5,'Photo Box','Custom photo printed on gift box.','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400',4,'active','2026-03-30 13:40:03'),(25,5,'Minimalist Solid','Clean solid color with subtle logo.','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400',5,'active','2026-03-30 13:40:03'),(26,6,'Text Neon Sign','Custom text in neon tube style. Perfect for business names.','https://images.unsplash.com/photo-1563089145-599997674d42?w=400',1,'active','2026-03-30 13:40:03'),(27,6,'Shape Neon Sign','Custom shape or icon in neon style.','https://images.unsplash.com/photo-1563089145-599997674d42?w=400',2,'active','2026-03-30 13:40:03'),(28,6,'Logo Neon Sign','Your logo recreated as neon sign.','https://images.unsplash.com/photo-1563089145-599997674d42?w=400',3,'active','2026-03-30 13:40:03'),(29,6,'Multi-Color Design','Multiple colors in one neon sign.','https://images.unsplash.com/photo-1563089145-599997674d42?w=400',4,'active','2026-03-30 13:40:03'),(30,6,'Abstract Neon','Abstract art piece in neon style.','https://images.unsplash.com/photo-1563089145-599997674d42?w=400',5,'active','2026-03-30 13:40:03'),(36,8,'Single Panel','Single canvas with your design or photo.','https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400',1,'active','2026-03-30 13:40:03'),(37,8,'Triptych (3 Panels)','Three-panel split design for modern look.','https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400',2,'active','2026-03-30 13:40:03'),(38,8,'Black & White','Classic black and white photo on canvas.','https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400',3,'active','2026-03-30 13:40:03'),(39,8,'Abstract Art','Modern abstract art on stretched canvas.','https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400',4,'active','2026-03-30 13:40:03'),(40,8,'Panoramic','Wide panoramic format for landscapes.','https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400',5,'active','2026-03-30 13:40:03'),(41,10,'Embroidered Logo','Classic embroidered logo on front.','https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400',1,'active','2026-03-30 13:40:03'),(42,10,'Side Logo','Logo embroidered on side of cap.','https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400',2,'active','2026-03-30 13:40:03'),(43,10,'Full Front Design','Large printed design on entire front panel.','https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400',3,'active','2026-03-30 13:40:03'),(44,10,'Mesh Back','Mesh back with embroidered front logo.','https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400',4,'active','2026-03-30 13:40:03'),(45,10,'Custom Patch','Removable patch that can be swapped.','https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400',5,'active','2026-03-30 13:40:03'),(56,13,'Leather Cover','Premium leather feel with embossed logo.','https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400',1,'active','2026-03-30 13:40:03'),(57,13,'Hard Cover Print','Full color print on hard cover.','https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400',2,'active','2026-03-30 13:40:03'),(58,13,'Personalized Name','Custom name engraved on cover.','https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400',3,'active','2026-03-30 13:40:03'),(59,13,'Spiral Bound','Spiral bound with custom cover design.','https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400',4,'active','2026-03-30 13:40:03'),(60,13,'Minimalist Design','Clean, simple design for professional use.','https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400',5,'active','2026-03-30 13:40:03'),(76,17,'Die-Cut Sticker','Custom shape cut around your design.','https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=400',1,'active','2026-03-30 13:40:03'),(77,17,'Vinyl Sticker','Durable vinyl for outdoor use.','https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=400',2,'active','2026-03-30 13:40:03'),(78,17,'Holographic Sticker','Shiny holographic effect stickers.','https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=400',3,'active','2026-03-30 13:40:03'),(79,17,'Transparent Sticker','Clear background stickers.','https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=400',4,'active','2026-03-30 13:40:03'),(80,17,'Circle Sticker','Classic circle shape stickers.','https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=400',5,'active','2026-03-30 13:40:03');
/*!40000 ALTER TABLE `product_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Premium Custom T-Shirt','High-quality 100% cotton t-shirt with your custom design. Perfect for events, teams, and personal branding. Available in all sizes and colors.','From 450 Birr','uploads/images/img_20260720_165956_8a440b5b.png','apparel','active',1,'2026-03-30 13:31:52'),(2,'Custom Printed Hoodie','Premium quality hoodie with your custom design. Stay warm and stylish with your brand. Made from soft, durable fabric.','From 850 Birr','https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500','apparel','active',2,'2026-03-30 13:31:52'),(3,'Custom Ceramic Mug','High-quality ceramic mug with your logo or design. Perfect for coffee lovers and great for corporate gifts. Dishwasher safe.','From 250 Birr','https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=500','accessories','active',3,'2026-03-30 13:31:52'),(4,'Custom Water Bottle','Stainless steel water bottle with your logo. Eco-friendly, keeps drinks cold for 24 hours, perfect for promotions and giveaways.','From 350 Birr','https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500','accessories','active',4,'2026-03-30 13:31:52'),(5,'Custom Gift Box','Beautiful gift box with personalized items. Perfect for special occasions, corporate gifts, and client appreciation.','From 650 Birr','https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=500','gifts','active',5,'2026-03-30 13:31:52'),(6,'Custom Neon Light','LED neon sign with your text or logo. Perfect for home decor, business signage, events, and special occasions.','From 1,200 Birr','https://images.unsplash.com/photo-1563089145-599997674d42?w=500','decor','active',6,'2026-03-30 13:31:52'),(8,'Custom Canvas Print','High-quality canvas print of your artwork or photo. Perfect for home decor, offices, and gifts. Available in various sizes.','From 500 Birr','https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=500','decor','active',8,'2026-03-30 13:31:52'),(9,'Custom Hoodie','Premium zip-up hoodie with custom embroidery. Perfect for team events, family gatherings, and personal style.','From 900 Birr','https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500','apparel','active',9,'2026-03-30 13:31:52'),(10,'Custom Cap','Adjustable baseball cap with embroidered logo. Great for outdoor events, teams, and promotional merchandise.','From 200 Birr','https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=500','apparel','active',10,'2026-03-30 13:31:52'),(13,'Custom Notebook','Premium hardcover notebook with your logo. Great for corporate gifts, events, and personal use.','From 180 Birr','https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=500','gifts','active',13,'2026-03-30 13:31:52'),(17,'Custom Stickers','High-quality vinyl stickers with your logo or design. Perfect for branding, packaging, and giveaways.','From 50 Birr','https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=500','gifts','active',17,'2026-03-30 13:31:52');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telegram` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `is_registered` tinyint(1) DEFAULT 0,
  `status` enum('pending','answered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `answered_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,'tesfa','tesfahunbaye6@gmail.com','@shetesfa','i want to check this system',0,'answered','2026-03-27 12:01:08','2026-03-27 12:01:59'),(3,'tesfa dgfs','tewte@grtr.com','0909090990','i want to check mine comment is registered or guest?',1,'pending','2026-03-27 12:08:58',NULL),(4,'ertyui','rtyu@fyy.com','qwerty','yuyftyy',0,'answered','2026-03-27 16:14:22','2026-04-02 10:36:00'),(6,'we4rtyu','qwret@ds.com','qwedsfg','q3wertyuio',0,'pending','2026-03-30 16:03:18',NULL);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `course` varchar(200) NOT NULL,
  `payment_receipt` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `registered_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
INSERT INTO `registrations` VALUES (2,'tesfa','dgfs','0909090990','tewte@grtr.com','Full Graphics Design',NULL,'pending','2026-03-27 13:03:32','2026-03-27 12:03:32'),(3,'agr','ertt','0986645342','tesf@tedt.com','YouTube Thumbnail Mastery',NULL,'pending','2026-03-27 16:06:50','2026-03-27 15:06:50'),(4,'4y55y','reye','09009090909','fgszdf@reeeeeeeeee.com','Full Graphics Design',NULL,'pending','2026-03-28 12:38:22','2026-03-28 11:38:22'),(5,'tsgdd','wsssssssse','09090990','esdf@fsd.com','Full Graphics Design',NULL,'pending','2026-03-28 12:50:29','2026-03-28 11:50:29'),(6,'wre','wettt','0987876756','sdfsdfgs@dyy.com','Full Graphics Design',NULL,'pending','2026-03-28 12:53:01','2026-03-28 11:53:01'),(7,'ertr','ewttt','0987765544','rsefd@rers.com','Full Graphics Design','uploads/payment_receipts/1774700769_7952.jpg','pending','2026-03-28 13:26:09','2026-03-28 12:26:09'),(8,'12131','ewqa','0987654332','tsgnx@tdh.com','Full Graphics Design','uploads/payment_receipts/1774701413_4987.jpg','pending','2026-03-28 13:36:53','2026-03-28 12:36:53'),(9,'daniel','asrat','0909090909','danielasrat@gmail.com','Full Graphics Design','uploads/payment_receipts/1774702677_1242.jpg','approved','2026-03-28 13:57:57','2026-03-28 12:57:57'),(10,'adggf','sdgg','523453252','ewtsfd@wrter.com','Full Graphics Design','uploads/payment_receipts/1774881605_7938.jpg','pending','2026-03-30 16:40:05','2026-03-30 14:40:05'),(11,'rwesd','wersd','0909090909','wtsrfdxc@wer.com','Full Graphics Design','uploads/payment_receipts/1774885350_7428.jpg','pending','2026-03-30 17:42:30','2026-03-30 15:42:30'),(12,'tergdfsv','wefsd','2143546577','qawsdfg@wesd.com','Full Graphics Design','uploads/payment_receipts/1774886547_7739.jpg','pending','2026-03-30 19:02:27','2026-03-30 16:02:27'),(13,'ytrew','uytre','0987654321','','Full Graphics Design','uploads/payment_receipts/1774940151_2474.jpg','pending','2026-03-31 09:55:51','2026-03-31 06:55:51');
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_packages`
--

DROP TABLE IF EXISTS `service_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(200) DEFAULT NULL,
  `package_name` varchar(100) NOT NULL,
  `package_price` varchar(50) NOT NULL,
  `features` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_packages_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_packages`
--

LOCK TABLES `service_packages` WRITE;
/*!40000 ALTER TABLE `service_packages` DISABLE KEYS */;
INSERT INTO `service_packages` VALUES (31,13,'Brand Design','Standard Package','1,500 Birr','Logo Design|Basic Brand Colors|2 Revisions|3 Days Delivery',1,'active','2026-04-02 08:01:23'),(32,13,'Brand Design','Premium Package','3,500 Birr','Logo Design|Complete Brand Identity|Social Media Kit|5 Revisions|5 Days Delivery',2,'active','2026-04-02 08:01:23'),(33,13,'Brand Design','Luxury Package','7,000 Birr','Logo Design|Complete Brand Identity|Social Media Kit|Business Cards|Unlimited Revisions|Priority Support|10 Days Delivery',3,'active','2026-04-02 08:01:23'),(34,14,'Logo Design','Basic Package','800 Birr','1 Logo Concept|2 Revisions|2 Days Delivery|PNG + JPG Files',1,'active','2026-04-02 08:01:23'),(35,14,'Logo Design','Standard Package','1,500 Birr','3 Logo Concepts|5 Revisions|Social Media Kit|PNG + JPG + Vector Files|3 Days Delivery',2,'active','2026-04-02 08:01:23'),(36,14,'Logo Design','Premium Package','3,000 Birr','5 Logo Concepts|Unlimited Revisions|Full Brand Guide|Business Card Design|Social Media Kit|Vector Files|5 Days Delivery',3,'active','2026-04-02 08:01:23'),(37,15,'Social Media Posters','Basic Package','1,000 Birr','5 Posts|2 Revisions|2 Days Delivery|JPG + PNG Files',1,'active','2026-04-02 08:01:23'),(38,15,'Social Media Posters','Standard Package','2,500 Birr','10 Posts|Social Media Kit|5 Revisions|Source Files Included|4 Days Delivery',2,'active','2026-04-02 08:01:23'),(39,15,'Social Media Posters','Premium Package','5,000 Birr','20 Posts|Complete Social Media Kit|Unlimited Revisions|Priority Support|Animation Included|7 Days Delivery',3,'active','2026-04-02 08:01:23'),(40,16,'Banner Design','Basic Package','800 Birr','1 Banner Design|2 Revisions|2 Days Delivery|JPG + PNG',1,'active','2026-04-02 08:01:23'),(41,16,'Banner Design','Standard Package','2,000 Birr','3 Banner Designs|5 Revisions|Source Files Included|4 Days Delivery',2,'active','2026-04-02 08:01:23'),(42,16,'Banner Design','Premium Package','4,000 Birr','5 Banner Designs|Unlimited Revisions|Print Ready Files|Multiple Formats|Priority Support|6 Days Delivery',3,'active','2026-04-02 08:01:23'),(43,17,'Certificate Design','Basic Package','600 Birr','1 Certificate Template|2 Revisions|2 Days Delivery|JPG + PNG',1,'active','2026-04-02 08:01:23'),(44,17,'Certificate Design','Standard Package','1,500 Birr','3 Certificate Templates|5 Revisions|Editable Source Files|Print Ready|4 Days Delivery',2,'active','2026-04-02 08:01:23'),(45,17,'Certificate Design','Premium Package','3,000 Birr','5 Certificate Templates|Unlimited Revisions|Custom Layouts|Multiple Formats|Priority Support|6 Days Delivery',3,'active','2026-04-02 08:01:23'),(46,18,'Thumbnail Design','Basic Package','500 Birr','1 Thumbnail|1 Revision|2 Days Delivery|JPG + PNG',1,'active','2026-04-02 08:01:23'),(47,18,'Thumbnail Design','Standard Package','1,500 Birr','3 Thumbnails|3 Revisions|Source Files Included|4 Days Delivery',2,'active','2026-04-02 08:01:23'),(48,18,'Thumbnail Design','Premium Package','3,000 Birr','5 Thumbnails|Unlimited Revisions|A/B Testing|Priority Support|5 Days Delivery',3,'active','2026-04-02 08:01:23');
/*!40000 ALTER TABLE `service_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_registrations`
--

DROP TABLE IF EXISTS `service_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(200) DEFAULT NULL,
  `package_name` varchar(100) DEFAULT NULL,
  `package_price` varchar(50) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telegram` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_registrations`
--

LOCK TABLES `service_registrations` WRITE;
/*!40000 ALTER TABLE `service_registrations` DISABLE KEYS */;
INSERT INTO `service_registrations` VALUES (1,'Brand Design','Luxury Package','7,000 Birr','werty','098765432','','qwefg','qewretryuiu','pending','2026-04-02 07:32:23');
/*!40000 ALTER TABLE `service_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_requests`
--

DROP TABLE IF EXISTS `service_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `service_name` varchar(200) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_telegram` varchar(100) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `deadline` varchar(100) DEFAULT NULL,
  `status` enum('pending','contacted','in_progress','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_requests`
--

LOCK TABLES `service_requests` WRITE;
/*!40000 ALTER TABLE `service_requests` DISABLE KEYS */;
INSERT INTO `service_requests` VALUES (2,13,'Brand Design','ututuy','tesf@dggg.com','0965431254','shetesfa','test','5,000 - 10,000 Birr','Within 1 month','contacted','2026-03-27 14:56:33'),(4,14,'Logo Design','ff','grtdyyn@54gmail.coj','0987654321','@defrttt','hhji,uji,uju','Under 1,000 Birr','Within 1 week','completed','2026-03-27 16:08:04'),(5,15,'Social Media Posters','wefsdf','werdsfd@rssd.com','0909090909','wesdfzd','weafiksdcnkwafosdcnkj mfsdvcxhkdsz, x','10,000+ Birr','Within 1 week','pending','2026-03-30 15:10:28');
/*!40000 ALTER TABLE `service_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_templates`
--

DROP TABLE IF EXISTS `service_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_templates_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_templates`
--

LOCK TABLES `service_templates` WRITE;
/*!40000 ALTER TABLE `service_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (13,'Brand Design','Complete brand identity development that tells your story and connects with your audience.','fas fa-bullhorn','Brand Strategy & Positioning|Visual Identity System|Brand Guidelines|Business Card Design','From 5,000 Birr','active',1,'2026-03-27 12:58:52'),(14,'Logo Design','Unique, memorable logos that capture your brand essence and make a lasting impression.','fas fa-pen-fancy','Custom Logo Concepts|Vector Format Files|Multiple Revisions|Full Ownership Rights','From 1,500 Birr','active',2,'2026-03-27 12:58:52'),(15,'Social Media Posters','Engaging social media graphics that boost engagement and grow your following.','fas fa-chart-line','Instagram Posts & Stories|Facebook Cover & Ads|LinkedIn Banners|Twitter Headers','From 2,000 Birr','active',3,'2026-03-27 12:58:52'),(16,'Banner Design','Professional banners for websites, events, and advertising campaigns.','fas fa-image','Web Banners & Ads|Event Banners|Roll-up Banners|Billboards','From 1,200 Birr','active',5,'2026-03-27 12:58:52'),(17,'Certificate Design','Elegant certificates and diplomas for courses, events, and achievements.','fas fa-certificate','Course Certificates|Award Certificates|Recognition Plaques|Custom Templates','From 800 Birr','active',6,'2026-03-27 12:58:52'),(18,'Thumbnail Design','Click-worthy YouTube thumbnails that boost views and engagement.','fas fa-video','YouTube Thumbnails|CTR Optimization|A/B Testing|Bulk Pricing Available','From 500 Birr','active',4,'2026-03-27 12:58:52');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_stats`
--

DROP TABLE IF EXISTS `site_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stat_name` varchar(100) DEFAULT NULL,
  `stat_value` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_stats`
--

LOCK TABLES `site_stats` WRITE;
/*!40000 ALTER TABLE `site_stats` DISABLE KEYS */;
INSERT INTO `site_stats` VALUES (1,'students','10K+','2026-03-27 10:52:17'),(2,'projects','500+','2026-03-27 10:52:17'),(3,'mentors','50+','2026-03-27 10:52:17');
/*!40000 ALTER TABLE `site_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_orders`
--

DROP TABLE IF EXISTS `template_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(200) DEFAULT NULL,
  `template_id` int(11) NOT NULL,
  `template_name` varchar(200) NOT NULL,
  `template_image` varchar(500) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_telegram` varchar(100) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `deadline` varchar(100) DEFAULT NULL,
  `status` enum('pending','contacted','in_progress','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_orders`
--

LOCK TABLES `template_orders` WRITE;
/*!40000 ALTER TABLE `template_orders` DISABLE KEYS */;
INSERT INTO `template_orders` VALUES (1,15,'Social Media Posters',7,'Instagram Story Templates','https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=400','tesfahun bayih','tesfahunbayih@kjdwck.com','0909090909','shetesfa','i wantg to only test for this system work or not ?','1,000 - 3,000 Birr','Flexible','pending','2026-03-30 12:56:29'),(2,14,'Logo Design',6,'Vintage & Retro Logo','https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=400','wefsdcx','rwefsd@erfgd.com','0909090909','34rwesdfzxc','43rwesdfcerwfsd','3,000 - 5,000 Birr','Within 2 weeks','pending','2026-03-30 15:54:23');
/*!40000 ALTER TABLE `template_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'dan_creatives_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-25  2:06:47
