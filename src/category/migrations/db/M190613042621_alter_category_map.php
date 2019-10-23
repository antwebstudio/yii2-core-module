<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M190613042621_alter_category_map
 */
class M190613042621_alter_category_map extends Migration
{
	public $tableName = '{{%category_map}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'category_type_id', $this->integer()->unsigned()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'category_type_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190613042621_alter_category_map cannot be reverted.\n";

        return false;
    }
    */
}
