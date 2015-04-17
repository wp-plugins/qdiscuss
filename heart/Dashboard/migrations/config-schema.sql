--
-- Table structure for table `_qdiscuss_prefix_config`
--

DROP TABLE IF EXISTS `_qdiscuss_prefix_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_qdiscuss_prefix_config` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` blob,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

