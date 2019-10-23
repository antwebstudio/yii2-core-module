<?php
namespace ant\category\migrations\db;

use yii\db\Schema;

use ant\db\Migration;

class m170703_123802_category extends Migration
{
    protected $tableName = '{{%category}}';
    
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'slug' => $this->string(1024)->notNull(),
            'title' => $this->string(512)->notNull(),
            'body' => $this->text(),
            'parent_id' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->getTableOptions());
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
