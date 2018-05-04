CREATE TABLE `async_job` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) unsigned NOT NULL,
  `unique_key` varchar(64) NOT NULL,
  `data` text NOT NULL,
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_type_uk` (`type`,`unique_key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
