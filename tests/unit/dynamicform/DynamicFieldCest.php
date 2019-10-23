<?php
//namespace tests\codeception\common\dynamicform;
use yii\helpers\Html;
//use tests\codeception\common\UnitTester;
use ant\dynamicform\models\DynamicField;

class DynamicFieldCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
	public function testUpdateLabel(UnitTester $I) {
		$fieldName = 'custom1';
		$fieldLabel = 'Me 1';
		$fieldValue = 'test value';
		
		$newFieldLabel = $fieldLabel.' new';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new TestModel;
		$ticketType->name = 'test ticket type';	
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$oldFormId = $ticketType->dynamicForm->id;
		$oldFieldId = $ticketType->getDynamicFieldByLabel($fieldLabel)->id;
		
		// Update ticket type dynamic form
		$ticketType = TestModel::findOne($ticketType->id);
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([$oldFieldId => ['label' => $newFieldLabel]]));
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$newFormId = $ticketType->dynamicForm->id;
		$newFieldId = $ticketType->getDynamicFieldByLabel($newFieldLabel)->id;
		
		$I->assertEquals($oldFormId, $newFormId);
		$I->assertEquals($oldFieldId, $newFieldId);
	}
	
	public function testFieldName() {
		$dynamicModel = new \yii\base\DynamicModel;
		
		$fieldLabel = 'Test (!@#$%^&*-_)';
		$field = new DynamicField;
		$field->attributes = [
			'label' => $fieldLabel,
			'class' => DynamicField::className(),
		];
		if (!$field->save()) throw new \Exception(Html::errorSummary($field));
		
		$field = DynamicField::findOne(['label' => $fieldLabel]);
		
		$dynamicModel->defineAttribute($field->getName());
		\yii\helpers\Html::activeTextInput($dynamicModel, $field->getName());
	}
	
	protected function getDynamicFormArray($fields) {
		$fieldConfig = [];
		foreach ($fields as $id => $field) {
			$fieldConfig[$id]['DynamicField'] = [
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::className(), 
				//'name' => $fieldName,
				'label' => $field['label'],
			];
		}
		return [
			'DynamicFormForm' => [
				'dynamicFields' => $fieldConfig,
			]
		];
	}
}

class TestModel extends \yii\db\ActiveRecord {
	public function behaviors() {
		return [
            'dynamicFormForm' =>
            [
                'class' => \ant\dynamicform\behaviors\DynamicFormBehavior::className(),
            ],
		];
	}
	
	public static function tableName() {
		return '{{%test}}';
	}
}
