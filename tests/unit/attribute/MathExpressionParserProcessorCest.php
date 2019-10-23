<?php
namespace attribute;
use \UnitTester;
use ant\attribute\components\MathExpressionParserProcessor;

class MathExpressionParserProcessorCest
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
		$expressions = [
			'=( 1 + 2)' => '3',
			'= ( abc ) ' => ' ( abc ) ',
			'=(1+2)' => '3',
            '1 + 1' => '1 + 1',
		];
		$expected = array_values($expressions);
		
		$result = [];
		foreach ($expressions as $expression => $e) {
			$processor = new MathExpressionParserProcessor;
			$processor->setExpression($expression);
			
			$result[] = $processor->process();
		}
		
		$I->assertEquals($expected, $result);
    }
}
