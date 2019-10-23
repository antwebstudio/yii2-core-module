<?php
namespace ant\attribute\behaviors;

use ant\dynamicform\models\DynamicField;

class DynamicAttributeQueryBehavior extends \yii\base\Behavior {
	public function orderByFieldId($fieldId, $orderType = SORT_ASC) {
		$query = $this->owner->joinWith(['allDynamicFormData' => function($q) use($fieldId) {
			$q->alias('dynamicField_'.$fieldId);
			$q
				//->andOnCondition(['dynamicField_'.$fieldId.'.model_id' => $modelId])
				->andOnCondition(['dynamicField_'.$fieldId.'.dynamic_form_field_id' => $fieldId]);
		}]);
		
		if ($orderType == SORT_DESC) {
			return $query
			->orderBy([
				'dynamicField_'.$fieldId.'.value_string' => $orderType,
				'dynamicField_'.$fieldId.'.value_json' => $orderType,
				'dynamicField_'.$fieldId.'.value_text' => $orderType,
				'dynamicField_'.$fieldId.'.value_number' => $orderType,
			]);
		}
		
		return $query
			->orderBy([
				'dynamicField_'.$fieldId.'.value_string' => $orderType,
				'dynamicField_'.$fieldId.'.value_json' => $orderType,
				'dynamicField_'.$fieldId.'.value_text' => $orderType,
				'dynamicField_'.$fieldId.'.value_number' => $orderType,
			]);
	}
	
	public function filterByFieldId($fieldId, $value) {
		//$dynamicFormId
		$field = DynamicField::findOne($fieldId);
		
		$query = $this->owner->joinWith(['allDynamicFormData' => function($q) use($fieldId) {
			$q->alias('dynamicField_'.$fieldId);
			$q//->andOnCondition(['dynamicField_'.$name.'.dynamic_form_id' => $dynamicFormId])
				->andOnCondition(['dynamicField_'.$fieldId.'.dynamic_form_field_id' => $fieldId]);
		}]);
		
		$quotedValue = trim($value) != '' ? '"'.$value.'"' : $value;
		
		return $field->fieldType->andFilterBy($query, $value);
	} 
	
	public function filterByFieldIdAndQuery($fieldId, $value) {
		$query = $this->owner->joinWith(['allDynamicFormData' => function($q) use($fieldId) {
			$q->alias('dynamicField_'.$fieldId);
			$q//->andOnCondition(['dynamicField_'.$name.'.dynamic_form_id' => $dynamicFormId])
				->andOnCondition(['dynamicField_'.$fieldId.'.dynamic_form_field_id' => $fieldId]);
		}]);
		
		$quotedValue = trim($value) != '' ? '"'.$value.'"' : $value;
		
		return $query->andFilterWhere(//['and', 
			//['dynamicFieldForm.dynamic_form_field_id' => $fieldId],
			['or', 
				['like', 'dynamicField_'.$fieldId.'.value_string', $value],
				['like', 'dynamicField_'.$fieldId.'.value_json', $quotedValue],
				['like', 'dynamicField_'.$fieldId.'.value_text', $value],
				['=', 'dynamicField_'.$fieldId.'.value_number', $value],
			]
		//]
		);
	}
}