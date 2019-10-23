<?php
//namespace tests\codeception\common\dynamicform;
//use tests\codeception\common\UnitTester;
use ant\dynamicform\models\DynamicField;

class DynamicFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
	// Set value when insert new record
    public function testInsert(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['name' => $fieldName, 'label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticket = new DynamicFormCestTestChildModel;
		$ticket->test_id = $ticketType->id;
		$ticket->setDynamicFieldValueByLabel($fieldLabel, $fieldValue);
		
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		
		$I->assertEquals($ticketType->id, $ticket->test->id);
		$I->assertEquals($fieldLabel, $ticket->test->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticket->test->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		//$I->assertEquals($fieldName, $ticket->ticketType->getDynamicFieldByLabel($fieldLabel)->name);
		$I->assertEquals($fieldValue, $ticket->getDynamicFieldValueByLabel($fieldLabel));
    }
	
	public function testInsertFieldWithLabelWithSymbols(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1 (!@#$%^&* Test)';
		$fieldValue = 'test value';
		
		$dynamicform = $this->getDynamicFormArray([['name' => $fieldName, 'label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticket = new DynamicFormCestTestChildModel;
		$ticket->test_id = $ticketType->id;
		$ticket->setDynamicFieldValueByLabel($fieldLabel, $fieldValue);
		
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		
		$I->assertEquals($ticketType->id, $ticket->test->id);
		$I->assertEquals($fieldLabel, $ticket->test->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticket->test->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		//$I->assertEquals($fieldName, $ticket->ticketType->getDynamicFieldByLabel($fieldLabel)->name);
		$I->assertEquals($fieldValue, $ticket->getDynamicFieldValueByLabel($fieldLabel));
    }
	
	// tests
	// Set value when insert record, and update value when update record (field label not changed)
    public function testUpdateValue(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Custom 1';
		$fieldValue = 'test value';
		
		$newFieldValue = $fieldValue . ' new';
		
		$dynamicform = $this->getDynamicFormArray([['name' => $fieldName, 'label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$ticket = $this->getNewTicket($ticketType);
		$ticket->setDynamicFieldValueByLabel($fieldLabel, $fieldValue);
		
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		// Assert old value correct
		$I->assertEquals($fieldValue, $ticket->getDynamicFieldValueByLabel($fieldLabel));
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		
		// Update
		$ticket->setDynamicFieldValueByLabel($fieldLabel, $newFieldValue);
		//$I->assertEquals($newFieldValue, $ticket->getDynamicFieldValueByLabel($fieldLabel)); // @TODO: uncomment this test assert
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		
		$I->assertEquals($ticketType->id, $ticket->test->id);
		$I->assertEquals($fieldLabel, $ticket->test->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals($fieldLabel, $ticket->getDynamicFieldByLabel($fieldLabel)->label);
		$I->assertEquals(0, $ticket->test->getDynamicFieldByLabel($fieldLabel)->is_deleted);
		//$I->assertEquals($fieldName, $ticket->ticketType->getDynamicFieldByLabel($fieldLabel)->name);
		$I->assertEquals($newFieldValue, $ticket->getDynamicFieldValueByLabel($fieldLabel));
    }
	
	// tests
	// Set value when insert record, change label and then get the value
    public function testGetValueAfterLabelChanged(UnitTester $I)
    {
		$fieldName = 'custom1';
		$fieldLabel = 'Me 1';
		$fieldValue = 'test value';
		
		$newFieldLabel = $fieldLabel.' new';
		
		$dynamicform = $this->getDynamicFormArray([['label' => $fieldLabel]]);
		
		$ticketType = new DynamicFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($dynamicform);
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$fieldId = $ticketType->getDynamicFieldByLabel($fieldLabel)->id;
		
		// Set dynamic field value
		$ticket = $this->getNewTicket($ticketType);
		$ticket->setDynamicFieldValueByLabel($fieldLabel, $fieldValue);
		
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		// Update ticket type
		$ticketType = DynamicFormCestTestModel::findOne($ticketType->id);
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([$fieldId => ['label' => $newFieldLabel]]));
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		// Assert ticket field value after field label changed
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id); // Need to reload ticket after ticket type dynamic fields is updated.
		
		$I->assertEquals(null, $ticket->getDynamicFieldByLabel($fieldLabel));
		$I->assertEquals($fieldValue, $ticket->getDynamicFieldValueByLabel($newFieldLabel));
		$I->assertEquals(0, $ticket->getDynamicFieldByLabel($newFieldLabel)->is_deleted);
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		
		$I->assertEquals(null, $ticket->getDynamicFieldByLabel($fieldLabel));
		$I->assertEquals($fieldValue, $ticket->getDynamicFieldValueByLabel($newFieldLabel));
		$I->assertEquals(0, $ticket->getDynamicFieldByLabel($newFieldLabel)->is_deleted);
    }
	
	public function testBulkAssignDynamicFieldAttributes(UnitTester $I) {
		$fieldLabel = '1Custom - test';
		$fieldValue = 'test value';
		
		$ticketType = new DynamicFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([['label' => $fieldLabel]]));
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$attributes = [
			$ticketType->getDynamicFieldByLabel($fieldLabel)->getName() => $fieldValue,
		];
		
		// Set dynamic field value
		$ticket = $this->getNewTicket($ticketType);
		
		$ticket->dynamicFieldForm->attributes = $attributes;
		
		$I->assertEquals($attributes, $ticket->dynamicFieldForm->attributes);
		
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		$I->assertEquals($attributes, $ticket->dynamicFieldForm->attributes);
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		
		$I->assertEquals($attributes, $ticket->dynamicFieldForm->attributes);
	}
	
	public function testBulkAssignDynamicFieldAttributesUsingLoad(UnitTester $I) {
		$fieldLabel = 'Custom 1';
		$fieldValue = 'test value';
		
		$ticketType = new DynamicFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
		$ticketType->setDynamicFormForm($this->getDynamicFormArray([['label' => $fieldLabel]]));
		
		if (!$ticketType->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticketType));
		
		$attributes = [
			$ticketType->getDynamicFieldByLabel($fieldLabel)->getName() => $fieldValue,
		];
		
		// Set dynamic field value
		$ticket = $this->getNewTicket($ticketType);
		$formName = $ticket->formName();
		
		$ticket->load([$formName => ['dynamicFieldForm' => $attributes]]);
		if (!$ticket->save()) throw new \Exception(\yii\helpers\Html::errorSummary($ticket));
		
		$I->assertEquals($attributes, $ticket->dynamicFieldForm->attributes);
		
		$ticket = DynamicFormCestTestChildModel::findOne($ticket->id);
		$I->assertEquals($attributes, $ticket->dynamicFieldForm->attributes);
	}
	
	protected function getNewTicket($ticketType) {
		$ticket = new DynamicFormCestTestChildModel;
		$ticket->test_id = $ticketType->id;
		return $ticket;
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

class DynamicFormCestTestModel extends \yii\db\ActiveRecord {
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

class DynamicFormCestTestChildModel extends \yii\db\ActiveRecord {
	public function behaviors() {
		return [
            'dynamicForm' =>
            [
                'class' => \ant\dynamicform\behaviors\DynamicFormMemberBehavior::className(),
				'relation' => 'test',
            ],
		];
	}
	
	public function rules() {
		return [
			[['dynamicFieldForm'], 'safe'],
		];
	}
	
	public static function tableName() {
		return '{{%test_child}}';
	}
	
	public function getTest() {
        return $this->hasOne(DynamicFormCestTestModel::className(), ['id' => 'test_id']);
	}
}
