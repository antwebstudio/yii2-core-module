<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M190218032809_alter_category
 */
class M190218032809_alter_category extends Migration
{
	public $tableName = '{{%category}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'created_at_2', $this->timestamp()->null()->defaultValue(null));
		$this->addColumn($this->tableName, 'updated_at_2', $this->timestamp()->null()->defaultValue(null));
		
		$this->db->createCommand()->update($this->tableName, [
			'updated_at_2' => new \yii\db\Expression('FROM_UNIXTIME(updated_at)'),
			'created_at_2' => new \yii\db\Expression('FROM_UNIXTIME(created_at)'),
		])->execute();
		
		$this->dropColumn($this->tableName, 'created_at');
		$this->dropColumn($this->tableName, 'updated_at');
		
		$this->renameColumn($this->tableName, 'created_at_2', 'created_at');
		$this->renameColumn($this->tableName, 'updated_at_2', 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {	
		$this->renameColumn($this->tableName, 'created_at', 'created_at_2');
		$this->renameColumn($this->tableName, 'updated_at', 'updated_at_2');
		
		$this->addColumn($this->tableName, 'created_at', $this->integer());
		$this->addColumn($this->tableName, 'updated_at', $this->integer());
		
		$this->db->createCommand()->update($this->tableName, [
			'updated_at' => new \yii\db\Expression('UNIX_TIMESTAMP(updated_at_2)'),
			'created_at' => new \yii\db\Expression('UNIX_TIMESTAMP(created_at_2)'),
		])->execute();
		
        $this->dropColumn($this->tableName, 'created_at_2');
        $this->dropColumn($this->tableName, 'updated_at_2');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190218032809_alter_category cannot be reverted.\n";

        return false;
    }
    */
}
