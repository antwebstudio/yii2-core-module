<?php 
namespace ant\attributev2\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use ant\attributev2\models\Attributev2;
use ant\attributev2\components\DynamicAttributeManager;
use ant\attributev2\components\FormAttributeManager;
use common\behaviors\SerializeBehavior;

class DynamicAttributeBehavior extends Behavior
{
	protected $_dynamicAttribute = null;
	protected $_formAttributeManager = null;

	public function events()
	{
		return [
			ActiveRecord::EVENT_INIT 			=> 'eventInit',
			ActiveRecord::EVENT_AFTER_FIND 		=> 'afterFind',
			ActiveRecord::EVENT_BEFORE_INSERT 	=> 'beforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE 	=> 'beforeSave',
			ActiveRecord::EVENT_AFTER_INSERT 	=> 'afterSave',
			ActiveRecord::EVENT_AFTER_UPDATE 	=> 'afterSave',
		];
	}

	public function eventInit()
	{
		// prepare global dynamic attribute
		$this->dynamicAttribute->prepareGlobal();
	}

	public function afterFind()
	{
		// prepare own dynamic attribute and dynamic attribute value
		$this->dynamicAttribute->prepareOwn();
	}

	public function beforeSave()
	{
		$this->dynamicAttribute->stash();
	}

	public function afterSave()
	{
		$attributeSetting = [];
		foreach ($this->owner->form as $attribute => $json) 
		{
			$attributeSetting[$attribute] = json_decode($json, true);
		}
		$this->dynamicAttribute->recheckAttribute($attributeSetting);
		$this->dynamicAttribute->commitStash();
	}

	public function getDynamicAttribute()
	{
		if ($this->_dynamicAttribute === null)
		{
			$this->_dynamicAttribute = new DynamicAttributeManager(['owner' => $this->owner]);
		}
		return $this->_dynamicAttribute;
	}

	public function getFormAttributeManager()
	{
		if ($this->_formAttributeManager === null)
		{
			$this->_formAttributeManager = new FormAttributeManager(['owner' => $this->owner]);
		}
		return $this->_formAttributeManager;
	}

	public function getFormAttributes()
	{
		return $this->formAttributeManager->formAttributes;
	}
}