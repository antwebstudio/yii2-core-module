<?php 
namespace ant\attributev2\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use ant\attributev2\components\ModelDefaultFormAttributeInfo;

class FormAttributeManager extends Component
{
	public $owner;

	protected $_dynamicFormAttributes 	= null;
	protected $_defaultFormAttributes 	= null;
	protected $_formAttributes 			= null;

	public $defaultReadOnlyAttributes = [
		'id', 
		'created_by', 
		'updated_by',
		'created_at', 
		'updated_at',
	];

	public function getFormAttributes()
	{
		if ($this->_formAttributes === null)
		{
			$this->_formAttributes = [];
			$formAttributes = ArrayHelper::merge($this->defaultFormAttributes, $this->dynamicFormAttributes);
			foreach ($formAttributes as $attribute => $formAttribute) 
			{
				if (in_array($attribute, $this->defaultReadOnlyAttributes)) continue;
				$this->_formAttributes[$attribute] = $formAttribute;
			}
		}
		return $this->_formAttributes;
	}

	public function getDynamicFormAttributes()
	{
		if ($this->_dynamicFormAttributes === null)
		{
			$this->_dynamicFormAttributes = [];
			foreach ($this->owner->dynamicAttribute->attributeModels as $dynamicAttribute) 
			{
				$this->_dynamicFormAttributes[$dynamicAttribute->name] = $dynamicAttribute->fieldtype()->frontendInput;
			}	
		}
		return $this->_dynamicFormAttributes;
	}

	public function getDefaultFormAttributes()
	{
		if ($this->_defaultFormAttributes === null) 
		{
			$this->_defaultFormAttributes = $this->ownerFormAttributeInfo->formAttributes;
		}
		return $this->_defaultFormAttributes;
	}

	public function getOwnerFormAttributeInfo()
	{
		return new ModelDefaultFormAttributeInfo(['model' => $this->owner]);
	}
}