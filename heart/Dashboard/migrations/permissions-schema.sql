CREATE TABLE `_qdiscuss_prefix_permissions` (
 `group_id` int(10) unsigned NOT NULL,
  `permission` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`, `permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;