<?php

namespace ant\tag\migrations\db;

use ant\db\Migration;

/**
 * Class M190123081535_create_tag
 */
class M190123081535_create_tag extends Migration
{
	protected $tableName = '{{%tag}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->notNull(),
			'model_class_id' => $this->integer()->unsigned()->null(),
            'frequency' => $this->integer()->notNull()->defaultValue(0),
            'name' => $this->string(255)->notNull(),
        ], $this->getTableOptions());

		$this->addForeignKeyTo('{{%model_class}}', 'model_class_id');
    }

    /**
     * {@inheritdoc}
     */
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
        echo "M190123081535_create_tag cannot be reverted.\n";

        return false;
    }
    */
}
