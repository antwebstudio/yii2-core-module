<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M190329135732_alter_category
 */
class M190329135732_alter_category extends Migration
{
	public $tableName = '{{%category}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'tree', $this->integer()->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'tree');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190329135732_alter_category cannot be reverted.\n";

        return false;
    }
    */
}
