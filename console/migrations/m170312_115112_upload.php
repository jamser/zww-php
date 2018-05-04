<?php

use yii\db\Migration;

class m170312_115112_upload extends Migration
{
    public function up()
    {
        $this->getDb()->createCommand("CREATE TABLE `upload_file` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) UNSIGNED NOT NULL COMMENT '用户ID' ,
`type`  tinyint(3) UNSIGNED NOT NULL COMMENT '类型' ,
`url`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '链接地址' ,
`width`  int(11) UNSIGNED NOT NULL COMMENT '图片宽度' ,
`height`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片高度' ,
`data`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '数据' ,
`created_at`  int(11) UNSIGNED NOT NULL COMMENT '创建时间' ,
`updated_at`  int(11) UNSIGNED NOT NULL COMMENT '更新时间' ,
PRIMARY KEY (`id`),
INDEX `idx_userid` (`user_id`) USING BTREE 
);")->execute();
    }

    public function down()
    {
        $this->dropTable('upload_file');

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
