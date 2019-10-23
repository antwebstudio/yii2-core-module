<?php
namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

class M170315081542_create_dynamic_form_data extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%dynamic_form_data}}', [
            'id' => $this->primaryKey()->unsigned(),
            'label' => $this->string(256)->notNull(),
            'dynamic_form_field_id' => $this->integer()->unsigned(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);

        $this->addForeignKey('fk_dynamic_form_data_dynamic_form_field_id', '{{%dynamic_form_data}}', 'dynamic_form_field_id', '{{%dynamic_form_field}}', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropForeignKey('fk_dynamic_form_data_dynamic_form_field_id', '{{%dynamic_form_data}}');
        $this->dropTable('{{%dynamic_form_data}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
