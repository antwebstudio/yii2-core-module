<?php

namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

class M170821084140_alter_dynamic_form_field extends Migration
{
    public function safeUp()
    {
		$this->addColumn('{{%dynamic_form_field}}', 'required', $this->smallInteger(1)->defaultValue(0));
		$this->addColumn('{{%dynamic_form_field}}', 'is_deleted', $this->smallInteger(1)->defaultValue(0));
    }

    public function safeDown()
    {
		$this->dropColumn('{{%dynamic_form_field}}', 'required');
        $this->dropColumn('{{%dynamic_form_field}}', 'is_deleted');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M170821084140_alter_dynamic_form_field cannot be reverted.\n";

        return false;
    }
    */
}
