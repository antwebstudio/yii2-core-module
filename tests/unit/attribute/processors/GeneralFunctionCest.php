<?php
namespace attribute\processors;
use \UnitTester;

use ant\attribute\processors\GeneralFunction;
use ant\attribute\components\ExpressionParserContext;

class GeneralFunctionCest
{
    public function _before(UnitTester $I)
    {
		\Yii::configure(\Yii::$app, [
			'components' => [
				'expressionParser' => [
					'class' => 'ant\attribute\components\ExpressionParser',
				],
			],
		]);
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testProcess(UnitTester $I)
    {
		$test = [
			'{lookup:6,[6:10]}' => ['10'],
			'{lookup:6,[ 6:10]}' => ['10'],
			'{lookup: 6,[6:10]}' => ['10'],
			'{lookup: 6,[6:10]} ' => ['10 '], // Space outside should be remain
			'{math:1+1}' => ['2'],
			'{math: 2 * 5}' => ['10'],
		];
		
		$expected = [];
		$result = [];
		foreach ($test as $expression => $setting) {
			$expected[] = $setting[0];
			$params = isset($setting['params']) ? $setting['params'] : [];
			
			$context = new ExpressionParserContext;
			$context->setParams($params);
			
			$processor = new GeneralFunction;
			$processor->setExpression($expression);
			$processor->setContext($context);			
			
			$result[] = $processor->process();
		}
		
		$I->assertEquals($expected, $result);
    }
}
