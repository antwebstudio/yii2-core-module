<?php
namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

class M170315082356_create_dynamic_form_field_map extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%dynamic_form_field_map}}', [
            'id' => $this->primaryKey(),
            'dynamic_form_id' => $this->integer()->unsigned(),
            'dynamic_form_field_id' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->addForeignKey('fk_dynamic_form_field_map_dynamic_form_id', '{{%dynamic_form_field_map}}', 'dynamic_form_id', '{{%dynamic_form}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_dynamic_form_field_map_dynamic_form_field_id', '{{%dynamic_form_field_map}}', 'dynamic_form_field_id', '{{%dynamic_form_field}}', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropForeignKey('fk_dynamic_form_field_map_dynamic_form_field_id', '{{%dynamic_form_field_map}}');
        $this->dropForeignKey('fk_dynamic_form_field_map_dynamic_form_id', '{{%dynamic_form_field_map}}');
        $this->dropTable('{{%dynamic_form_field_map}}');
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
