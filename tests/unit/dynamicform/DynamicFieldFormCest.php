<?php
//namespace tests\codeception\common\dynamicform;
//use tests\codeception\common\UnitTester;

use ant\event\models\Ticket;
use ant\event\models\TicketType;
use ant\dynamicform\models\DynamicFieldForm;

class DynamicFieldFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testWhereFieldNameIsSet(UnitTester $I)
    {
        $fieldName = 'testfield';
        $fieldLabel = 'testfield label';
        $fieldValue = 'testfield value';

        $dynamicform = $this->getDynamicFormArray([['name' => $fieldName, 'label' => $fieldLabel]]);
		
		$ticketType = new DynamicFieldFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
        $ticketType->setDynamicFormForm($dynamicform);
        if (!$ticketType->save()) throw new \Exception(print_r($ticketType->errors, 1));
        
        $ticket = new TestChildModel([
            'test_id' => $ticketType->id,
            'dynamicFieldForm' => [
                $fieldName => $fieldValue,
            ],
        ]);
        if (!$ticket->save()) throw new \Exception(print_r($ticket->errors, 1));

        $I->assertEquals($fieldLabel, $ticket->dynamicFieldForm->getAttributeLabel($fieldName));
        $I->assertEquals($fieldValue, $ticket->dynamicFieldForm->{$fieldName});

        $ticket = TestChildModel::findOne($ticket->id);

		$I->assertEquals($fieldLabel, $ticket->dynamicFieldForm->getAttributeLabel($fieldName));
		$I->assertEquals($fieldValue, $ticket->dynamicFieldForm->{$fieldName});
    }


    // A bug where only occured when field name is not set, and field value is empty
    public function testWhereFieldNameNullAndFieldValueEmpty(UnitTester $I)
    {
        $fieldName = null; // test field name is not set
        $fieldLabel = 'testfield label';
        $fieldValue = ''; // test field value is empty

        $dynamicform = $this->getDynamicFormArray([['name' => $fieldName, 'label' => $fieldLabel]]);
		
		$ticketType = new DynamicFieldFormCestTestModel;
		$ticketType->name = 'test ticket type';
		
        $ticketType->setDynamicFormForm($dynamicform);
        if (!$ticketType->save()) throw new \Exception(print_r($ticketType->errors, 1));
		
		//throw new \Exception($I->renderDbTable(\ant\dynamicform\models\DynamicField::tableName()));

        $ticketType = DynamicFieldFormCestTestModel::findOne($ticketType->id);

        $field = current($ticketType->dynamicForm->dynamicFields);
        
        $ticket = new TestChildModel([
            'test_id' => $ticketType->id,
            'dynamicFieldForm' => [
                $field->handle => $fieldValue,
            ],
        ]);
        if (!$ticket->save()) throw new \Exception(print_r($ticket->errors, 1));

        foreach ($ticket->dynamicFieldForm->attributes as $attribute => $field) {
            $I->assertEquals($fieldValue, $ticket->dynamicFieldForm->{$attribute});
            $I->assertEquals($fieldLabel, $ticket->dynamicFieldForm->getAttributeLabel($attribute));
        }

        $ticket = TestChildModel::findOne($ticket->id);

        foreach ($ticket->dynamicFieldForm->attributes as $attribute => $field) {
            $I->assertEquals($fieldValue, $ticket->dynamicFieldForm->{$attribute});
            $I->assertEquals($fieldLabel, $ticket->dynamicFieldForm->getAttributeLabel($attribute));
        }
    }

    protected function getDynamicFormArray($fields) {
		$fieldConfig = [];
		foreach ($fields as $id => $field) {
			$fieldConfig[$id]['DynamicField'] = [
				'class' => \ant\dynamicform\fieldtypes\classes\TextField::className(), 
				'name' => $field['name'],
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

class DynamicFieldFormCestTestModel extends \yii\db\ActiveRecord {
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

class TestChildModel extends \yii\db\ActiveRecord {
	public function behaviors() {
		return [
            'dynamicForm' =>
            [
                'class' => \ant\dynamicform\behaviors\DynamicFormMemberBehavior::className(),
				'relation' => 'test',
            ],
		];
	}
	
	public static function tableName() {
		return '{{%test_child}}';
	}
	
	public function getTest() {
        return $this->hasOne(DynamicFieldFormCestTestModel::className(), ['id' => 'test_id']);
	}
}

