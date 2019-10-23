<?php  
namespace ant\attribute\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

use common\helpers\ArrayHelper;
use common\components\AttributeExpression;
use ant\attribute\models\Attribute;
use ant\attribute\models\AttributeGroup;
use ant\attribute\models\CustomAttribute;

class AttributeBehavior extends Behavior
{
	public $attributeRelation = 'attributeSetting';

	public $contextKey = 'context';

	public $attribute = 'customAttributes';

	protected $_expressions = null;

	protected $_defaultExpressions = null;

	protected $_processeDexpressions;

	protected $_params = [];

	protected $_parsedAttributes = null;

	protected $_customAttributeModel = null;
	
	protected $_customAttributeSaved = false;

	public function events()
	{
		return 
		[
			ActiveRecord::EVENT_AFTER_FIND 		=> 'afterFind',
			ActiveRecord::EVENT_AFTER_VALIDATE 	=> 'afterValidate',
			ActiveRecord::EVENT_AFTER_INSERT 	=> 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE 	=> 'afterSave',
		];
	}

	public function getCustomAttributeModel()
	{
		if ($this->_customAttributeModel == null) 
		{
			$this->_customAttributeModel = new CustomAttribute($this->owner);
		}

		return $this->_customAttributeModel;
	}

	public function afterFind()
	{
		$this->owner->{$this->attribute} = $this->customAttributeModel;
	}

	public function afterValidate()
	{	
		$attributes = $this->owner->{$this->attribute};

		if (!is_array($attributes)) $attributes = [];

		foreach ($attributes as $key => $value) 
		{
			if (is_integer($key) && isset($value['name']) && isset($value['setting']))
			{
				$attributes[$value['name']] = $value['setting'];
				unset($attributes[$key]);
			}
		}

		$this->owner->{$this->attribute} = $this->customAttributeModel;

		$this->customAttributeModel->setAttributes($attributes);
	}

	public function afterSave()
	{
		if (!$this->_customAttributeSaved) {
			$this->_customAttributeSaved = true;
			$this->customAttributeModel->save();
		}
	}

	public function getAttributeGroup()
	{
		return $this->owner->hasOne(AttributeGroup::className(), ['id' => 'attribute_group_id']);
	}





















	public function getParams()
	{
		$this->_params = is_callable($this->_params) ? call_user_func_array($this->_params, []) : $this->_params;

		return ArrayHelper::merge($this->_params, [$this->contextKey => $this->owner]);
	}

	public function setParams($params)
	{
		$this->_params = $params;
	}

	public function setExpressions($expressions)
	{
		$this->_expressions = null;

		$this->_parsedAttributes = null;

		$this->_defaultExpressions = $expressions;
	}

	public function getExpressions()
	{
		if ($this->_expressions == null) 
		{
			$this->_expressions = ArrayHelper::merge(
				$this->customAttributeModel->attributes,
				is_callable($this->_defaultExpressions) ? call_user_func_array($this->_defaultExpressions, []) : $this->_defaultExpressions
			);
		}

		return $this->_expressions;
	}

	public function getParsedAttribute($name, $params = [])
	{
		$params = ArrayHelper::merge($this->params, $params);
		
		if (isset($params[$name])) return $params[$name];

		$expression = new AttributeExpression($this->expressions[$name] , $params);
		$expression->setContext($this->owner);
		return $expression->toString();
	}

	public function setParsedAttribute($name, $newExpression)
	{
		$this->_expressions[$name] = $newExpression;
	}

	public function hasExpressionAttribute($name)
	{
		return isset($this->expressions[$name]);
	}

	public function getParsedAttributes()
	{
		$this->_parsedAttributes = [];

		if ($this->_parsedAttributes == null) 
		{
			if ($this->expressions)
			{
				foreach ($this->expressions as $name => $expression)
				{
					$this->_parsedAttributes[$name] = $this->getParsedAttribute($name);
				}
			}
		}

		return $this->_parsedAttributes;
	}
}
?>