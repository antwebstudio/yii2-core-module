<?php
use yii\helpers\ArrayHelper;
use ant\behaviors\AttachBehaviorBehavior;
use ant\behaviors\ExpressionAttribute;
use ant\attribute\components\AttributeExpression;

class OldAttributeExpressionCest
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

	// public function testParseFunctionParams(UnitTester $I) {
	// 	$tests = [
	// 		'*, 2' => ['*', '2'],
	// 		'currency,[MYR:2, SGD:3, USD:4]' => ['currency', ['MYR' => 2, 'SGD' => 3, 'USD' => 4]],
	// 		// @TODO: make this unit test pass
	// 		//'currency, test, [MYR:2, SGD:3, USD:4]' => ['currency', 'test', ['MYR' => 2, 'SGD' => 3, 'USD' => 4]],
	// 	];
	// 	$expr = new AttributeExpression('');
	// 	//$expr->setContext($object);

	// 	foreach ($tests as $paramString => $expected) {
	// 		$parsed = $I->invokeMethod($expr, 'parseFunctionParams', [$paramString]);
	// 		$I->assertEquals($expected, $parsed);
	// 	}
	// }

	public function testExpression(UnitTester $I) {
		$object = new TestClass;

		$test = [
			'123' => ['value' => '123'],
			'=123' => ['value' => '123'],
			'=3*5' => ['value' => '15'],
			'{test}' => ['value' => '', 'exception' => new \Exception('test is not able to be parsed. ')],
			'={test}' => ['value' => '123', 'params' => ['test' => '123']],
			'={object.value}' => ['value' => 'testValue', 'params' => ['object' => $object]],
			'={array.value}' => ['value' => 'testValue', 'params' => ['array' => ['value' => 'testValue']]],
			'={lookup:{currency},[MYR:2, SGD:3, USD:4]}' => ['value' => 3, 'params' => ['currency' => 'SGD']],
			'={range:{value}, [5-10:3, 10-20:4]}' => ['value' => 4, 'params' => ['value' => 10]],
			'={range:{value}, [5-10:3, 10-20:4, 20-*:5]}' => ['value' => 5, 'params' => ['value' => 90]],
			'= {math: {unitWeight} * {pricePerWeight}}' => ['value' => 10, 'params' => ['unitWeight' => 5, 'pricePerWeight' => 2]],
		];

		foreach ($test as $expression => $setting) {
			try {
				$expected = $setting['value'];
				$expr = new AttributeExpression($expression, isset($setting['params']) ? $setting['params'] : []);
				$I->assertEquals($expected, $expr->toString());
			} catch (\Exception $ex) {
				//expected exception
				if (isset($setting['exception'])) {
					$I->assertEquals($setting['exception'], $ex);
				} else {
					throw $ex;
				}
			}
		}
	}
	
	public function testContext(UnitTester $I) {
		$object = new TestClass;

		$test = [
			'={value}' => ['value' => 'testValue'],
		];

		foreach ($test as $expression => $setting) {
			$expected = $setting['value'];
			$expr = new AttributeExpression($expression, isset($setting['params']) ? $setting['params'] : []);
			$expr->setContext($object);
			$I->assertEquals($expected, $expr->toString());
		}
	}
	
	public function testContextWithDynamicExpression(UnitTester $I) {
		AttachBehaviorBehavior::attachTo(TestClass::class, [
			'dynamicAttribute' => [
				'class' => \ant\behaviors\DynamicAttribute::className(),
				'attributes' => ['width', 'length'],
			],
		]);
		
		$expected = 200;
		$expression = '= {width}';

		$object = new TestClass;
		$object->width = $expected;
		
		$expr = new AttributeExpression($expression);
		$expr->setContext($object);

		$I->assertEquals($expected, $expr->toString());
	}
}
/*
class TestModel extends \yii\base\Model {
	public $testAttribute;
}*/

class TestClass extends \yii\base\Model implements \ant\interfaces\GetterSetterTraitInterface {
	use \ant\traits\GetterSetterTrait;
	
    public function getterOverride($name, $ex)
    {
        if ($this->hasMethod('hasDynamicAttribute') && $this->hasDynamicAttribute($name)) 
        {
            return $this->getDynamicAttribute($name);
        }

        throw $ex;
    }

    public function setterOverride($name, $value, $ex)
    {
        if ($this->hasMethod('hasDynamicAttribute') && $this->hasDynamicAttribute($name)) 
        {
            return $this->setDynamicAttribute($name, $value);
        }
        
        throw $ex;
	}
	
	public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => \ant\behaviors\AttachBehaviorBehavior::className(),
                'config' => '@common/config/behaviors.php',
            ],
			'dynamicAttribute' => [
				'class' => \ant\behaviors\DynamicAttribute::className(),
				'attributes' => ['width', 'length'],
			],
        ]);
	}

	public function getValue() {
		return 'testValue';
	}

	public function __toString() {
		return 'toString';
	}

	public function getTestAttribute2() {
		return 'testValue2';
	}
}
