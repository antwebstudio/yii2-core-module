<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M200114141409AlterCategory
 */
class M200114141409AlterCategory extends Migration
{
	public $tableName = '{{%category}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'short_description', $this->string()->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'short_description');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200114141409AlterCategory cannot be reverted.\n";

        return false;
    }
    */
}
