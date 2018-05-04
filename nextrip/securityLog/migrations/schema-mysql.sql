drop table if exists `user_security_log`;

CREATE TABLE `user_security_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` text CHARACTER SET utf8mb4 NOT NULL,
  `url` text NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  `http_user_agent` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_USERID_TYPE` (`user_id`,`type`,`created_at`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


