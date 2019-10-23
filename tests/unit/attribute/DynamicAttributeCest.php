<?php 
namespace attribute;

use UnitTester;

use ant\dynamicform\models\DynamicFormData;

class DynamicAttributeCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testSetDynamicAttributes(UnitTester $I)
    {
		$label = 'test field';
		$name = 'testField';
		$value = 'expected value';
		
		$model = new DynamicAttributeCestTestModel;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model->saveDynamicAttributes([
			[
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::class,
				'name' => $name,
				'label' => $label,
			],
		]);
		
		$child = new DynamicAttributeCestTestChild(['test_id' => $model->id]);
		
		$child->attributes = ['dynamicAttributes' => [$name => $value]]; // Instead of $model->setDynamicAttributes([$name => $value]), use $model->attributes = [];
		
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));

		$child = DynamicAttributeCestTestChild::findOne($child->id);
		
		$I->assertEquals($value, $child->getDynamicAttributeValue($name));
		$I->assertEquals([$name => $value], $child->dynamicAttributes);
    }
	
    public function testSetDynamicAttributesBySetAttributes(UnitTester $I)
    {
		$label = 'test field';
		$name = 'testField';
		$value = 'expected value';
		
		$model = new DynamicAttributeCestTestModel;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model->saveDynamicAttributes([
			[
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::class,
				'name' => $name,
				'label' => $label,
			],
		]);
		
		$child = new DynamicAttributeCestTestChild(['test_id' => $model->id]);
		
		$child->setDynamicAttributes([$name => $value]);
		
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));

		$child = DynamicAttributeCestTestChild::findOne($child->id);
		$I->assertEquals($value, $child->getDynamicAttributeValue($name));
    }
	
	public function testSaveTwoRecord(UnitTester $I) {
		
		$label = 'test field';
		$name = 'testField';
		$value1 = 'expected value';
		$value2 = 'zexpected value';
		
		$model = new DynamicAttributeCestTestModel;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model->saveDynamicAttributes([
			[
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::class,
				'name' => $name,
				'label' => $label,
			],
		]);
		
		$child1 = new DynamicAttributeCestTestChild(['test_id' => $model->id]);
		$child1->attributes = ['dynamicAttributes' => [$name => $value1]]; // Instead of $model->setDynamicAttributes([$name => $value]), use $model->attributes = [];
		
		if (!$child1->save()) throw new \Exception(print_r($child1->errors, 1));
		
		$child2 = new DynamicAttributeCestTestChild(['test_id' => $model->id]);
		$child2->attributes = ['dynamicAttributes' => [$name => $value2]]; // Instead of $model->setDynamicAttributes([$name => $value]), use $model->attributes = [];
		
		if (!$child2->save()) throw new \Exception(print_r($child2->errors, 1));
		
		$I->assertEquals(2, DynamicFormData::find()->count());
	}
}


class DynamicAttributeCestTestModel extends \yii\db\ActiveRecord {
	public function rules() {
		return [
			[['dynamicAttributeSettings'], 'safe'],
		];
	}
	
	public function behaviors() {
		return [
            [
                'class' => \ant\attribute\behaviors\DynamicAttributeType::class,
            ],
		];
	}
	
	public static function tableName() {
		return '{{%test}}';
	}
}

class DynamicAttributeCestTestChild extends \yii\db\ActiveRecord {
	
	public function rules() {
		return [
			[['dynamicAttributes'], 'safe'],
		];
	}
	
	public function behaviors() {
		return [
            [
                'class' => \ant\attribute\behaviors\DynamicAttribute::class,
				'relation' => 'type',
				'relationModelClass' => DynamicAttributeCestTestModel::class,
            ],
		];
	}
	
	public function getType() {
		return $this->hasOne(DynamicAttributeCestTestModel::class, ['id' => 'test_id']);
	}
	
	public static function tableName() {
		return '{{%test_child}}';
	}
}