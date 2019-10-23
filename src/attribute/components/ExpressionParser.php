<?php
namespace ant\attribute\components;

use Yii;

class ExpressionParser extends \yii\base\Component {

    public $params = [];
	public $processors;

    protected $_result = null;
	
	protected $_log = [];
    protected $objects = null;	
    protected $_context = null;

    protected $variables = null;
	
    public function __construct($config = [])
    {
        return \yii\base\Component::__construct($config);
    }


    protected function getVariable($name)
    {
        return $this->variables[$name];
    }
	
	public function parse($expression = null, $context = null) {
		$processors = isset($this->processors) ? $this->processors : [
			\ant\attribute\processors\SimpleVariableProcessor::class,
			\ant\attribute\processors\CustomFunction::class,
			\ant\attribute\processors\GeneralFunction::class,
			MathExpressionParserProcessor::class,
		];
		
		$result = $expression;
		foreach ($processors as $processorClass) {
			if (is_array($processorClass)) {
				$processor = Yii::createObject($processorClass);
			} else {
				$processor = new $processorClass;
			}
			$processor->setExpression($result);
			$processor->setContext($context);
			
			$result = $processor->process();
		}
        $this->_result = $result;

        return $this->_result;
    }
	
	public function getParams() {
		return $this->_params;
	}
	
	public function setParams(array $params) {
        if(is_array($params)) {
            foreach ($params as $key => $value)
            {
                if (is_callable($value)) {
                    $this->variables[$key] = $value;
                } else if (is_object($value)) {
					if ($key == 'parent') throw new \Exception('t1');
                    $this->objects[$key] = $value;
                } else if (is_array($value) && isset($value['class'])) {
					
					if ($key == 'parent') throw new \Exception('t2');
                    $this->objects[$key] = Yii::createObject($value);
                } else {
                    $this->variables[$key] = $value;
                }
            }
        } else {
			throw new \Exception('Param not array.');
		}
	}
	
	public function getContext() {
		return $this->_context;
	}

    public function setContext($context)
    {
		$this->_context = $context;
    }
	
	protected function addLog($message) {
		$this->_log[] = $message;
	}
	
	public function getLogs() {
		return $this->_log;
	}
}