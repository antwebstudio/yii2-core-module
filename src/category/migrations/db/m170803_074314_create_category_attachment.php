<?php
namespace ant\category\migrations\db;

use ant\db\Migration;
class m170803_074314_create_category_attachment extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%category_attachment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'path' => $this->string()->notNull(),
            'base_url' => $this->string(),
            'type' => $this->string(),
            'size' => $this->integer(),
            'name' => $this->string(),
            'created_at' => $this->integer()
        ], $this->getTableOptions());

		$this->addForeignKey('fk_category_attachment_article', '{{%category_attachment}}', 'category_id', '{{%category}}', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_category_attachment_article', '{{%category_attachment}}');
        $this->dropTable('{{%category_attachment}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170803_074314_create_article_category_attachment cannot be reverted.\n";

        return false;
    }
    */
}
