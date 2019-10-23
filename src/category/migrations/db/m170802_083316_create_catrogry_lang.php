<?php

namespace ant\category\migrations\db;

use common\components\Migration;
class m170802_083316_create_catrogry_lang extends Migration
{
    public function safeUp()
    {

        $this->createTable('{{%category_lang}}', [
            'id' => $this->primaryKey()->unsigned(),
			'master_id' => $this->integer()->unsigned(),
			'language' => $this->string(6)->notNull(),
            'slug' => $this->string(1024),
            'title' => $this->string(512),
			'subtitle' => $this->string(512),
            'body' => $this->text(),
        ], $this->getTableOptions());

		$this->createIndex('ix_category_lang_language', '{{%category_lang}}', 'language');
		$this->createIndex('ix_category_lang_master_id', '{{%category_lang}}', 'master_id');

        //$this->addForeignKey('fk_lang_article', '{{%lang}}', 'master_id', '{{%article}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_category_lang_article', '{{%category_lang}}', 'master_id', '{{%category}}', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {

        //$this->dropForeignKey('fk_lang_article', '{{%lang}}');
        $this->dropForeignKey('fk_category_lang_article', '{{%category_lang}}');

        //$this->dropTable('{{%lang}}');
        $this->dropTable('{{%category_lang}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170802_083317_create_article_lang cannot be reverted.\n";

        return false;
    }
    */
}
