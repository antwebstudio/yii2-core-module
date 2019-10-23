<?php

namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

class M170821090053_alter_dynamic_form_data extends Migration
{
    public function safeUp()
    {
		$this->addColumn('{{%dynamic_form_data}}', 'dynamic_form_id', $this->integer()->notNull());
		$this->addColumn('{{%dynamic_form_data}}', 'model_id', $this->integer()->notNull());
		$this->addColumn('{{%dynamic_form_data}}', 'value_string', $this->string(255)->defaultValue(NULL));
		$this->addColumn('{{%dynamic_form_data}}', 'value_number', $this->integer(12)->defaultValue(NULL));
		$this->addColumn('{{%dynamic_form_data}}', 'value_text', $this->text()->defaultValue(NULL));
		$this->addColumn('{{%dynamic_form_data}}', 'value_json', $this->text()->defaultValue(NULL));
		
		$this->dropColumn('{{%dynamic_form_data}}', 'label');
		
		$this->createIndex('ix_dynamic_form_data_model_id', '{{%dynamic_form_data}}', 'model_id');
		$this->createIndex('ix_dynamic_form_data_dynamic_form_id', '{{%dynamic_form_data}}', 'dynamic_form_id');
    }

    public function safeDown()
    {
		$this->dropIndex('ix_dynamic_form_data_dynamic_form_id', '{{%dynamic_form_data}}');
		$this->dropIndex('ix_dynamic_form_data_model_id', '{{%dynamic_form_data}}');
				
		$this->addColumn('{{%dynamic_form_data}}', 'label', $this->string(256)->notNull());
		
		$this->dropColumn('{{%dynamic_form_data}}', 'dynamic_form_id');
        $this->dropColumn('{{%dynamic_form_data}}', 'model_id');
        $this->dropColumn('{{%dynamic_form_data}}', 'value_string');
        $this->dropColumn('{{%dynamic_form_data}}', 'value_number');
        $this->dropColumn('{{%dynamic_form_data}}', 'value_text');
        $this->dropColumn('{{%dynamic_form_data}}', 'value_json');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M170821090053_alter_dynamic_form_data cannot be reverted.\n";

        return false;
    }
    */
}
