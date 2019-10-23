<?php
//namespace tests\codeception\common\category;
//use tests\codeception\common\UnitTester;
use ant\category\models\Category;
use ant\category\models\CategoryType;

class CategoryCest
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
						'class' => 'common\behaviors\FileStorageLogBehavior',
						'component' => 'fileStorage'
					]*/
				],
			],
		]);
    }

    public function _after(UnitTester $I)
    {
    }
	
	public function testMakeRoot(UnitTester $I) {
		$type = 'testEnsureRoot';
		$typeId = CategoryType::getIdFor($type);
		$root = new Category(['type_id' => $typeId]);
		$root->title = 'test category';
		
		if (!$root->makeRoot()) throw new \Exception(print_r($root->errors, 1));
		
		$I->assertEquals($typeId, $root->type_id);
	}
	
	public function testEnsureRoot(UnitTester $I) {
		$type = 'testEnsureRoot';
		$I->assertEquals(0, Category::find()->rootsOfType($type)->count());
		
		$root = Category::ensureRoot($type);
		
		$I->assertEquals(1, Category::find()->rootsOfType($type)->count());
		$I->assertEquals(0, $root->depth);
		$I->assertTrue(isset($root->type));
		$I->assertEquals($type, $root->type->name);
		
		$root = Category::ensureRoot($type);
		
		$I->assertEquals(1, Category::find()->rootsOfType($type)->count());
		$I->assertEquals(0, $root->depth);
		$I->assertTrue(isset($root->type));
		$I->assertEquals($type, $root->type->name);
	}

    // tests
    public function testSaveUpdate(UnitTester $I)
    {
		$filename = 'file/720x480.png';
		$filename2 = 'file2/720x480.png';

		// Get category type which do not have categories.
		$categoryType = $I->grabFixture('categoryType')->getModel('empty');
		
		$I->assertEquals(0, Category::find()->andWhere(['type_id' => $categoryType->id])->count());
		
		$model = new Category(['type_id' => $categoryType->id]);
		$model->attributes = [
			'title' => 'category root',
			'attachments' => [
				[
					'path' => $filename,
				]
			],
			'thumbnail' => [
				'path' => $filename,
			],
			'banner' => [
				'path' => $filename,
			],
		];
		$model->makeRoot();

		$I->assertEquals($filename, $model->attachments[0]['path']);
		$I->assertEquals($filename, $model->thumbnail['path']);
		$I->assertEquals($filename, $model->banner['path']);
		
		$model = Category::findOne($model->id);
		$model->attributes = [
			'title' => 'category root',
			'attachments' => [
				[
					'path' => $filename2,
				]
			],
			'thumbnail' => [
				'path' => $filename2,
			],
			'banner' => [
				'path' => $filename2,
			],
		];
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

		$I->assertEquals($filename2, $model->attachments[0]['path']);
		$I->assertEquals($filename2, $model->thumbnail['path']);
		$I->assertEquals($filename2, $model->banner['path']);
    }
	
	public function testSlug() {
		$type = 'testType';
		
		$root = Category::ensureRoot($type);
		$root = Category::ensureRoot($type.'2'); // Slug of the root will be same as the previous root, but yet should not cause exception.
	}
	
	public function _fixtures() {
		return [
			'category' => [
				'class' => 'tests\fixtures\CategoryFixture',
			],
			'categoryType' => [
				'class' => 'tests\fixtures\CategoryTypeFixture',
			],
		];
	}
}
