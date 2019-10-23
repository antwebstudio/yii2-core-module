<?php
namespace attribute\processors;
use \UnitTester;

use ant\attribute\processors\CustomFunction;
use ant\attribute\components\ExpressionParserContext;

class CustomFunctionCest
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
			'{custom: abc}' => ['{custom: abc}'], // Return the original string when the function is not exist.
			'{custom: abc}' => ['cba', 'params' => [
				'custom' => function($string) {
					return strrev($string);
				},
			]],
		];
		
		$expected = [];
		$result = [];
		foreach ($test as $expression => $setting) {
			$expected[] = $setting[0];
			$params = isset($setting['params']) ? $setting['params'] : [];
			
			$context = new ExpressionParserContext;
			$context->setParams($params);
			
			$processor = new CustomFunction;
			$processor->setExpression($expression);
			$processor->setContext($context);			
			
			$result[] = $processor->process();
		}
		
		$I->assertEquals($expected, $result);
    }
}
