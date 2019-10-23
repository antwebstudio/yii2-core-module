<?php

namespace ant\file\migrations\db;

use yii\db\Migration;

class M171112090654_create_file extends Migration
{
	protected $tableName = '{{%file}}';
	
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'file_storage_item_id' => $this->integer()->notNull(),
			'folder_id' => $this->integer()->defaultValue(NULL),
            'name' => $this->string(255)->defaultValue(NULL),
			'position' => $this->integer()->notNull()->defaultValue(0),
			'owner_id' => $this->integer()->unsigned()->defaultValue(NULL),
			'created_by' => $this->integer()->defaultValue(NULL),
			'collaborator_group' => $this->integer()->defaultValue(NULL),
			'expire_at' => $this->timestamp()->defaultValue(NULL),
            'created_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);
		
		$this->addForeignKey('fk_file_owner_id', $this->tableName, 'owner_id', '{{%user}}', 'id', 'cascade', 'cascade');
		$this->addForeignKey('fk_file_file_storage_item_id', $this->tableName, 'file_storage_item_id', '{{%file_storage_item}}', 'id', 'cascade', 'cascade');
		$this->addForeignKey('fk_file_folder_id', $this->tableName, 'folder_id', '{{%file_folder}}', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M171112090654_create_file cannot be reverted.\n";

        return false;
    }
    */
}
