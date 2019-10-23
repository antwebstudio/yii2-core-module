<?php
//namespace tests\codeception\common\file;
//use tests\codeception\common\UnitTester;
use ant\file\models\FileAttachment;
use ant\file\models\FileAttachmentGroup;

class FileAttachmentGroupCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function testAttachment(UnitTester $I) {
        $product = new FileAttachmentGroupCestTestActiveRecord;
        $product->module_id = 'test_module';
        $product->save(false);

        $attachmentGroup = new FileAttachmentGroup;
        //$attachmentGroup->save(false);

        $product->link('attachmentGroup', $attachmentGroup);

        $I->assertTrue($product->attachmentGroup->id > 0);

        $attachment = new FileAttachment;
        $attachment->path = 'etst';
        $attachment->save(false);

        $group = new FileAttachmentGroup;
        //$group->save(false);
        
        $product->link('attachmentGroup', $group);

        $group->link('attachments', $attachment);        

        //$product = TestProduct::findOne($product->id);
        //$I->assertTrue($product->attachment->id > 0);   
        $attachment = FileAttachment::findOne($attachment->id);

        $I->assertTrue($attachment->group_id > 0);

        $group = FileAttachmentGroup::findOne($group->id);

        $I->assertTrue($group->model_id > 0);
    }
}

class FileAttachmentGroupCestTestActiveRecord extends \yii\db\ActiveRecord {
    public static function tableName() {
        return '{{%module}}';
    }

    public function getAttachment() {
        return $this->hasOne(FileAttachment::className(), ['group_id' => 'id'])->via('attachmentGroup');
    }

    public function getAttachmentGroup() {
        return $this->hasOne(FileAttachmentGroup::className(), ['model_id' => 'id']);
    }
}
