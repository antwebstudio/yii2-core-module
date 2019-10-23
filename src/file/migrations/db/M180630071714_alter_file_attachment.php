<?php

namespace ant\file\migrations\db;

use yii\db\Query;
use common\components\Migration;

class M180630071714_alter_file_attachment extends Migration
{
    protected $tableName = '{{%file_attachment}}';
    
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'group_id', $this->integer()->unsigned()->null()->after('id'));
        
        $this->createAttachmentGroupTable();

        $query = new Query;
        $query->select('*')->from($this->tableName)->where(['group_id' => null])->limit(20);

        while ($records = $query->all()) {				
            foreach($records as $record) {
                $this->processRecord($record);
            }
        }
        
        $this->dropColumn($this->tableName, 'model');
        $this->dropColumn($this->tableName, 'model_id');

        
    }

    public function safeDown()
    {
        $this->addColumn($this->tableName, 'model', $this->string(255));
        $this->addColumn($this->tableName, 'model_id', $this->integer()->unsigned()->notNull());

        $query = new Query;
        $query->select('attachment.*, group.model, group.model_id')->from(['attachment' => $this->tableName])
            ->leftJoin(['group' => '{{%file_attachment_group}}'], '`attachment`.`group_id` = `group`.`id`')
            ->where(['IS NOT', 'group_id', null])->limit(20);

        //echo $query->createCommand()->rawSql;

        while ($records = $query->all()) {
            foreach($records as $record) {
                $this->reverseRecord($record);
            }
        }
        
        $fromForeignKey = 'group_id';
        $indexName = $this->getIndexName($this->tableName, $fromForeignKey, self::INDEX_FK);
        $this->dropForeignKey($indexName, $this->tableName);
        $this->dropColumn($this->tableName, 'group_id');

        $this->dropTable('{{%file_attachment_group}}');
    }

    protected function createAttachmentGroupTable() {
        
		$this->createTable('{{%file_attachment_group}}', [
            'id' => $this->primaryKey()->unsigned(),
			'model' => $this->string(255),
            'model_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->string()->defaultValue('default'),
            'created_at' => $this->timestamp()->null()->defaultValue(null),
        ], $this->getTableOptions());
        
        $fromTable = $this->tableName;
        $fromForeignKey = 'group_id';
        $toTable = '{{%file_attachment_group}}';
        $toPrimaryKey = 'id';

        $indexName = $this->getIndexName($this->tableName, $fromForeignKey, self::INDEX_FK);
		$this->addForeignKeyTo($toTable, 'group_id');
    }

    protected function processRecord($record) {
		$this->insert('{{%file_attachment_group}}', [
			'model' => $record['model'],
			'model_id' => $record['model_id'],
		]);
		
		$id = \Yii::$app->db->getLastInsertID();
		
		$this->update($this->tableName, ['group_id' => $id], ['id' => $record['id']]);
    }

    protected function reverseRecord($record) {
        $this->update($this->tableName, ['group_id' => null, 'model' => $record['model'], 'model_id' => $record['model_id']], ['id' => $record['id']]);

        $this->delete('{{%file_attachment_group}}', ['id' => $record['group_id']]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180630081714_alter_file_attachment cannot be reverted.\n";

        return false;
    }
    */
}
