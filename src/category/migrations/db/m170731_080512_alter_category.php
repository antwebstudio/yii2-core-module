<?php
namespace ant\category\migrations\db;

use common\components\Migration;
class m170731_080512_alter_category extends Migration
{
	public $tableName = '{{%category}}';
	
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'subtitle', $this->string(512));
		$this->addColumn($this->tableName, 'icon_base_url', $this->string(1024));
		$this->addColumn($this->tableName, 'icon_path', $this->string(1024));
		$this->addColumn($this->tableName, 'thumbnail_base_url', $this->string(1024));
		$this->addColumn($this->tableName, 'thumbnail_path', $this->string(1024));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'subtitle');
        $this->dropColumn($this->tableName, 'icon_base_url');
        $this->dropColumn($this->tableName, 'icon_path');
        $this->dropColumn($this->tableName, 'thumbnail_base_url');
        $this->dropColumn($this->tableName, 'thumbnail_path');
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
