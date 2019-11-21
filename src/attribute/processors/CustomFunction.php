<?php

namespace ant\attribute\processors;

class CustomFunction extends \yii\base\Component {
    public $renderers = null;
	public $passParamsAsArray = false;
	
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
        //do {
			$indentString = str_repeat('---', $indent	++).' ';
			$this->addLog($indentString.'Parsing expression: '.$expression);
			
            $expression = preg_replace_callback('/' . $this->_pattern . '/i', [$this, 'replace'], $expression);
        //} while (preg_match('/' . $this->_pattern . '/i', $expression) && !$this->_takeABreak);

        return $expression;
    }

    protected static function decode($ison, $assoc = false)
    {
        return json_decode(static::toJson($ison), $assoc);
    }

    protected static function toJson($ison)
    {
        $pattern = '/\[([^\[^\]]+)\]/i';

        do {
            $ison = preg_replace_callback($pattern, function($input) {
                list($fullInput, $input) = $input;

                if (strpos($input, ':')) {

                    $parts = explode(',', $input);

                    foreach ($parts as $i => $part)
                    {
                        $keyOrValue =  explode(':', $part);

                        $isKey = true;

                        foreach ($keyOrValue as $j => $item)
                        {
                            if (!(strpos($item, '{') || strpos($item, '}') || strpos($item, '<') || strpos($item, '>')))
                            {
                                if (!is_numeric($item) || $isKey)
                                {
                                    $keyOrValue[$j] = '"' . $item . '"';
                                }

                                $isKey = false;
                            }
                        }

                        $parts[$i] = implode('|', $keyOrValue);
                    }

                    $output = '{' . implode(',', $parts) . '}';

                    //echo $output;die;
                } else {

                    $values = explode(',', $input);

                    foreach ($values as $j => $value)
                    {
                        if (!is_numeric($value) && !strpos($value, '{') && !strpos($value, '}') && !strpos($value, '<') && !strpos($value, '>'))
                        {
                            $values[$j] = '"' . $value . '"';
                        }
                    }

                    $input = implode(',', $values);

                    $output = '<' . $input . '>';
                }

                return str_replace(',', '&', $output);

            }, $ison);

        } while (preg_match($pattern, $ison));

        return str_replace(['<', '>', '|', '&'], ['[', ']', ':', ','], $ison);
    }
	
	protected function replace($matches) {
		$token = $this->parseToken($matches[1]);
		
		$renderName = $token['name'];
		$paramsString = $token['paramsString'];
		
        $renderName = str_replace(' ', '', ucwords(str_replace('-', ' ', $renderName)));
		$renderName = lcfirst($renderName);
			
        $result = $this->processToken($renderName, $paramsString);
		if (is_array($result)) throw new \Exception('Result of parsing "'.$renderName.'" is an array: '.print_r($result, 1));      
		
		return isset($result) ? $result : $matches[0];
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

            return self::decode($paramsString, true);

        } else {
            return null;
        }
    }
	
	protected function processToken($name, $paramsString) {
		$renderName = $name;
		$paramsString = str_replace(' ', '', $paramsString);
		$params = $this->paramsStringToParams($paramsString);
		
		if (isset($this->variables[$name]) && is_callable($this->variables[$name])) {
			//return $this->params[$name]($params);
			if ($this->passParamsAsArray) {
				return call_user_func_array($this->variables[$name], [$params, $this->variables]);
			} else {
				return call_user_func_array($this->variables[$name], $params);
			}
		}
		
		//throw new \Exception($renderName.' is not a valid function. ');

        //return $this->error();
	}
}