<?php

namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

/**
 * Class M190423050357_alter_dynamic_field
 */
class M190423050357_alter_dynamic_form_field extends Migration
{
	protected $tableName = '{{%dynamic_form_field}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'name', $this->string(50)->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190423050357_alter_dynamic_field cannot be reverted.\n";

        return false;
    }
    */
}
