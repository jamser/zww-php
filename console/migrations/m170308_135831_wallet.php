<?php

use yii\db\Migration;

class m170308_135831_blance extends Migration
{
    public function up()
    {
        $this->getDb()->createCommand("CREATE TABLE `user_wallet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `blance` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '账户余额',
  `income` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收入',
  `withdrawals` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已提现金额',
  `can_withdrawals` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '可提现金额',
  `spend` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '累计消费',
  `virtual_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟货币余额',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_USERID` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;")->execute();
    }

    public function down()
    {
        $this->dropTable('user_wallet');
        return false;
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
