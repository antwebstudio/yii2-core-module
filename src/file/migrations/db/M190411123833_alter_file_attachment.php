<?php

namespace ant\file\migrations\db;

use yii\db\Migration;

/**
 * Class M190411123833_alter_file_attachment
 */
class M190411123833_alter_file_attachment extends Migration
{
    protected $tableName = '{{%file_attachment}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'data', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'data');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190411123833_alter_file_attachment cannot be reverted.\n";

        return false;
    }
    */
}
