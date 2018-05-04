<?php

use yii\db\Migration;

class m170305_055943_user extends Migration
{
    public function up()
    {
        #添加 UserAccount
        $this->execute("CREATE TABLE `user_account` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) UNSIGNED NOT NULL COMMENT '用户ID' ,
`type`  tinyint(3) UNSIGNED NOT NULL COMMENT '类型' ,
`value`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '账号值' ,
`created_at`  int(11) UNSIGNED NOT NULL COMMENT '创建时间' ,
`updated_at`  int(11) UNSIGNED NOT NULL COMMENT '更新时间' ,
PRIMARY KEY (`id`),
UNIQUE INDEX `idx_userid` (`user_id`, `type`) USING BTREE ,
UNIQUE INDEX `idx_type_value` (`type`, `value`) USING BTREE
);");
        
        #添加 UserChangeLog
        $this->execute("CREATE TABLE `user_change_log` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) UNSIGNED NOT NULL COMMENT '用户ID' ,
`field`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字段' ,
`old_value`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '旧值' ,
`new_value`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '新值' ,
`remark`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注' ,
`created_at`  int(11) UNSIGNED NOT NULL COMMENT '创建时间' ,
PRIMARY KEY (`id`),
INDEX `idx_userid_field` (`user_id`,`field`) USING BTREE
);");
        
        #添加 UserBlanceChangeLog
        $this->execute("CREATE TABLE `user_blance_change_log` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) UNSIGNED NOT NULL COMMENT '用户ID' ,
`change_value`  int(11) NOT NULL COMMENT '改变值',
`old_value`  int(11) UNSIGNED NOT NULL COMMENT '旧值' ,
`new_value`  int(11) UNSIGNED NOT NULL COMMENT '新值' ,
`remark`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注' ,
`created_at`  int(11) UNSIGNED NOT NULL COMMENT '创建时间' ,
PRIMARY KEY (`id`),
INDEX `idx_userid_field` (`user_id`) USING BTREE
);");
        
        #添加用户的资料字段
        $this->execute("ALTER TABLE `user`
DROP COLUMN `password_reset_token`,
DROP COLUMN `email`,
MODIFY COLUMN `username`  varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL AFTER `id`,
ADD COLUMN `sex`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别,0为未知,1为男,2为女' AFTER `updated_at`,
ADD COLUMN `birthday`  date NULL DEFAULT NULL COMMENT '生日(Y-m-d)' AFTER `sex`,
ADD COLUMN `country_id`  int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在国家ID' AFTER `birthday`,
ADD COLUMN `province_id`  int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在省份ID' AFTER `country_id`,
ADD COLUMN `city_id`  int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在城市ID' AFTER `province_id`,
ADD COLUMN `avatar`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像' AFTER `city_id`,
ADD COLUMN `about`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '个人说明' AFTER `avatar`;");
        
    }

    public function down()
    {
        echo "m170305_055943_user cannot be reverted.\n";

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
