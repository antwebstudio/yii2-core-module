<?php

namespace ant\attribute\behaviors;

use yii\db\ActiveRecord;
use ant\models\ModelClass;
use ant\dynamicform\models\DynamicField;
use ant\dynamicform\models\DynamicForm;
use ant\dynamicform\models\DynamicFormData;

class DynamicAttribute extends \yii\base\Behavior {	
	public $relation;
	public $relationModelClass;
	
	protected $_fieldValues = [];
	
	public function init() {
		if (!isset($this->relationModelClass)) throw new \Exception('Property "relationModelClass" must be set. ');
		if (!isset($this->relation)) throw new \Exception('Property "relation" must be set. ');
	}

	public function events()
    {
        return
        [
            ActiveRecord::EVENT_AFTER_INSERT    => 'afterInsert',
			ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
			//ActiveRecord::EVENT_BEFORE_VALIDATE    => 'beforeValidate',
        ];
    }
	
	/*public function saveDynamicAttributes($attributeConfigs) {
		if ($this->owner->isNewRecord) throw new \Exception('Must save the active record first before save its dynamic attributes. ');
		
		$dynamicForm = DynamicForm::ensureFor(ModelClass::getClassId(get_class($this->owner)), $this->owner->id);
		
		foreach ($attributeConfigs as $config) {
			$field = new DynamicField;
			$field->attributes = $config;
			
			if (!$field->save()) throw new \Exception(print_r($field->errors, 1));
			
			$dynamicForm->link('dynamicFields', $field);
		}
	}*/
	
	public function setDynamicAttributes($attributes) {
		foreach ($attributes as $name => $value) {
			$this->_fieldValues[$name] = $value;
		}
	}
	
	public function getDynamicAttributes() {
		foreach ($this->owner->dynamicFields as $name => $field) {
			if (!isset($this->_fieldValues[$name])) {
				$this->_fieldValues[$name] = $field->getValue($this->owner->id);
			}
		}
		return $this->_fieldValues;
	}
	
	public function getDynamicForm() {
		//throw new \Exception($this->relation);
		//throw new \Exception(\ant\models\ModelClass::getClassId($this->relationModelClass));
		return $this->owner->hasOne(DynamicForm::class, ['model_id' => 'id'])
			->via($this->relation)
			->onCondition(['model_class_id' => \ant\models\ModelClass::getClassId($this->relationModelClass)]);
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
	
	public function getDynamicFormData() {
		return $this->owner->hasMany(DynamicFormData::class, ['dynamic_form_field_id' => 'id'])
			->via('dynamicFields')
			->andOnCondition(['dynamic_form_id' => $this->owner->dynamicForm->id]);
	}
	
	public function getAllDynamicFormData() {
		return $this->owner->hasMany(DynamicFormData::class, ['model_id' => 'id']);
	}
	
	public function getDynamicAttributeValue($name) {
		if (isset($this->_fieldValues[$name])) {
			return $this->_fieldValues[$name];
		}
		return $this->owner->dynamicFields[$name]->getValue($this->owner->id);
	}
	
	public function getDynamicAttributeSortingAttributes($query, $name) {
		$field = $this->owner->dynamicFields[$name];
		$fieldId = $field->id;
		
		$query->joinWith(['allDynamicFormData' => function($q) use($fieldId) {
			$q->alias('dynamicField_'.$fieldId);
			$q
				//->andOnCondition(['dynamicField_'.$fieldId.'.model_id' => $modelId])
				->andOnCondition(['dynamicField_'.$fieldId.'.dynamic_form_field_id' => $fieldId]);
		}]);
		
		return [
			'asc' => [
				'dynamicField_'.$fieldId.'.value_string' => SORT_ASC,
				'dynamicField_'.$fieldId.'.value_json' => SORT_ASC,
				'dynamicField_'.$fieldId.'.value_text' => SORT_ASC,
				'dynamicField_'.$fieldId.'.value_number' => SORT_ASC,
			],
			'desc' => [
				'dynamicField_'.$fieldId.'.value_string' => SORT_DESC,
				'dynamicField_'.$fieldId.'.value_json' => SORT_DESC,
				'dynamicField_'.$fieldId.'.value_text' => SORT_DESC,
				'dynamicField_'.$fieldId.'.value_number' => SORT_DESC,
			],
		];
	}
	
	public function afterInsert() {
		foreach ($this->_fieldValues as $name => $value) {
			$this->saveFieldValue($name, $value);
		}
	}
	
	public function afterUpdate() {
		foreach ($this->_fieldValues as $name => $value) {
			$this->saveFieldValue($name, $value);
		}
	}
	
	protected function saveFieldValue($name, $value) {
		if (isset($this->owner->dynamicFields[$name])) {
			$this->owner->dynamicFields[$name]->saveValue($value, $this->owner->id);
		} else if (isset($this->owner->dynamicForm)) {
			throw new \Exception('No field is named "'.$name.'". (Form ID: '.$this->owner->dynamicForm->id.')');
		} else if (isset($this->owner->{$this->relation})) {
			throw new \Exception('None of dynamic form is mapped with the model "'.$this->relationModelClass.'" (ID: '.$this->owner->{$this->relation}->id.'). ');
		} else {
			throw new \Exception('Relation "'.$this->relation.'" of "'.get_class($this->owner).'" (ID: '.$this->owner->id.') is null. To use dynamic attribute, you need to set this. ');
		}
	}
}