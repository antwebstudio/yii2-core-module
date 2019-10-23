<?php
namespace ant\attribute\components;

class ExpressionParserContext {
	public $variables;
	public $objects;
	
	protected $_context;
	
	public function setParams($params) {
		$this->_params = $params;
		
		if(is_array($this->_params)) {
            foreach ($this->_params as $key => $value)
            {
                if (is_callable($value)) {
                    $this->variables[$key] = $value;
                } else if (is_object($value)) {
                    $this->objects[$key] = $value;
                } else if (is_array($value) && isset($value['class'])) {
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
	
	public function setContext($context) {
		$this->_context = $context;
	}
}