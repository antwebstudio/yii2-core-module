<?php
//namespace tests\codeception\common;

use yii\base\Component;

//use tests\codeception\common\UnitTester;
use ant\attribute\components\AttributeExpression;
use ant\ecommerce\models\Product;

class OldExpressionCest
{
    public function _before(UnitTester $I)
    {
		Yii::configure(Yii::$app, [
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

    //tests
    public function testStringAndSpacesExpression(UnitTester $I)
    {
        $tests =
        [
            'test'                          => 'test',
            123                             => 123,
            '   spaces  test'               => '   spaces  test',
            'end with   spaces  test    '   => 'end with   spaces  test    ',
        ];

        foreach ($tests as $expression => $expectedResult)
        {
            $result = (new AttributeExpression($expression))->toString();

            $I->assertEquals($expectedResult, $result);
        }
    }

    public function testCalculationAttributeExpression(UnitTester $I)
    {
        $tests =
        [
            '=1 + 1'                    => '2',
            '=2 / 3'                    => '0.66666666666667',
            '=( 4 + 4 ) / 2 + 2'        => '6',
            '= ( 2.4 / 2 ) + 1.56 * 2'  => '4.32',
            '1 + 1'                     => '1 + 1',
            '=a + a'                    => 'a + a',
			'=(1+1)'					=> '2',
        ];

        foreach ($tests as $expression => $expectedResult)
        {
            $result = (new AttributeExpression($expression))->toString();

            $I->assertEquals($expectedResult, $result);
        }
    }
	
	public function testAttributeExpressionWithFunction(UnitTester $I, $scenario)
    {
		$scenario->skip();
		
        $tests =
        [
            '{sum:1,2,3,4}' => [
                'params' => ['sum' => function($params) {
                    return array_sum($params);
                }],
                'expectedResult' => '10'
            ],
            'My name is {john.name:max}' => [
                'params' => ['john' => [
                    'class' => AttributeExpressionTestClass::className(),
                    'name' => 'john'
                ]],
                'expectedResult' => 'My name is max'
            ],
        ];

        foreach ($tests as $expression => $test)
        {
            try {
                $params = $test['params'];
                $expectedResult = $test['expectedResult'];
                $result = (new AttributeExpression($expression, $params))->toString();

                $I->assertEquals($expectedResult, $result);
            } catch (\Exception $ex) {
                //expected exception
                if (isset($test['expectedException'])) {
                    $I->assertEquals($test['expectedException'], $ex);
                } else {
                    throw $ex;
                }
            }
        }
    }

    public function testAttributeExpressionWithVariableAndParams(UnitTester $I)
    {
        $tests =
        [
            '{test}' => [
                'params' => ['test' => 123],
                'expectedResult' => '123'
            ],
            '{firstname} {lastname}' => [
                'params' => ['firstname' => 'Mlax', 'lastname' => 'Wong'],
                'expectedResult' => 'Mlax Wong'
            ],
            'param variable {notFound} example' => [
                'params' => [],
                'expectedResult' => 'param variable  example',
                'expectedException' => new \Exception('notFound is not able to be parsed. '),
            ],
        ];

        foreach ($tests as $expression => $test)
        {
            try {
                $params = $test['params'];
                $expectedResult = $test['expectedResult'];
                $result = (new AttributeExpression($expression, $params))->toString();

                $I->assertEquals($expectedResult, $result);
            } catch (\Exception $ex) {
                //expected exception
                if (isset($test['expectedException'])) {
                    $I->assertEquals($test['expectedException'], $ex);
                } else {
                    throw $ex;
                }
            }
        }
    }

    public function testAttributeExpressionWtihObjectAndParams(UnitTester $I)
    {
        $obj = new AttributeExpressionTestClass();
        $obj->name = 'Pre-created Object';

        $tests =
        [
            '{test.name}' => [
                'params' => ['test' => [
                    'class' => AttributeExpressionTestClass::className(),
                    'name' => 'testing'
                ]],
                'expectedResult' => 'testing'
            ],
            '{obj.name}' => [
                'params' => ['obj' => $obj],
                'expectedResult' => 'Pre-created Object'
            ],
        ];

        foreach ($tests as $expression => $test)
        {
            $params = $test['params'];
            $expectedResult = $test['expectedResult'];
            $result = (new AttributeExpression($expression, $params))->toString();

            $I->assertEquals($expectedResult, $result);
        }
    }
}

class AttributeExpressionTestClass extends Component
{
    public $_name;

    public function getName($params = false)
    {
        return $params ? $params[0] : $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }
}
