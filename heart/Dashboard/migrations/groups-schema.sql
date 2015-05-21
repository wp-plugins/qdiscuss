--
-- Table structure for table `_qdiscuss_prefix_groups`
--

DROP TABLE IF EXISTS `_qdiscuss_prefix_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_qdiscuss_prefix_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_singular` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name_plural` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
   `color` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL ,
    `icon` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;    