<?php

namespace ant\category\migrations\db;

use ant\db\Migration;

/**
 * Class M200525032126AlterCategoryMap
 */
class M200525032126AlterCategoryMap extends Migration
{
	public $tableName = '{{%category_map}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKeyTo('{{%category}}', 'category_id');
		$this->addForeignKeyTo('{{%category}}', 'category_id', self::FK_TYPE_CASCADE, self::FK_TYPE_CASCADE);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200525032126AlterCategoryMap cannot be reverted.\n";

        return false;
    }
    */
}
