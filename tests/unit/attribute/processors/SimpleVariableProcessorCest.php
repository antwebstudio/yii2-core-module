<?php
namespace attribute\processors;
use \UnitTester;

use ant\attribute\processors\SimpleVariableProcessor;
use ant\attribute\components\ExpressionParserContext;

class SimpleVariableProcessorCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testProcess(UnitTester $I)
    {
		$test = [
			'={test}' => ['=value', 'params' => ['test' => 'value']],
			'={test}' => ['=value2', 'params' => ['test' => '{value}', 'value' => 'value2']],
			'={test:a}' => ['={test:a}', 'params' => ['test' => 'value']],
		];
		
		$expected = [];
		$result = [];
		foreach ($test as $expression => $setting) {
			$expected[] = $setting[0];
			$params = isset($setting['params']) ? $setting['params'] : [];
			
			$context = new ExpressionParserContext;
			$context->setParams($params);
			
			$processor = new SimpleVariableProcessor;
			$processor->setExpression($expression);
			$processor->setContext($context);			
			
			$result[] = $processor->process();
		}
		
		$I->assertEquals($expected, $result);
    }
}
