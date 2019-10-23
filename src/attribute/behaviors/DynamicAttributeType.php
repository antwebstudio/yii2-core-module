<?php

namespace ant\attribute\behaviors;

use yii\db\ActiveRecord;
use ant\models\ModelClass;
use ant\dynamicform\models\DynamicField;
use ant\dynamicform\models\DynamicForm;
use ant\dynamicform\models\DynamicFormData;

class DynamicAttributeType extends \yii\base\Behavior {	
	protected $_fieldValues = [];
	protected $_fieldSettings;

	public function events()
    {
        return
        [
            ActiveRecord::EVENT_AFTER_INSERT    => 'afterInsert',
			ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
			//ActiveRecord::EVENT_BEFORE_VALIDATE    => 'beforeValidate',
        ];
    }
	
	public function setDynamicAttributeSettings($attributes) {
		foreach ($attributes as $name => $value) {
			$this->_fieldSettings[$name] = $value;
		}
	}
	
	public function getDynamicAttributeSettings() {
		foreach ($this->owner->dynamicFields as $field) {
			$this->_fieldSettings[$field->id] = $field->attributes;
		}
		return $this->_fieldSettings;
	}
	
	public function saveDynamicAttributes($attributeConfigs) {
		if ($this->owner->isNewRecord) throw new \Exception('Must save the active record first before save its dynamic attributes. ');
		
		$dynamicForm = DynamicForm::ensureFor(ModelClass::getClassId(get_class($this->owner)), $this->owner->id);
		
		foreach ($attributeConfigs as $config) {
			$field = new DynamicField;
			$field->attributes = $config;
			
			if (!$field->save()) throw new \Exception(print_r($field->errors, 1));
			
			$dynamicForm->link('dynamicFields', $field);
		}
	}
	
	/*public function setDynamicAttributes($attributes) {
		foreach ($attributes as $name => $value) {
			$this->_fieldValues[$name] = $value;
		}
	}*/
	
	public function getDynamicForm() {
		return $this->owner->hasOne(DynamicForm::class, ['model_id' => 'id'])
			->onCondition(['model_class_id' => \ant\models\ModelClass::getClassId(get_class($this->owner))]);
	}
	
	public function getDynamicFieldMap() {
		return $this->owner->hasMany(\ant\dynamicform\models\DynamicFieldMap::class, ['dynamic_form_id' => 'id'])
			->via('dynamicForm');
	}
	
	public function getDynamicFields() {
		return $this->owner->hasMany(DynamicField::class, ['id' => 'dynamic_form_field_id'])
			->indexBy('name')
			->via('dynamicFieldMap');
			
		//return $this->owner->hasMany();
	}
	
	/*public function getDynamicFormData() {
		return $this->owner->hasMany(DynamicFormData::class, ['dynamic_form_field_id' => 'id'])
			->via('dynamicFields')
			->andOnCondition(['dynamic_form_id' => $this->owner->dynamicForm->id]);
	}
	
	public function getAllDynamicFormData() {
		return $this->owner->hasMany(DynamicFormData::class, ['dynamic_form_field_id' => 'id'])
			->via('dynamicFields');
	}
	
	public function getDynamicAttributeValue($name) {
		if (isset($this->_fieldValues[$name])) {
			return $this->_fieldValues[$name];
		}
		return $this->owner->dynamicFields[$name]->value;
	}
	*/
	public function afterInsert() {
		if (isset($this->dynamicAttributeSettings)) {
			$this->saveDynamicAttributes($this->dynamicAttributeSettings);
		}
		//throw new \Exception(count($this->owner->dynamicFields));
	}
	
	public function afterUpdate() {
		if (isset($this->dynamicAttributeSettings)) {
			$this->saveDynamicAttributes($this->dynamicAttributeSettings);
		}
		/*foreach ($this->_fieldValues as $name => $value) {
			$this->owner->dynamicFields[$name]->saveValue($value);
		}*/
	}
}