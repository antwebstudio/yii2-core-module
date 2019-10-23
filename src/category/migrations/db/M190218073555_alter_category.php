<?php

namespace ant\category\migrations\db;

use yii\db\Migration;

/**
 * Class M190218073555_alter_category
 */
class M190218073555_alter_category extends Migration
{
    public $tableName = '{{%category}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'left', $this->integer()->notNull());
        $this->addColumn($this->tableName, 'right', $this->integer()->notNull());
        $this->addColumn($this->tableName, 'depth', $this->integer()->notNull());
		
		$allTypes = (new \yii\db\Query())
			->select(['type', 'COUNT(*) as count'])
			->from($this->tableName)
			->groupBy('type')
			->all();
			
		$updated = [];
		
		foreach ($allTypes as $type) {
			$max = $type['count'] * 2;
			
			// Insert root node
			$this->db->createCommand()->insert($this->tableName, [
				'title' => 'Uncategoried',
				'slug' => '',
				'left' => 1,
				'right' => $max + 2,
				'depth' => 0,
				'type' => $type['type'],
				'created_at' => new \yii\db\Expression('NOW()'),
				'updated_at' => new \yii\db\Expression('NOW()'),
			])->execute();
					
			//for ($i = 1; $i <= $type['count']; $i++) {
				$index = 2;
				
				$query = new \yii\db\Query();
				$query = $query->from($this->tableName)->where(['left' => 0, 'right' => 0, 'type' => $type['type']]);
				
				foreach ($query->each() as $row) {
					$this->db->createCommand()->update($this->tableName, [
						'left' => $index++,
						'right' => $index++,
						'depth' => 1,
					], 'id = :id', ['id' => $row['id']])->execute();
				}
			//}
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {	
        $this->dropColumn($this->tableName, 'left');
        $this->dropColumn($this->tableName, 'right');
        $this->dropColumn($this->tableName, 'depth');
    }
}
