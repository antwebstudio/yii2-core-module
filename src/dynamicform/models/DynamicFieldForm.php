<?php
namespace ant\dynamicform\models;

use yii\base\DynamicModel;
use ant\dynamicform\models\DynamicField;

class DynamicFieldForm extends DynamicModel
{
	public $parent;
	public $relationName;
	
	protected $_labelMap = [];
	protected $_labelToAttribute = [];
	protected $_deletedAttributes = [];
	protected $_attributeToId = [];
	
	public function init() {
		if (isset($this->parent->dynamicForm)) {
			//// Need to add trashed attribute to avoid error in grid view using Search model, but trashed attribute should be a not required and not safe attribute.
			foreach ($this->parent->dynamicForm->getDynamicFields()->notTrashed()->all() as $field) {
				$attribute = $field->handle;
				$value = $this->parent->getDynamicFieldValueByLabel($field->label);
				
				// Define attributes
				$this->_labelMap[$attribute] = $field->label;
				$this->_labelToAttribute[$field->label] = $attribute;
				$this->_attributeToId[$attribute] = $field->id;
				$this->defineAttribute($attribute, $value);
				
				// Define rules
				$this->addRule($attribute, 'safe');
				foreach ($field->getInputRules() as $rule) {
					list($ruleAttribute, $ruleName, $options) = $rule;
					$this->addRule($ruleAttribute, $ruleName, $options);
				}
			}
			
			foreach ($this->parent->dynamicFormData as $formData) {
				$attribute = $formData->dynamicField->handle;
				if (in_array($attribute, $this->attributes)) {
					$this->{$attribute} = $formData->value;
				} else {
					$this->_deletedAttributes[$attribute] = $formData->value;
				}
			}
		}
	}
	
	public function setDynamicFieldValueByLabel($label, $value) {
		if (!array_key_exists($label, $this->_labelToAttribute)) throw new \Exception('Field with label "'.$label.'" is not exist. ');
		$attribute = $this->_labelToAttribute[$label];
		$this->{$attribute} = $value;
	}
	
	public function getDeletedAttributeValue($attribute) {
		return isset($this->_deletedAttributes[$attribute]) ? $this->_deletedAttributes[$attribute] : null;
	}
	
	public function getAttributeLabel($attribute) {
		return $this->_labelMap[$attribute];
	}
	
	public function save() {
		if (!isset($this->parent->{$this->relationName}->dynamicForm)) return false;
		
		$fields = $this->parent->{$this->relationName}->dynamicForm->dynamicFields;
		
		$formData = $this->parent->getDynamicFormData()->indexBy('dynamic_form_field_id')->all();
		
		foreach ($this->attributes as $attribute => $value) {
			$id = $this->_attributeToId[$attribute];
			$field = $fields[$id];
			
			if (isset($formData[$field->id])) {
				// Update
				$formData[$field->id]->value = $value;
				
				if (!$formData[$field->id]->save()) throw new \Exception(\yii\helpers\Html::errorSummary($formData[$field->id]));
			} else {
				// Insert
				$data = new DynamicFormData([
					'dynamic_form_id' => $this->parent->{$this->relationName}->dynamicForm->id,
					'dynamic_form_field_id' => $field->id,
				]);
				$data->value = $value;
				$data->model_id = $this->parent->id;
				
				if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
			}
		}
	}
}