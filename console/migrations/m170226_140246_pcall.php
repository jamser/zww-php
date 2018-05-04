<?php

use yii\db\Migration;

class m170226_140246_pcall extends Migration
{
    public function up()
    {
         
        $sql = "CREATE TABLE `prodcall_caller` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `covers` text NOT NULL,
  `description` text CHARACTER SET utf8mb4 NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `service_time` text NOT NULL COMMENT '服务时间段',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  `apply_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `prodcall_account_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `money_amount_change` decimal(9,2) NOT NULL,
  `remark` text CHARACTER SET utf8mb4 NOT NULL COMMENT '备注',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_USERID` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `prodcall_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `caller_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫者ID',
  `booking_date` date NOT NULL COMMENT '预约日期',
  `booking_time_start` int(11) unsigned NOT NULL COMMENT '预约开始时间',
  `booking_time_end` int(11) unsigned NOT NULL COMMENT '预约日期结束',
  `money_amount` decimal(9,2) unsigned NOT NULL COMMENT '金额',
  `remark` text CHARACTER SET utf8mb4 NOT NULL COMMENT '备注',
  `status` smallint(5) NOT NULL COMMENT '状态',
  `user_confirm` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0',
  `caller_confirm` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0',
  `pay_id` varchar(64) NOT NULL DEFAULT '' COMMENT '支付ID',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间',
  `dispatched_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分发时间',
  `dispatch_type` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分发类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE `prodcall_order_notify` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL COMMENT '订单ID',
  `type` int(11) unsigned NOT NULL COMMENT '类型',
  `notify_type` tinyint(3) unsigned NOT NULL COMMENT '通知类型',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `IDX_ORDER_TYPE` (`order_id`,`type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `prodcall_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `nickname` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '昵称',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0',
  `phone` varchar(20) NOT NULL COMMENT '手机号',
  `email` varchar(64) NOT NULL COMMENT '邮箱',
  `money` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_USERID` (`user_id`) USING BTREE,
  KEY `IDX_PHONE` (`phone`) USING BTREE,
  KEY `IDX_EMAIL` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

ALTER TABLE `order_pay`
ADD INDEX `idx_payid` (`pay_id`) USING BTREE ,
ADD INDEX `idx_userid_orderid` (`user_id`, `order_id`) USING BTREE ,
AUTO_INCREMENT=1;


ALTER TABLE `pay`
ADD COLUMN `trade_no`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '交易号' AFTER `status`,
AUTO_INCREMENT=1;

ALTER TABLE `pay`
ADD COLUMN `out_pay_id`  varchar(255) NOT NULL AFTER `trade_no`;

CREATE TABLE `order_log` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`order_id`  int(11) UNSIGNED NOT NULL COMMENT '订单ID' ,
`operator_user_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作人ID' ,
`remark`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注' ,
`created_at`  int(11) UNSIGNED NOT NULL COMMENT '创建时间' ,
PRIMARY KEY (`id`),
INDEX `idx_orderid` (`order_id`) USING BTREE 
)
;

";
        
        Yii::$app->getDb()->createCommand($sql)->execute();
    }

    public function down()
    {
        $this->dropTable('{{%prodcall_caller}}');
        $this->dropTable('{{%prodcall_account_log}}');
        $this->dropTable('{{%prodcall_order}}');
        $this->dropTable('{{%prodcall_order_notify}}');
        $this->dropTable('{{%prodcall_user}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
