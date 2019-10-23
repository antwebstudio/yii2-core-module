<?php

namespace ant\attribute\migrations\db;

use yii\db\Migration;

class M170613014811_create_attribute_group extends Migration
{
	public $tableName = '{{%attribute_group}}';
	
    public function safeUp()
    {
		$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'updated_by' => $this->integer(11)->unsigned()->defaultValue(null),
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
        echo "M170905040524_create_attribute_group cannot be reverted.\n";

        return false;
    }
    */
}
