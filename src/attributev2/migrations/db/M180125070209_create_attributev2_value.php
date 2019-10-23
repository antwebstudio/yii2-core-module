<?php

namespace ant\attributev2\migrations\db;

use yii\db\Migration;

class M180125070209_create_attributev2_value extends Migration
{
    public $tableName = '{{%attributev2_value}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'attributev2_id' => $this->integer()->unsigned(),
            'model_id' => $this->integer()->unsigned(),
            'value' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_attributev2_attributev2_id', $this->tableName, 'attributev2_id', '{{%attributev2}}', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_attributev2_attributev2_id', $this->tableName);
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180125070209_create_attributev2_value cannot be reverted.\n";

        return false;
    }
    */
}
