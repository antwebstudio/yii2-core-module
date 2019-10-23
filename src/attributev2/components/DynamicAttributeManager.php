<?php 
namespace ant\attributev2\components;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use ant\attributev2\models\Attributev2;
use ant\attributev2\models\Attributev2Value;

class DynamicAttributeManager extends Component
{
	public $owner = null;

	protected $_attributeModels = null;
	protected $_valueModels 	= null;

	protected $_attributeSetting = null;
	protected $_attributes = null;
	protected $_rules = null;
	protected $_labels = null;

	protected $_stash = [];

	public function getAttributeModels()
	{
		if ($this->_attributeModels === null)
		{
			$owner = $this->owner;
			$this->_attributeModels = $this->owner->id ? ArrayHelper::merge($this->globalAttributeModels, $this->ownAttributeModels) : $this->globalAttributeModels;
		}
		return $this->_attributeModels;
	}

	protected function getGlobalAttributeModels()
	{
		$globalAttributeModels = [];
		$owner = $this->owner;
		$attributeModels = Attributev2::find()
			->andWhere(['model' => $owner::className()])
			->andWhere(['model_id' => 0])
			->all()
		;
		foreach ($attributeModels as $attributeModel) $globalAttributeModels[$attributeModel->name] = $attributeModel;
		return $globalAttributeModels;
	}

	protected function getOwnAttributeModels()
	{
		$ownAttributeModels = [];
		$owner = $this->owner;
		$attributeModels = Attributev2::find()
			->andWhere(['model' => $owner::className()])
			->andWhere(['model_id' => $owner->id])
			->all()
		;
		foreach ($attributeModels as $attributeModel) $ownAttributeModels[$attributeModel->name] = $attributeModel;
		return $ownAttributeModels;
	}

	public function addTempAttributeModel($name, Attributev2 $attribute)
	{
		if (!is_array($this->_attributeModels)) $this->_attributeModels = [];
		$this->_attributeModels[$name] = $attribute;
	}

	public function getValueModels()
	{
		if ($this->_valueModels === null)
		{
			$this->_valueModels = [];
			foreach ($this->attributeModels as $name => $attributeModelModel) 
			{
				$value = $attributeModelModel->getAttributev2Values()
					->andWhere(['model_id' => $this->owner->id])
					->one();
				$this->_valueModels[$name] = $value;
			}
		}
		return $this->_valueModels;
	}

	public function getAttributeSetting()
	{
		if ($this->_attributeSetting === null)
		{
			$this->_attributeSetting = [];
			foreach ($this->attributeModels as $name => $attributeModel) 
			{
				$this->_attributeSetting[$name] = [
					'fieldtype' => $attributeModel->fieldtype,
					'fieldtype_setting' => $attributeModel->fieldtype_setting,
					'rules' => $attributeModel->rules,
				];
			}
		}
		return $this->_attributeSetting;
	}

	public function getAttributes()
	{
		if ($this->_attributes === null)
		{
			$this->_attributes = [];
			foreach ($this->valueModels as $name => $value) $this->_attributes[$name] = (string) $value;
		}
		return $this->_attributes;
	}

	public function getNames()
	{
		return array_keys($this->attributeModels);
	}

	public function getRules()
	{
		if ($this->_rules === null)
		{
			$this->_rules = [];
			$this->_rules[] = ['form', 'safe'];
			foreach ($this->attributeModels as $attributeModel) 
			{
				$rules = $attributeModel->rules;
				if (!is_array($rules) && is_array(json_decode($rules, true))) $rules = json_decode($rules, true);
				foreach ($rules as $rule) 
				{
					array_unshift($rule, [$attributeModel->name]);
					$this->_rules[] = $rule;
				}
			}	
		}
		return $this->_rules;
	}

	public function getLabels()
	{
		if ($this->_labels === null)
		{
			$this->_labels = [];
			foreach ($this->attributeModels as $attributeModel) 
			{
				$this->_labels[$attributeModel->name] = $attributeModel->label;
			}	
		}
		return $this->_labels;
	}

	public function recheckAttribute($attributeSetting)
	{
		$keep = [];
		$owner = $this->owner;
		foreach ($attributeSetting as $attribute => $setting) 
		{
			if (!isset($this->attributeModels[$attribute])) 
			{
				$model = new Attributev2([
					'model' => $owner::className(),
					'model_id' => $owner->id,
					'name' => $attribute,
				]);
			} else {
				$model = $this->attributeModels[$attribute];
			}
			foreach ($setting as $key => $value) $model->$key = $value;

			if ($model->save())
			{
				if (!isset($this->_stash[$attribute]))
				{
					// append new attribute to stash
					$postValue = Yii::$app->request->post(basename($owner::className()));
					if ($postValue && isset($postValue[$attribute])) 
					{
						$this->_stash[$attribute] = $postValue[$attribute];
					}
				}

				$keep[] = $model->id;
			}
		}

		$query = Attributev2::find()
			->andWhere(['model' => $owner::className()])
			->andWhere(['in', 'model_id', [0, $owner->id]])
		;
        if ($keep) $query->andWhere(['not in', 'id', $keep]);
        foreach ($query->all() as $removedAttribute) 
        {
        	unset($this->_stash[$removedAttribute->name]);
            $removedAttribute->delete();
        }

		$this->_attributeModels = null;
		$this->_attributes 		= null;
	}

	public function prepareGlobal()
	{
		$this->_attributeModels = $this->globalAttributeModels;
	}

	public function prepareOwn()
	{
		$this->prepareOwnAttributes();
		$this->prepareValues();
	}

	protected function prepareOwnAttributes()
	{
		$owner = $this->owner;
		$ownAttributes = [];
		$attributeModels = Attributev2::find()
			->andWhere(['model' => $owner::className()])
			->andWhere(['model_id' => $owner->id])
			->all()
		;
		foreach ($attributeModels as $attributeModel) $ownAttributes[$attributeModel->name] = $attributeModel;
		$this->_attributeModels = ArrayHelper::merge($this->_attributeModels, $ownAttributes);
	}

	public function prepareValues()
	{
		foreach ($this->attributes as $name => $value) $this->owner->$name = (string) $value;
	}

	public function stash()
	{
		$this->_stash = [];
		foreach ($this->attributes as $name => $oldValue) 
		{
			$this->_stash[$name] = $this->owner->$name;
			unset($this->owner->$name);
		}
	}

	public function commitStash()
	{
		$transaction = Yii::$app->db->beginTransaction();
		foreach ($this->_stash as $name => $value)
		{
			if (!isset($this->valueModels[$name])) {
				$valueModel = new Attributev2Value([
					'attributev2_id' 	=> $this->attributeModels[$name]->id,
					'model_id' 			=> $this->owner->id,
				]);
			} else {
				$valueModel = $this->valueModels[$name];
			}
			
			$valueModel->value = (string) $value;
			if (!$valueModel->save()) 
			{
				throw new \Exception(print_r($valueModel->errors, 1));
				$transaction->rollback();
			}
		}
		$transaction->commit();
		$this->refresh();
		$this->prepareValues();
	}

	public function stashPop()
	{
		foreach ($this->_stash as $name => $value) $this->owner->$name = $value;
		$this->_stash = [];
	}

	public function refresh()
	{
		$this->_attributeModels 	= null;
		$this->_valueModels 		= null;
		$this->_attributes 			= null;
		$this->_rules 				= null;
		$this->_labels 				= null;
		$this->_stash 				= [];
	}
}