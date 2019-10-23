<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M190314084819_alter_category_type
 */
class M190314084819_alter_category_type extends Migration
{
	public $tableName = '{{%category_type}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->renameColumn($this->tableName, 'type', 'name');
		$this->addColumn($this->tableName, 'title', $this->string()->notNull());
		
		$this->insert($this->tableName, ['id' => '0', 'name' => 'default', 'title' => 'Category']);
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->delete($this->tableName, ['id' => 0]);
		
		$this->renameColumn($this->tableName, 'name', 'type');
        $this->dropColumn($this->tableName, 'title');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190314084819_alter_category_type cannot be reverted.\n";

        return false;
    }
    */
}
