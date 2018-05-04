CREATE TABLE `file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `type` tinyint(2) unsigned NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 NOT NULL,
  `url` varchar(255) NOT NULL,
  `data` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_NAME` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1439 DEFAULT CHARSET=utf8