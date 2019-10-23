<?php

namespace ant\attribute\migrations\db;

use yii\db\Migration;

class M170613014812_create_attribute extends Migration
{
	public $tableName = '{{%attribute}}';
	
    public function safeUp()
    {
		$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
			'group_id' => $this->integer(11)->unsigned()->defaultValue(null),
            'name' => $this->string(64)->notNUll(),
            'setting' => $this->text()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'updated_by' => $this->integer(11)->unsigned()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey('fk_attribute_group_id', $this->tableName, 'group_id', '{{%attribute_group}}', 'id', 'cascade', 'cascade');
    
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_attribute_group_id', $this->tableName);

        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M170905040515_create_attribute cannot be reverted.\n";

        return false;
    }
    */
}
