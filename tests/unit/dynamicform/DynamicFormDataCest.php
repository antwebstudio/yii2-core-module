<?php
//namespace tests\codeception\common\dynamicform;
//use tests\codeception\common\UnitTester;
use ant\dynamicform\models\DynamicFormData;

class DynamicFormDataCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
	protected function createDynamicForm() {
		$form = new \ant\dynamicform\models\DynamicForm;
		if (!$form->save()) throw new \Exception(\yii\helpers\Html::errorSummary($form));
		
		return $form;
	}
	
	protected function createDynamicFormField() {
		$field = new \ant\dynamicform\models\DynamicField;
		$field->attributes = [
			'label' => 'label',
			'class' => \ant\dynamicform\fieldtypes\classes\TextField::className(),
		];
		if (!$field->save()) throw new \Exception(\yii\helpers\Html::errorSummary($field));
		
		return $field;
	}

    // tests
    public function testInsert(UnitTester $I)
    {
		$form = $this->createDynamicForm();
		$field = $this->createDynamicFormField();
		$value = 'test value';
		
		$data = new DynamicFormData;
		$data->value = $value;
		$data->dynamic_form_id = $form->id;
		$data->model_id = 1;
		$data->dynamic_form_field_id = $field->id;
		
		if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
		
		$data = DynamicFormData::findOne($data->id);
		
		$I->assertEquals($value, $data->value);
    }
	
	public function testUpdate(UnitTester $I) {
		$form = $this->createDynamicForm();
		$field = $this->createDynamicFormField();
		$value = 'test value';
		$newValue = $value.' new';
		
		$data = new DynamicFormData;
		$data->value = $value;
		$data->dynamic_form_id = $form->id;
		$data->model_id = 1;
		$data->dynamic_form_field_id = $field->id;
		
		if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
		
		// Assert before reload data
		$I->assertEquals($value, $data->value);
		
		$data = DynamicFormData::findOne($data->id);
		
		// Assert after reload data
		$I->assertEquals($value, $data->value);
		
		// Update
		$data->value = $newValue;
		if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
		
		// Assert before reload data
		$I->assertEquals($newValue, $data->value);
		
		$data = DynamicFormData::findOne($data->id);
		
		// Assert after reload data
		$I->assertEquals($newValue, $data->value);
	}
}
