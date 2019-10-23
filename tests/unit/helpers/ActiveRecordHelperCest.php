<?php
namespace helpers;

use Yii;
use \UnitTester;
use ant\helpers\ActiveRecordHelper;

class ActiveRecordHelperCest
{
	protected $baseUrl = 'http://localhost';
	protected $basePath = '@runtime/file';
	
    public function _before(UnitTester $I)
    {
		Yii::configure(Yii::$app, [
			'components' => [
				'fileStorage' => [
					'class' => '\trntv\filekit\Storage',
					'baseUrl' => $this->baseUrl,
					'filesystem' => [
						'class' => 'ant\file\LocalFlysystemBuilder',
						'path' => $this->basePath,
					],
					/*'as log' => [
						'class' => 'ant\behaviors\FileStorageLogBehavior',
						'component' => 'fileStorage'
					]*/
				],
			],
		]);
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function test(UnitTester $I)
    {
		$fileAttachment = $this->createFileAttachment();
		$attributes = $fileAttachment->attributes;
		
		$newAttachment = ActiveRecordHelper::duplicate($fileAttachment);
		
		$I->assertEquals($attributes, $fileAttachment->attributes);
		$I->assertEquals($attributes['size'], $newAttachment->attributes['size']);
		$I->assertNotEquals($attributes['path'], $newAttachment->attributes['path']);
    }
	
	protected function createFileAttachment() {
		$filename = Yii::getAlias('@tests/fixtures/file/720x480.png');
		return \ant\file\models\FileAttachment::storeFromPath($filename);
	}
}
