CREATE TABLE `wechat_mp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 NOT NULL COMMENT '名称',
  `app_id` varchar(255) NOT NULL,
  `app_secret` varchar(255) NOT NULL,
  `default_reply` varchar(2048) CHARACTER SET utf8mb4 NOT NULL,
  `default_welcome` varchar(2048) CHARACTER SET utf8mb4 NOT NULL,
  `access_token` varchar(1024) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `js_ticket` varchar(1024) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  `mch_id` varchar(255) NOT NULL DEFAULT '' COMMENT '商户ID',
  `pay_key` varchar(255) NOT NULL DEFAULT '' COMMENT '支付key',
  `ssl_cert` varchar(255) NOT NULL DEFAULT '' COMMENT 'SSL证书',
  `ssl_key` varchar(255) NOT NULL DEFAULT '' COMMENT 'ssl key',
  `auto_reply_token` varchar(255) NOT NULL DEFAULT '' COMMENT '自动回复TOKEN',
  `auto_reply_encoding_aes_key` varchar(255) NOT NULL DEFAULT '' COMMENT '自动回复加密',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_KEY` (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `wechat_mp_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_user` varchar(255) NOT NULL,
  `msg_data` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FROMUSER_CREATEDAT` (`from_user`,`created_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `wechat_unionid` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `open_id` varchar(128) NOT NULL,
  `union_id` varchar(128) NOT NULL,
  `app_id` varchar(32) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_OID_UID_MK` (`open_id`,`app_id`,`union_id`) USING BTREE,
  KEY `IDX_UNIONID` (`union_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `wechat_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `union_id` varchar(128) NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `country` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `province` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `city` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `headimgurl` varchar(255) NOT NULL DEFAULT '',
  `language` varchar(255) NOT NULL DEFAULT '',
  `data` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_UNIONID` (`union_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

