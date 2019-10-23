<?php
namespace file\behaviors;

use Yii;
use \UnitTester;

class AttachmentBehaviorCest
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
	
    public function testSaveUpdate(UnitTester $I)
    {
		$filename = 'file/720x480.png';
		$filename2 = 'file2/720x480.png';

		$model = new AttachmentBehaviorCestTestModel;
		$model->attributes = [
			'name' => 'test name',
			'picture' => [
				[
					'path' => $filename,
				]
			],
			'single' => [
				'path' => $filename,
			],
		];
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

		$I->assertEquals($filename, $model->single['path']);
		$I->assertEquals($filename, $model->picture[0]['path']);

		$model = AttachmentBehaviorCestTestModel::findOne($model->id);

		$I->assertEquals($filename, $model->single['path']);
		$I->assertEquals($filename, $model->picture[0]['path']);

		// Update
		$model->attributes = [
			'name' => 'test name',
			'picture' => [
				[
					'path' => $filename2,
				]
			],
			'single' => [
				'path' => $filename2,
			],
		];
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = AttachmentBehaviorCestTestModel::findOne($model->id);
		$I->assertEquals($filename2, $model->single['path']);
		$I->assertEquals($filename2, $model->picture[0]['path']);
    }

    // tests
    public function testAddAttachmentFromPath(UnitTester $I)
    {
		$filename = Yii::getAlias('@tests/fixtures/file/720x480.png');
		
		$test = new AttachmentBehaviorCestTestModel;
		$test->name = 'test name';
		if (!$test->save()) throw new \Exception(print_r($test->errors, 1));
		
		$test->addAttachmentFromPath($filename);
		
		$I->assertEquals(1, count($test->fileAttachments));
    }
	
	public function testDetachBehavior(UnitTester $I) {
		$test = new AttachmentBehaviorCestTestModel;
		$countOfValidators = count($test->validators);
		
		$test->detachBehavior('attachment');
		$I->assertNotEquals($countOfValidators, count($test->validators));
	}
}

class AttachmentBehaviorCestTestModel extends \yii\db\ActiveRecord {
	public $picture;
	public $single;
	
	public function behaviors() {
		return [
			'attachment' => [
				'class' => 'ant\file\behaviors\AttachmentBehavior',
				'modelType' => self::className(),
				'attribute' => 'picture',
				'multiple' => true,
			],
			'singleAttachment' => [
				'class' => 'ant\file\behaviors\AttachmentBehavior',
				'modelType' => self::className(),
				'attribute' => 'single',
				'uploadRelation' => 'singleAttachments',
				'multiple' => false,
				'type' => 'single',
			],
		];
	}
	
	public function rules() {
		return [
			[['name'], 'default', 'value' => 'test name'],
			[['name', 'picture', 'single'], 'safe'],
		];
	}
	
	public static function tableName() {
		return '{{%test}}';
	}

	public function getSingleAttachments() {
		return $this->getAttachmentsRelation('single');
	}

	public function getFileAttachments() {
		return $this->getAttachmentsRelation('default');
	}
}
