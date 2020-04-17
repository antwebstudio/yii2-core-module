<?php

namespace ant\language\migrations\db;

use ant\db\Migration;

/**
 * Class M200417135757LanguageTranslatables
 */
class M200417135757LanguageTranslatable extends Migration
{
	protected $tableName = '{{%language_translatable}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
			'translatable_id' => $this->morphId(),
            'translatable_class_id' => $this->morphClass(),
			'language' => $this->string(6),
			'translated' => 'longtext',
        ], $this->getTableOptions());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200417135757LanguageTranslatables cannot be reverted.\n";

        return false;
    }
    */
}
