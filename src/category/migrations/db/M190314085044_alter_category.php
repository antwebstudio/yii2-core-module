<?php

namespace ant\category\migrations\db;

use yii\db\Migration;
use yii\db\Query;
use yii\helpers\Inflector;
use ant\helpers\ArrayHelper;

/**
 * Class M190314085044_alter_category
 */
class M190314085044_alter_category extends Migration
{
	public $tableName = '{{%category}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'type_id', $this->integer()->null()->defaultValue(null));
		
		$query = (new Query)->select('type')
					->from($this->tableName)
					->groupBy('type');
					
		foreach ($query->all() as $row) {
			$this->insert('{{%category_type}}', [
				'name' => $row['type'],
				'title' => Inflector::camel2words($row['type']),
			]);
		}
		
		$query = (new Query)->from('{{%category_type}}');
		
		foreach ($query->all() as $row) {
			$this->update($this->tableName, ['type_id' => $row['id']], ['type' => $row['name']]);
		}
		
        $this->dropColumn($this->tableName, 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->addColumn($this->tableName, 'type', $this->string()->null()->defaultValue(null));
		
		$query = (new Query)->from('{{%category_type}}');
		
		foreach ($query->all() as $row) {
			$this->update($this->tableName, ['type' => $row['name']], ['type_id' => $row['id']]);
		}
		
        $this->dropColumn($this->tableName, 'type_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190314085044_alter_category cannot be reverted.\n";

        return false;
    }
    */
}
