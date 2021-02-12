<?php

namespace ant\file\migrations\db;

use yii\db\Migration;

/**
 * Class M210212130657AlterFileAttachment
 */
class M210212130657AlterFileAttachment extends Migration
{
    protected $tableName = '{{%file_attachment}}';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'cropper', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'cropper');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M210212130657AlterFileAttachment cannot be reverted.\n";

        return false;
    }
    */
}
