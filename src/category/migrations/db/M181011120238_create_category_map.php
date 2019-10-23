<?php

namespace ant\category\migrations\db;

use ant\db\Migration;

class M181011120238_create_category_map extends Migration
{
  public $tableName = '{{%category_map}}';

    public function safeUp()
    {
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey()->unsigned(),
			'model_class_id' => $this->integer()->unsigned()->notNull(),
			'model_id' => $this->integer(11)->unsigned()->notNull(),
			'category_id' => $this->integer()->unsigned()->notNull(),
		], $this->getTableOptions());

		$this->addForeignKeyTo('{{%model_class}}', 'model_class_id', self::FK_TYPE_CASCADE, self::FK_TYPE_CASCADE);
		$this->addForeignKeyTo('{{%category}}', 'category_id', null, self::FK_TYPE_CASCADE);
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
        echo "M180611120238_create_category_map cannot be reverted.\n";

        return false;
    }
    */
}
