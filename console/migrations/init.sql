
/**
 * 支付日志
 * 2017-04-04
 */
CREATE TABLE `pay_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pay_id` int(11) unsigned NOT NULL COMMENT '支付ID',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '类型',
  `remark` text NOT NULL COMMENT '备注',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_payid` (`pay_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


