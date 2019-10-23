<?php

namespace ant\file\migrations\db;

use yii\db\Migration;

class M170309070524_create_file_storage_item extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file_storage_item}}', [
            'id' => $this->primaryKey(),
            'component' => $this->string(255)->notNull(),
            'base_url' => $this->string(1024)->notNull(),
            'path' => $this->string(1024)->notNull(),
            'type' => $this->string(255)->notNull(),
            'size' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'upload_ip' => $this->string(15)->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%file_storage_item}}');
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
