<?php

namespace ant\file\migrations\db;

use yii\db\Migration;

/**
 * Class M200519152117AlterFileAttachment
 */
class M200519152117AlterFileAttachment extends Migration
{
    protected $tableName = '{{%file_attachment}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'created_at', $this->timestamp()->null()->defaultValue(null));		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'created_at', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200519152117AlterFileAttachment cannot be reverted.\n";

        return false;
    }
    */
}
