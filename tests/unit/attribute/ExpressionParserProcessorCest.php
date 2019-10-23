<?php
namespace attribute;

use \UnitTester;
use ant\attribute\components\ExpressionParserProcessor;
use ant\attribute\components\ExpressionParserContext;

class ExpressionParserProcessorCest
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
		];
		
		$expected = [];
		$result = [];
		foreach ($test as $expression => $setting) {
			$expected[] = $setting[0];
			$params = isset($setting['params']) ? $setting['params'] : [];
			
			$context = new ExpressionParserContext;
			$context->setParams($params);
			
			$processor = new ExpressionParserProcessor;
			$processor->setExpression($expression);
			$processor->setContext($context);			
			
			$result[] = $processor->process();
		}
		
		$I->assertEquals($expected, $result);
    }
	
	public function testProcessWithoutContext(UnitTester $I, $scenario)
    {
		$scenario->skip();
		
		$test = [
			'={test}' => ['value', 'params' => ['test' => 'value']],
		];
		
		$expected = [];
		$result = [];
		foreach ($test as $expression => $setting) {
			$expected[] = $setting[0];
			
			$processor = new ExpressionParserProcessor;
			$processor->setExpression($expression);
			
			$result[] = $processor->process();
		}
		
		$I->assertEquals($expected, $result);
    }
}
