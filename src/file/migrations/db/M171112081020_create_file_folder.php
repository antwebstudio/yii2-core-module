<?php

namespace ant\file\migrations\db;

use yii\db\Migration;

class M171112081020_create_file_folder extends Migration
{
	public $tableName = '{{%file_folder}}';
	
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'parent_id' => $this->integer()->defaultValue(NULL),
			'position' => $this->integer()->notNull()->defaultValue(0),
			'owner_id' => $this->integer()->unsigned()->defaultValue(NULL),
			'created_by' => $this->integer()->defaultValue(NULL),
			'collaborator_group' => $this->integer()->defaultValue(NULL),
            'created_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);
		
		$this->addForeignKey('fk_file_folder_owner_id', $this->tableName, 'owner_id', '{{%user}}', 'id', 'cascade', 'cascade');
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
        echo "M171112081020_create_file_folder cannot be reverted.\n";

        return false;
    }
    */
}
