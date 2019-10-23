<?php

namespace ant\attribute\components;

use ant\helpers\Ison;

class ExpressionParserProcessor extends \yii\base\Component {
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
	
	protected function processToken($name, $paramsString) {
		$renderName = $name;
		$params = $this->paramsStringToParams($paramsString);

        if (strpos($renderName, '.'))
        {
            list($objectName, $property) = explode('.', $renderName);

            $objectName = lcfirst($objectName);

            if (isset($this->objects[$objectName])) {
				return ($params == null) ?
				$this->objects[$objectName]->$property :
				//call_user_func_array(array($this->objects[$name], 'get' . ucwords($property)), $params);
				$this->objects[$objectName]->{'get' . ucwords($property)}($params);
            }
			
			if (isset($this->variables[$objectName]) && is_array($this->variables[$objectName]) && isset($this->variables[$objectName][$property])) {


                return $this->variables[$objectName][$property];
			}
			
			if (isset($this->context) && is_object($this->context->{$objectName})) {
				//$this->context->{$objectName}->{$property};
				//isset($this->context->{$objectName}->{$property});
				if ($objectName == 'parent' && get_class($this->context) == 'TestClass2'	) throw new \Exception('1parent:'.get_class($this->context));	
                if (isset($this->context->{$objectName}->{$property})) {
					//throw new \Exception(get_class($this->context).$objectName.':'.$renderName);
					//if (get_class($this->context) == 'TestClass2') throw new \Exception('test:'.$property.':'.$objectName);
					
					//if ($objectName == 'parent' && get_class($this->context) == 'TestClass2'	) throw new \Exception('2parent:'.get_class($this->context));	
					//if ($objectName == 'parent' || $property == 'parent') throw new \Exception('t'.$objectName.':'.$property.':'.get_class($this->context));
                    return $this->context->{$objectName}->{$property};
				} else if ($this->context->{$objectName}->hasMethod('getDynamicAttributes') && $this->context->{$objectName}->hasDynamicAttribute($property)) {
					return $this->context->{$objectName}->getDynamicAttribute($property);
				} else if ($this->context->{$objectName}->hasExpressionAttribute($property)) {
					return $this->context->{$objectName}->getParsedAttribute($property, $this->params);
				}
			}
        }
        
        if($this->renderers instanceof self && method_exists($this->renderers, 'render' . ucfirst($renderName)))
        {
            return $this->renderers->{'render' . ucfirst($renderName)}{$params};
        }

        if(is_array($this->renderers) && isset($this->renderers[$renderName]) && is_callable($this->renderers[$renderName]))
        {
            return $this->renderers[$renderName]($params, $this->params);
        }

        if(is_array($this->renderers) && is_callable($this->renderers))
        {
            return call_user_func_array($this->renderers, [$renderName]);
        }

        if(method_exists($this, 'render' . $renderName))
        {
            return $this->{'render' . $renderName}($params, $this->params);
        }

        if (isset($this->variables[$renderName]))
        {
            if (is_callable($this->variables[$renderName])) {
                return $this->getAnonymousFunction($renderName, $params);
            } else {
                return $this->variables[$renderName];
            }
        }

        if (is_callable([$this->context, 'hasExpressionAttribute']) && is_callable([$this->context, 'getParsedAttribute'])) 
        {
            if($this->context->hasMethod('hasExpressionAttribute') && $this->context->hasExpressionAttribute($renderName)) 
            {
				return $this->context->getParsedAttribute($renderName, $this->params);
            }
        }
        
        if(isset($this->context) && isset($this->context->{$renderName})) 
        {
            return $this->context->{$renderName};
        }

        // if($this->context->hasExpressionAttribute($renderName)) 
        // {
        //     return $this->context->getParsedAttribute($renderName);
        // }
		throw new \Exception($renderName.' is not able to be parsed. ');

        return $this->error();
	}

    protected function getAnonymousFunction($name, $params = [])
    {
		if (isset($this->variables[$name]) && is_callable($this->variables[$name])) {
			return $this->variables[$name]($params, $this->params);
		}
    }
}