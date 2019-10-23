<?php 
namespace attribute;

use UnitTester;

use ant\dynamicform\models\DynamicField;

class DynamicAttributeQueryBehaviorCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testFilterByFieldIdAndQuery(UnitTester $I)
    {
		$label = 'test field';
		$name = 'testField';
		$value = 'expected value';
		
		$model = new DynamicAttributeQueryBehaviorCestTestModel;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model->saveDynamicAttributes([
			[
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::class,
				'name' => $name,
				'label' => $label,
			],
		]);
		
		$child = new DynamicAttributeQueryBehaviorCestTestChild(['test_id' => $model->id]);
		
		$child->attributes = ['dynamicAttributes' => [$name => $value]]; // Instead of $model->setDynamicAttributes([$name => $value]), use $model->attributes = [];
		
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));
		
		$field = DynamicField::findByName($name, $child->dynamicForm->id);

		$found = DynamicAttributeQueryBehaviorCestTestChild::find()->filterByFieldIdAndQuery($field->id, $value)->one();
		
		$I->assertTrue(isset($found));
		$I->assertEquals($child->id, $found->id);
    }
	
	public function testOrderByFieldId(UnitTester $I)
    {
		$label = 'test field';
		$name = 'testField';
		$value1 = 'expected value';
		$value2 = 'zexpected value';
		
		$model = new DynamicAttributeQueryBehaviorCestTestModel;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model->saveDynamicAttributes([
			[
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::class,
				'name' => $name,
				'label' => $label,
			],
		]);
		
		$child1 = new DynamicAttributeQueryBehaviorCestTestChild(['test_id' => $model->id]);
		$child1->attributes = ['dynamicAttributes' => [$name => $value1]]; // Instead of $model->setDynamicAttributes([$name => $value]), use $model->attributes = [];
		
		if (!$child1->save()) throw new \Exception(print_r($child1->errors, 1));
		
		$first = $I->renderDbTable('{{%dynamic_form_data}}');
		
		$child2 = new DynamicAttributeQueryBehaviorCestTestChild(['test_id' => $model->id]);
		$child2->attributes = ['dynamicAttributes' => [$name => $value2]]; // Instead of $model->setDynamicAttributes([$name => $value]), use $model->attributes = [];
		
		if (!$child2->save()) throw new \Exception(print_r($child2->errors, 1));
		
		$field = DynamicField::findByName($name, $child1->dynamicForm->id);

		$found = DynamicAttributeQueryBehaviorCestTestChild::find()->orderByFieldId($field->id, SORT_ASC)->one();
		
		$I->assertTrue(isset($found));
		$I->assertEquals($child1->id, $found->id);
		
		$found = DynamicAttributeQueryBehaviorCestTestChild::find()->orderByFieldId($field->id, SORT_DESC)->one();
		
		$I->assertTrue(isset($found));
		$I->assertEquals($child2->id, $found->id);
    }
}

class DynamicAttributeCestQuery extends \yii\db\ActiveQuery {
	public function behaviors() {
		return [
            [
                'class' => \ant\attribute\behaviors\DynamicAttributeQueryBehavior::class,
            ],
		];
	}
}

class DynamicAttributeQueryBehaviorCestTestModel extends \yii\db\ActiveRecord {
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

class DynamicAttributeQueryBehaviorCestTestChild extends \yii\db\ActiveRecord {
	public static function find() {
		return new DynamicAttributeCestQuery(get_called_class());
	}
	
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
				'relationModelClass' => DynamicAttributeQueryBehaviorCestTestModel::class,
            ],
		];
	}
	
	public function getType() {
		return $this->hasOne(DynamicAttributeQueryBehaviorCestTestModel::class, ['id' => 'test_id']);
	}
	
	public static function tableName() {
		return '{{%test_child}}';
	}
}
