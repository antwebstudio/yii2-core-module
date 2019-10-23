<?php

namespace ant\attribute\processors;

use ant\helpers\Ison;

class GeneralFunction extends \yii\base\Component {
    public $renderers = null;
	
	protected $_context;
	protected $_expression;
	protected $_takeABreak;

    // v1 :     protected $_pattern = '\{([^\:\}]+)\:?([^\}]*)\}';
    // v2 :     protected $_pattern = '\{([^\{\:\}]+)\:?([^\}]*\}?)\}';
    /* v3 :*/   protected $_pattern = '\{([^\{\}]+)\}';

    protected $_paramPattern = '([^\:]+)\:?(.*)';

    protected $_paramSeperator = ',';
	
	public function getParams() {
		return $this->_context->params;
	}
	
	public function getVariables() {
		return $this->_context->variables;
	}
	
	public function getObjects() {
		return $this->_context->objects;
	}
	
	public function getContext() {
		return $this->_context->getContext();
	}
	
	public function setContext($context) {
		$this->_context = $context;
	}
	
	public function setExpression($expression) {
		$this->_expression = $expression;
	}
	
	public function process()
    {
		
		$expression = $this->_expression;
		
		$indent = 0;
        do {
			$indentString = str_repeat('---', $indent	++).' ';
			$this->addLog($indentString.'Parsing expression: '.$expression);
			
            $expression = preg_replace_callback('/' . $this->_pattern . '/i', [$this, 'replace'], $expression);
        } while (preg_match('/' . $this->_pattern . '/i', $expression) && !$this->_takeABreak);

        return $expression;
    }
	
	protected function replace($matches) {
		$token = $this->parseToken($matches[1]);
		
		$renderName = $token['name'];
		$paramsString = $token['paramsString'];
		
        $renderName = str_replace(' ', '', ucwords(str_replace('-', ' ', $renderName)));
		$renderName = lcfirst($renderName);
			
        $result = $this->processToken($renderName, $paramsString);
		if (is_array($result)) throw new \Exception('Result of parsing "'.$renderName.'" is an array: '.print_r($result, 1));      
		
		return $result;
	}
	
	protected function addLog() {
			
	}
	
	protected function parseToken($token) {
		
        preg_match('/' . $this->_paramPattern . '/i', $token, $match);

        list($fullExpression, $renderName, $paramsString) = $match;
		
		return [
			'name' => $renderName,
			'paramsString' => $paramsString,
		];
	}

    //protected function
    protected function paramsStringToParams($paramsString)
    {
        if ($paramsString !== '') {

            $paramsString = '[' . $paramsString . ']';

            return Ison::decode($paramsString, true);

        } else {
            return null;
        }
    }
	
	protected function funcLookup($params) {
        list ($find, $data) = array_pad($params, 2, null);

        $find = trim($find);

        if (!isset($data[$find])) {
            if (isset($params[2])) {
                return $params[2];
            }
            throw new \Exception('"'.$find.'" is not exist in '.print_r($data,1));
        }
        
        return $data[$find];
    }
	
	protected function funcMath($params) {
		$expression = new \ant\attribute\components\AttributeExpression('='.$params[0]);
		$value = $expression->toString();	
		return is_numeric($value) ? $value : null;
	}
	
	/*
	protected function round($params) {
		$value = $params[0];
		$decimal = $params[1];
		
		if (!is_numeric($value)) throw new \Exception('"'.$value.'" is not a numeric value. ');
		
		return number_format($value, $decimal);
	}
	
	protected function priceRule($params) {
		// {priceRule:condition:[b:{context.b},height:{context.height},width:{context.width}],rules:[[rule:[b:[b80],height:[40],width:[1350,1500,1850,2100,2400,2460,2700,3000]],value:[MYR:10,USD:10,SGD:10]]],defaultValue:[MYR:10,USD:10,SGD:10]}

		$condition = $params['condition'];
		unset($params['condition']);

		$priceRule = new \common\components\PriceRule($params);
		return $priceRule->getValue($condition);
	}
	*/
	
	protected function funcCurrency($params) {
		return \Yii::$app->user->identity->getDynamicAttribute('currency');
	}
	
	protected function funcRange($params) {
		$variableValue = $params[0];
		foreach ($params[1] as $range => $returnValue) {
			list($min, $max) = explode('-', $range);

			if ($min == '*' && $variableValue < $max) {
				return $returnValue;
			} else if ($max == '*' && $variableValue >= $min) {
				return $returnValue;
			} else if ($variableValue >= $min && $variableValue < $max) {
				return $returnValue;
			}
		}
	}
	
	// minimun numeric value 
	protected function funcMin($params) {

		list($value, $min) = $params;
		//return  0;
		if (!is_numeric($value)) throw new \Exception('"'.$value.'" is not a numeric value. ');

		if (!is_numeric($min)) throw new \Exception('"'.$min.'" is not a numeric value. ');

		return ($value >= $min) ? $value : $min;
	}
	
	protected function funcEither($params) {
		foreach ($params as $value) {
			if (isset($value) && trim($value) != '') {
				return $value;
			}
		}
	}
	
	protected function processToken($name, $paramsString) {
		$renderName = $name;
		$paramsString = str_replace(' ', '', $paramsString);
		$params = $this->paramsStringToParams($paramsString);
		
		$methodName = 'func'.ucfirst($name);
		
		if ($this->hasMethod($methodName)) {
			return call_user_func_array([$this, $methodName], [$params]);
		}
		
		//throw new \Exception($renderName.' is not a valid function. ');

        //return $this->error();
	}
}