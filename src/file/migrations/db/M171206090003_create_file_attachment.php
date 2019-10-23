<?php

namespace ant\file\migrations\db;

use ant\db\Migration;

class M171206090003_create_file_attachment extends Migration
{
	protected $tableName = '{{%file_attachment}}';
	
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
			'model' => $this->string(255),
            'model_id' => $this->integer()->unsigned()->notNull(),
            //'file_storage_item_id' => $this->integer()->notNull(),
			'order' => $this->integer()->unsigned()->defaultValue(0),
			'path' => $this->string()->notNull(),
            'base_url' => $this->string(),
            'type' => $this->string(),
            'size' => $this->integer(),
            'name' => $this->string(),
            'caption' => $this->string()->null()->defaultValue(null),
            'description' => $this->text()->null()->defaultValue(null),
            'created_at' => $this->integer()
        ], $this->getTableOptions());
		
		$this->createIndexFor(['model', 'model_id']);
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
        echo "M171206090003_create_file_attachment cannot be reverted.\n";

        return false;
    }
    */
}
