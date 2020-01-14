<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M200114152929AlterCategoryType
 */
class M200114152929AlterCategoryType extends Migration
{
	public $tableName = '{{%category_type}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'is_hierarchical', $this->boolean()->defaultValue(1));
		$this->addColumn($this->tableName, 'visible_fields', $this->string()->null()->defaultValue(null));
		$this->addColumn($this->tableName, 'required_fields', $this->string()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_hierarchical');
        $this->dropColumn($this->tableName, 'visible_fields');
        $this->dropColumn($this->tableName, 'required_fields');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200114152929AlterCategoryType cannot be reverted.\n";

        return false;
    }
    */
}
