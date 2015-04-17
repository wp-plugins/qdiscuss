--
-- Table structure for table `_qdiscuss_prefix_users_discussions`
--

DROP TABLE IF EXISTS `_qdiscuss_prefix_users_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_qdiscuss_prefix_users_discussions` (
  `user_id` int(10) unsigned NOT NULL,
  `discussion_id` int(10) unsigned NOT NULL,
  `read_time` datetime DEFAULT NULL,
  `read_number` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`,`discussion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
