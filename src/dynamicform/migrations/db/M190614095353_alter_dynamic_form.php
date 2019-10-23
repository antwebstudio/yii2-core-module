<?php

namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

/**
 * Class M190614095353_alter_dynamic_form
 */
class M190614095353_alter_dynamic_form extends Migration
{
	protected $tableName = '{{%dynamic_form}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'model_class_id', $this->integer(11)->null()->defaultValue(null));
		$this->addColumn($this->tableName, 'model_id', $this->integer(11)->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'model_class_id');
        $this->dropColumn($this->tableName, 'model_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190614095353_alter_dynamic_form cannot be reverted.\n";

        return false;
    }
    */
}
