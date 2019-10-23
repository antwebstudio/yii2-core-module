<?php

namespace ant\attribute\components;

class MathExpressionParserProcessor extends \yii\base\Component {
	protected $_expression;
    protected $_calculateSign = '=';	
	
	public function setExpression($expression) {
		$this->_expression = $expression;
	}
	
	public function setContext() {
		
	}
	
	public function process() {
        $expression = $this->_expression;
		
		$replace = [
			'(' => ' ( ',
			')' => ' ) ',
			'+' => ' + ',
			'*' => ' * ',
			'/' => ' / ',
		];
		
        if(substr($expression, 0, 1) == $this->_calculateSign)
        {
			$expression = str_replace(array_keys($replace), array_values($replace), $expression);
            $expression = $this->parseMath(substr($expression, 1));
			$expression = str_replace(array_values($replace), array_keys($replace), $expression);
        }

        return $expression;
	}

    protected function parseMath($expression)
    {
        try {

            return (new \Math\Parser())->evaluate($expression);

        } catch (\InvalidArgumentException $e) {

            //ignore parse error
            return $expression;

        } catch (\Exception $e) {

            return $expression;

        }
    }
}