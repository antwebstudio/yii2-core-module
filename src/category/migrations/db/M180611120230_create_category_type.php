<?php

namespace ant\category\migrations\db;

use ant\db\Migration;

class M180611120230_create_category_type extends Migration
{
  public $tableName = '{{%category_type}}';

    public function safeUp()
    {
      $this->createTable($this->tableName, [
              'id' => $this->primaryKey()->unsigned(),
              'type' => $this->string()->notNull(),
              'model' => $this->string(255),
              'status' => $this->smallInteger()->notNull()->defaultValue(0),
          ], $this->getTableOptions());
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
        echo "M180611120230_create_category_type cannot be reverted.\n";

        return false;
    }
    */
}
