<?php
//namespace tests\codeception\common\dynamicform;
//use tests\codeception\common\UnitTester;

class DynamicFormBuildCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testInsert(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		$I->assertEquals($fieldLabel, $ticketType->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		//$I->assertEquals($fieldName, $ticketType->getDynamicField($fieldName)->name);
    }
	
	public function testInsertWithLabelWithSymbol(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1 / 2 - Label No. (Test, !@#$%^&* Test)';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		$I->assertEquals($fieldLabel, $ticketType->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		//$I->assertEquals($fieldName, $ticketType->getDynamicField($fieldName)->name);
    }
	
	public function testInsertWithLabelWithChineseCharaters(UnitTester $I)
    {
		$fieldName = 'custom2';
		$fieldLabel = '中 文';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		$I->assertEquals($fieldLabel, $ticketType->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		//$I->assertEquals($fieldName, $ticketType->getDynamicField($fieldName)->name);
    }
	
    // tests
    public function testInsertTwoWithSameLabel(UnitTester $I, $scenario)
    {
		$scenario->skip();
		
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel], ['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		$I->assertFalse($ticketType->validate());
    }
	
    // tests
    public function testInsertTwoWithSameLabelButDifferentModel(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType2 = new DynamicFormBuildCestTestModel;
		$ticketType2->name = 'test ticket type';
		
		$ticketType2->setDynamicFormForm($dynamicform);
		
		if (!$ticketType2->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType2));
		
		$I->assertEquals($fieldLabel, $ticketType->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals($fieldLabel, $ticketType2->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		$I->assertEquals(0, $ticketType2->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		$I->assertNotEquals($ticketType->getDynamicFieldByLabel($fieldLabel)->id, $ticketType2->getDynamicFieldByLabel($fieldLabel)->id);
    }
	
    public function testUpdate(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		
		$newFieldName = $fieldName.'_new';
		$newFieldLabel = $fieldLabel.' new';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([['label' => $newFieldLabel]]));
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$I->assertEquals($newFieldLabel, $ticketType->getDynamicFieldByLabel($newFieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($newFieldLabel)->is_deleted);
		//$I->assertEquals($newFieldName, $ticketType->getDynamicField($newFieldName)->name);
    }
	
	public function testUpdateWithoutSetDynamicFormForm(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		
		$newFieldName = $fieldName.'_new';
		$newFieldLabel = $fieldLabel.' new';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		
		$I->assertEquals($fieldLabel, $ticketType->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($fieldLabel)->is_deleted);
    }
	
	public function testDelete(UnitTester $I) {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		
		$newFieldName = $fieldName.'_new';
		$newFieldLabel = $fieldLabel.' new';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([]));
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		
		$I->assertEquals(1, $ticketType->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		
	}
	
	// Add dynamic form after update
	public function testUpdateAddLater(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		
		$newFieldName = $fieldName.'_new';
		$newFieldLabel = $fieldLabel.' new';
		
		//$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormBuildCestTestModel;
		$ticketType->name = 'test ticket type';
		
		//$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([['label' => $newFieldLabel]]));
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$formId = $ticketType->dynamicForm->id;
		
		$ticketType = DynamicFormBuildCestTestModel::findOne($ticketType->id);
		
		$I->assertEquals($newFieldLabel, $ticketType->getDynamicFieldByLabel($newFieldLabel)->label);
		$I->assertEquals(0, $ticketType->getDynamicFieldByLabel($newFieldLabel)->is_deleted);
		//$I->assertEquals($newFieldName, $ticketType->getDynamicField($newFieldName)->name);
		$I->assertEquals($formId, $ticketType->dynamicForm->id);
		$I->assertEquals($formId, $ticketType->dynamicFormForm->dynamicForm->id);
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

class DynamicFormBuildCestTestModel extends \yii\db\ActiveRecord {
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
