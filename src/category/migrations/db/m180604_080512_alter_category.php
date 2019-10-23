<?php
namespace ant\category\migrations\db;

use ant\db\Migration;
class m180604_080512_alter_category extends Migration
{
	public $tableName = '{{%category}}';
	
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'type', $this->string(512));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170731_080512_alter_article_category cannot be reverted.\n";

        return false;
    }
    */
}
