<?php
namespace ant\dynamicform\migrations\db;

use yii\db\Migration;

class M170315080842_create_dynamic_form_field extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%dynamic_form_field}}', [
            'id' => $this->primaryKey()->unsigned(),
            'label' => $this->string(255)->notNull(),
            'class' => $this->text()->notNull(),
            'setting' => $this->text(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%dynamic_form_field}}');
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
