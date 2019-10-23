<?php
namespace ant\attribute\behaviors;

use ant\helpers\ArrayHelper;
use ant\attribute\components\AttributeExpression;
use ant\attribute\exceptions\ParseException;

class ExpressionAttribute extends \yii\base\Behavior {

	public $contextKey = 'context';
	public $throwException = true; // Whether to throw exception when parse failed.
	public $throwRawException = false;

	protected $_rawExpressions = [];

	protected $_expressions = null;

	protected $_processeDexpressions;

	protected $_params = [];

	protected $_parsedAttributes = null;

	public function setParams($params)
	{
		$this->_params = $params;
	}

	public function getParams()
	{
		$this->_params = is_callable($this->_params) ? call_user_func_array($this->_params, []) : $this->_params;

		$return = ArrayHelper::merge($this->_params, [$this->contextKey => $this->owner]);
		
		if (!is_array($return)) throw new \Exception('not array');
		
		return $return;
	}

	public function setExpressions($expressions)
	{
		$this->_expressions = null;
		$this->_parsedAttributes = null;

		$this->_rawExpressions = $expressions;
	}

	public function getExpressions()
	{
		if ($this->_expressions === null)
		{
			$this->_expressions = is_callable($this->_rawExpressions) ? call_user_func_array($this->_rawExpressions, [$this->owner]) : $this->_rawExpressions;
		}

		return $this->_expressions;
	}

	public function getParsedAttribute($name, $params = [])
	{
		if (!is_array($params)) throw new \Exception('not array');
		$params = ArrayHelper::merge($this->params, $params);
		
		if (isset($params[$name])) return $params[$name];
		if (!isset($this->expressions[$name])) throw new \Exception('Parsed attribute "'.$name. '" is not defined for: '.get_class($this->owner).'. ('.implode(', ', array_keys($this->expressions)).')');
		
		$expressionString = is_callable($this->expressions[$name]) ? call_user_func_array($this->expressions[$name], [$params]) : $this->expressions[$name];
		
		$expression = new AttributeExpression($expressionString , $params);
		$expression->setContext($this->owner);
		try {
			return $expression->toString();
		} catch (\Exception $ex) {
			if ($this->throwException) {
				if ($this->throwRawException) throw $ex;
				throw new ParseException('Error while parsing attribute: '.$name.' of '.get_class($this->owner).' in expression "'.$expressionString.'". ', $params, $ex);
			}
		}
	}

	public function setParsedAttribute($name, $newExpression)
	{
		$this->_expressions[$name] = $newExpression;
	}

	public function hasExpressionAttribute($name)
	{
		return isset($this->expressions[$name]);
	}

	public function getParsedAttributes($params = [])
	{
		$this->_parsedAttributes = [];

		if ($this->_parsedAttributes == null) {

			foreach ((array) $this->expressions as $name => $expression)
			{
				$this->_parsedAttributes[$name] = $this->getParsedAttribute($name, $params);
			}
		}

		return $this->_parsedAttributes;
	}
}
