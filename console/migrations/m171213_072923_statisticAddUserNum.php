<?php

use yii\db\Migration;

class m171213_072923_statisticAddUserNum extends Migration
{
    public function up()
    {
        $sql = "ALTER TABLE `doll_statistic`
ADD COLUMN `user_num`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '总用户数' AFTER `type`";
        $this->getDb()->createCommand($sql)->execute();
    }

    public function down()
    {
        echo "m171213_072923_statisticAddUserNum cannot be reverted.\n";

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
