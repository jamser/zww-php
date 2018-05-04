<?php

use yii\db\Migration;

class m170311_040201_setting extends Migration
{
    public function up()
    {
        $this->getDb()->createCommand("CREATE TABLE `setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT 'KEY',
  `value` text NOT NULL COMMENT '值',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_key` (`key`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;")->execute();
    }

    public function down()
    {
        echo "m170311_040201_setting cannot be reverted.\n";

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
