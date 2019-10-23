<?php
namespace ant\attribute\components;

use Yii;
use yii\base\Component;

class AttributeExpression extends Component
{
    public $defaultResult = null;

    public $expression = null;

    public $params = [];

    protected $_objects = null;
    protected $_variables = null;

    protected $_context = null;

    //protected $_middleMan = ':';

    protected $_calculateSign = '=';

    protected $_takeABreak = false;

    protected $_result = null;

    public function __construct($expression, $params = [], $config = [])
    {
        $this->expression	 = $expression;
		
		$this->params = $params;
		
		if(is_array($this->params)) {
            foreach ($this->params as $key => $value)
            {
                if (is_callable($value)) {
                    $this->_variables[$key] = $value;
                } else if (is_object($value)) {
                    $this->_objects[$key] = $value;
                } else if (is_array($value) && isset($value['class'])) {
                    $this->_objects[$key] = Yii::createObject($value);
                } else {
                    $this->_variables[$key] = $value;
                }
            }
        } else {
			throw new \Exception('Param not array.');
		}

        parent::__construct($config);
    }

    public function toString()
    {
        if ($this->_result == null) {
            $this->_result = $this->parse();
        }

        return (string) $this->_result;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function setContext($context)
    {
		$this->_context = $context;
    }
	
	protected function getParser() {
		return Yii::$app->expressionParser;
	}

    public function parse()
    {
		//$this->getParser()->setContext($this->_context);
		$context = new \ant\attribute\components\ExpressionParserContext;
		$context->setContext($this->context);
		$context->variables = $this->_variables;
		$context->objects = $this->_objects;
		$context->params = $this->params	;
		//$context->setParams($this->params);
		
		return $this->getParser()->parse($this->expression, $context);
    }
	
	public function getLogs() {
		return $this->getParser()->getLogs();
	}
}