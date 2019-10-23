<?php
namespace ant\dynamicform\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use ant\dynamicform\models\DynamicField;
use ant\dynamicform\models\DynamicFormData;

class DynamicFormMemberBehavior extends Behavior
{
    const RELATION_COLUMN_NAME = 'dynamic_form_id';

	public $relation;
	public $attribute = 'dynamic_form_id';
	
	protected $_form;
	
	public function attach($owner) {
		parent::attach($owner);
		
		// May cause ant\behaviors\RuleBehavior not working properly as isAttributeSafe will access rules() method before the RuleBehavior have needed attribute value to decide rules.
		//if (!$owner->isAttributeSafe('dynamicFieldForm')) {
			//throw new \Exception('Please add "dynamicFieldForm" attribute as safe attribute to "'.$owner::className().'" in order to use this behavior. ');
		//}
	}

	public function events()
    {
        return
        [
            ActiveRecord::EVENT_AFTER_INSERT    => 'afterInsert',
			ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
			ActiveRecord::EVENT_BEFORE_VALIDATE    => 'beforeValidate',
        ];
    }
	
	public function beforeValidate($event) {
		$form = $this->getDynamicFieldForm();
		
		if ($this->owner->isAttributeSafe('dynamicFieldForm') && !$form->validate()) {
			$this->owner->addErrors($form->errors);
			$event->isValid = false;
		}
	}
	
	public function afterUpdate()
    {
		$this->dynamicFieldForm->save();
		/*foreach ($this->_field as $label => $value) {
			$this->updateDynamicFieldValue($label, $value);
		}*/
    }
	
    public function afterInsert()
    {
		$this->dynamicFieldForm->save();
		/*
		foreach ($this->_field as $label => $value) {
			$this->updateDynamicFieldValue($label, $value);
		}*/
    }
	
	public function getDynamicFieldByLabel($label) {
		return $this->owner->{$this->relation}->getDynamicFieldByLabel($label);
	}
	
	public function getAllDynamicFormData() {
		return $this->owner->hasMany(DynamicFormData::className(), ['model_id' => 'id']);
	}
	
	public function getDynamicFormData() {
		return $this->owner->hasMany(DynamicFormData::className(), ['model_id' => 'id'])
			->andOnCondition(['dynamic_form_id' => $this->owner->{$this->relation}->{$this->attribute}]);
	}
	
	public function getDynamicFieldValueByLabel($label) {
		$field = $this->getDynamicFieldByLabel($label);
		$data = $this->getDynamicFormData()->andOnCondition(['dynamic_form_field_id' => $field->id])->one();
		
		if (!isset($data)) {
			return null;
		}
		
		return $data->value;
	}
	
	public function setDynamicFieldForm($value) {
		$form = $this->getDynamicFieldForm();
		$form->attributes = $value;
	}
	
	public function getDynamicFieldForm() {
		if (!isset($this->_form)) {
			$this->_form = new \ant\dynamicform\models\DynamicFieldForm([], ['parent' => $this->owner, 'relationName' => $this->relation]);
		}
		return $this->_form;
	}
	
	public function getDynamicForm() {
		return $this->owner->{$this->relation} != null ? $this->owner->{$this->relation}->dynamicForm : null;
	}
	
	/*public function setDynamicFieldAttributes($array) {
		foreach ($array as $name => $value) {
			$this->setDynamicFieldValueByLabel($name, $value);
		}
	}
	
	public function getDynamicFieldAttributes() {
		foreach ($this->owner->dynamicFormData as $formData) {
			$this->_field[$formData->dynamicFormField->label] = $formData->value;
		}
		return $this->_field;
	}
	}*/
	
	public function setDynamicFieldValueByLabel($label, $value) {
		$this->dynamicFieldForm->setDynamicFieldValueByLabel($label, $value);
	}
	
	/*protected function updateDynamicFieldValue($label, $value) {
		$field = $this->getDynamicFieldByLabel($label);
		$data = $this->getDynamicFormData()->andOnCondition(['dynamic_form_field_id' => $field->id])->one();
		
		if (!isset($data)) {
			$data = new DynamicFormData;
			$data->value = $value;
			$data->dynamic_form_id = $this->owner->{$this->relation}->{$this->attribute};
			$data->model_id = $this->owner->id;
			$data->dynamic_form_field_id = $field->id;
			
			if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
		} else {
			$data->value = $value;
			
			if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
		}
	}*/
}
?>
