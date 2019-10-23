<?php
//namespace tests\codeception\common;
//use tests\codeception\common\UnitTester;
use common\behaviors\DynamicAttribute;

class DynamicAttributeCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
	}
	
	public function testGetDynamicSafeAttributesForEmptyAttributes(UnitTester $I) {
		
		$model = new DynamicAttributeCestTestClass;
		$model->attachBehaviors([
			[
				'class' => DynamicAttribute::className(),
			],
		]);

		$I->assertTrue(is_array($model->getDynamicSafeAttributes()));
		$I->assertEquals(0, count($model->getDynamicSafeAttributes()));
	}
	
	public function testGetDynamicSafeAttributes(UnitTester $I) {
		
		$model = new DynamicAttributeCestTestClass;
		$model->attachBehaviors([
			[
				'class' => DynamicAttribute::className(),
				'attributes' => [
					'attribute1',
					'attribute2',
				],
			],
		]);

		$I->assertTrue(is_array($model->getDynamicSafeAttributes()));
		$I->assertEquals(2, count($model->getDynamicSafeAttributes()));
		$I->assertEquals(['attribute1', 'attribute2'], $model->getDynamicSafeAttributes());
	}

	public function testGetDynamicSafeAttributesWithCallable(UnitTester $I) {
		
		$model = new DynamicAttributeCestTestClass;
		$model->attachBehaviors([
			[
				'class' => DynamicAttribute::className(),
				'attributes' => [
					'attribute1',
					'attribute2' => function() {

					},
				],
			],
		]);

		$I->assertTrue(is_array($model->getDynamicSafeAttributes()));
		$I->assertEquals(1, count($model->getDynamicSafeAttributes()));
		$I->assertEquals(['attribute1'], $model->getDynamicSafeAttributes());
	}

    // tests
    public function testHasDynamicAttribute(UnitTester $I)
    {
		$model = new DynamicAttributeCestTestClass;
		$model->attachBehaviors([
			[
				'class' => DynamicAttribute::className(),
				'attributes' => [
					'attribute1',
					'attribute2' => function() {
						
					}
				],
			],
		]);
		
		$I->assertTrue($model->hasDynamicAttribute('attribute1'));
		$I->assertTrue($model->hasDynamicAttribute('attribute2'));
	}
	
	public function testSetDynamicAttributes(UnitTester $I) {
		
		$model = new DynamicAttributeCestTestClass;
		$model->attachBehaviors([
			[
				'class' => DynamicAttribute::className(),
				'attributes' => [
					'attribute1',
					'attribute2',
				],
			],
		]);

		$data = [
			'attribute1' => 50,
			'attribute2' => 100,
		];

		foreach ($data as $key => $value) {
			$model->setDynamicAttribute($key, $value);
		}
		
		$I->assertEquals($data, $model->dynamicAttributes);
	}
}

class DynamicAttributeCestTestClass extends \yii\base\Model {
	
}
