<?php

namespace ant\attributev2\migrations\db;

use yii\db\Migration;

class M180309033723_create_attributev2_demoitem extends Migration
{
    public $tableName = '{{%attributev2_demoitem}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
            'price' => $this->double(15, 2)->defaultValue(0.00),
            'expired_date' => $this->timestamp()->defaultValue(null),
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
        echo "M180309033723_create_attributev2_demoitem cannot be reverted.\n";

        return false;
    }
    */
}
