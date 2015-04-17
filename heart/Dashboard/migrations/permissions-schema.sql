--
-- Table structure for table `_qdiscuss_prefix_permissions`
--

DROP TABLE IF EXISTS `_qdiscuss_prefix_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_qdiscuss_prefix_permissions` (
  `grantee` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `entity` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `permission` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`grantee`,`entity`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;