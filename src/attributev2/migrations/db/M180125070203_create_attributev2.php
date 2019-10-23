<?php

namespace ant\attributev2\migrations\db;

use yii\db\Migration;

class M180125070203_create_attributev2 extends Migration
{
    public $tableName = '{{%attributev2}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'model' => $this->string()->notNull(),
            'model_id' => $this->integer()->unsigned()->defaultValue(0),
            'fieldtype' => $this->text()->notNull(),
            'fieldtype_setting' => $this->text()->defaultValue(null),
            'rules' => $this->text()->defaultValue(null),
            'name' => $this->string()->notNull(),
            'label' => $this->string()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180125070203_create_attributev2 cannot be reverted.\n";

        return false;
    }
    */
}
