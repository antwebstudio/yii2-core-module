<?php

namespace ant\tag\migrations\db;

use ant\db\Migration;

/**
 * Class M190123083721_create_tag_map
 */
class M190123083721_create_tag_map extends Migration
{
	protected $tableName = '{{%tag_map}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            //'id' => $this->primaryKey()->unsigned()->notNull(),
			'model_class_id' => $this->integer()->unsigned()->null(),
            'model_id' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'tag_id' => $this->integer()->unsigned()->notNull(),
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
        echo "M190123083721_create_tag_map cannot be reverted.\n";

        return false;
    }
    */
}
