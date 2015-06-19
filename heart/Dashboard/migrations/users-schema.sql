--
-- Table structure for table `_qdiscuss_prefix_users`
--

DROP TABLE IF EXISTS `_qdiscuss_prefix_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_qdiscuss_prefix_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `confirmation_token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_activated` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `bio_html` text COLLATE utf8_unicode_ci,
  `avatar_path` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `join_time` datetime DEFAULT NULL,
  `last_seen_time` datetime DEFAULT NULL,
  `read_time` datetime DEFAULT NULL,
  `discussions_count` int(10) unsigned NOT NULL DEFAULT '0',
  `comments_count` int(10) unsigned NOT NULL DEFAULT '0',
  `wp_user_id` int(10) unsigned DEFAULT NULL,
  `preferences` blob,
  `notification_read_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;