<?php

namespace ant\file\migrations\db;

use ant\db\Migration;

/**
 * Class M200108151835AlterFileAttachment
 */
class M200108151835AlterFileAttachment extends Migration
{
    protected $tableName = '{{%file_attachment}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'model_id', $this->morphId());
        $this->addColumn($this->tableName, 'model_class_id', $this->morphClass());
        $this->dropColumn($this->tableName, 'model');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'model_id');
        $this->dropColumn($this->tableName, 'model_class_id');
        $this->addColumn($this->tableName, 'model', $this->string(255));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200108151835AlterFileAttachment cannot be reverted.\n";

        return false;
    }
    */
}
