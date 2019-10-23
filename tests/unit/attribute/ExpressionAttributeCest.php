<?php
use ant\attribute\behaviors\ExpressionAttribute;

class ExpressionAttributeCest
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

    // tests
	
	// Getter of class should be higher priority than parsed attribute
	// public function testPriority(UnitTester $I) {
	// 	$model = new TestClass2;
		
	// 	$model->attachBehavior('expressionAttribute', [
	// 		'class' => ExpressionAttribute::className(),
	// 		'expressions' => [
	// 			'testAttribute2' => 'fromParsedAttribute',
	// 		],
	// 	]);
		
	// 	$I->assertEquals('fromParsedAttribute', $model->testAttribute2);
	// }

    // tests
    public function testBehavior(UnitTester $I)
    {
		//$model = new \yii\base\Model;
		$model = new TestClass2;
		// $json = json_encode([
		// 	'class' => TestClass::className(),
		// ]);
		$model->attachBehavior('expressionAttribute', [
			'class' => ExpressionAttribute::className(),
			'expressions' => [
				//'testAttribute' => '=json:'.$json,
				//'testAttribute2' => '={testAttribute2}',
				'testObjectGetter' => '={object.string}',
				//'testMultiAttribute' => '={testAttribute}', // Use sub
			],
		]);

		//$I->assertEquals('testValue', $model->getParsedAttribute('testAttribute'));
		//$I->assertEquals('testValue2', $model->getParsedAttribute('testAttribute2'));
		$I->assertEquals('testValue', $model->getParsedAttribute('testObjectGetter'));
		//$I->assertEquals('testValue', $model->getParsedAttribute('testMultiAttribute'));
    }

	public function testExtendedBehavior(UnitTester $I) 
	{
		$model = new TestClass2;

		$model->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'render' => 'renderValue',
			],
		]);
	}

	public function testPassParams(UnitTester $I) {
		$defaultParams = [
			'thickness' => '25',
			'width' => '550',
			'length' => '165',
			'qty' => '100',
		];
		$model = new TestClass2;

		$model->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'unitWeight' => '={thickness} * ( {width} + 3 ) * ( {length} + 3 ) * 1.2 * 1.12 / 1000000',
				'price' => '={unitWeight} * 22',
				'fullShtWeight' => '={lookup:{thickness},[25:89.3]}',
				'weightPercent' => '={totalWeight} / {fullShtWeight} * 100',
				'totalWeight' => '={unitWeight} * {qty}',
				'discount' => '{range:{weightPercent},[0-5:0,5-10:3,10-20:4,20-30:5,30-40:8,40-60:10,60-*:15]}',
			],
			'params' => [
				'lookup' => function($find, $data) {
					return $data[$find];
				},
				'range' => function($variableValue, $rangeValues) {
					foreach ($rangeValues as $range => $returnValue) {
						list($min, $max) = explode('-', $range);

						if ($min == '*' && $variableValue < $max) {
							return $returnValue;
						} else if ($max == '*' && $variableValue >= $min) {
							return $returnValue;
						} else if ($variableValue >= $min && $variableValue < $max) {
							return $returnValue;
						}
					}
				}
			],
		]);

		$I->assertEquals(3.1215744, $model->getParsedAttribute('unitWeight', $defaultParams));
		$I->assertEquals(312.15744, $model->getParsedAttribute('totalWeight', $defaultParams));
		$I->assertEquals(349.5604031355, $model->getParsedAttribute('weightPercent', $defaultParams));
		$I->assertEquals(15, $model->getParsedAttribute('discount', $defaultParams));
		
		$params = $defaultParams;
		$params['qty'] = 10;

		$I->assertEquals(3.1215744, $model->getParsedAttribute('unitWeight', $params));
		$I->assertEquals(31.215744, $model->getParsedAttribute('totalWeight', $params));
		$I->assertEquals(34.95604031355, $model->getParsedAttribute('weightPercent', $params));
		$I->assertEquals(8, $model->getParsedAttribute('discount', $params));
		
		$params['thickness'] = 8;
		$I->assertEquals(9.98903808, $model->getParsedAttribute('totalWeight', $params));
	}
	
	public function testPassParams2(UnitTester $I) {
		$params = [
			'thickness' => '4',
		];
		$model = new TestClass2;

		$model->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'thickness' => 25,
				'thickness2' => '={thickness}',
				'thickness3' => '={thickness2}',
			],
		]);

		$I->assertEquals($params['thickness'], $model->getParsedAttribute('thickness', $params));
		$I->assertEquals($params['thickness'], $model->getParsedAttribute('thickness2', $params));
		$I->assertEquals($params['thickness'], $model->getParsedAttribute('thickness3', $params));
	}
	
	// Get value from expression attribute of another object (parent)
	public function testObjectParsedAttribute(UnitTester $I) {
		$child = new TestClass3;
		$parent = new TestClass2;

		$child->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute' => '={parent.testAttribute3}',
			],
		]);
		
		$parent->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute3' => '={object.value}',
			],
		]);
		
		$child->setParent($parent);

		$I->assertEquals(5, $child->getParsedAttribute('testAttribute'));
	}
	
	// Get value from params passed down from another object (parent)
	public function testObjectParsedAttributeWithParentCustomParams(UnitTester $I) {
		$child = new TestClass3;
		$parent = new TestClass2;

		$child->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute' => '={parent.testAttribute3}',
			],
		]);
		
		$parent->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute3' => '={object.value} * {custom}',
			],
		]);
		
		$child->setParent($parent);

		$I->assertEquals(10, $child->getParsedAttribute('testAttribute', ['custom' => 2]));
	}
	
	public function testObjectParsedAttributeWithCustomFunctionWithParentCustomParams(UnitTester $I) {
		Yii::$app->expressionParser->processors = [
			\ant\attribute\processors\SimpleVariableProcessor::class,
			[
				'class' => \ant\attribute\processors\CustomFunction::class,
				'passParamsAsArray' => true,
			],
			\ant\attribute\processors\GeneralFunction::class,
			\ant\attribute\processors\MathExpression::class,
		];
		
		$child = new TestClass3;
		$parent = new TestClass2;

		$child->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute' => '={customFunc: xxx}',
			],
			'params' => [
				'customFunc' => function($funcParams, $params = []) {
					//throw new \Exception('customFunc: '.print_r($params,1));
					return $params['custom'] * 5;
				}
			],
		]);

		$I->assertEquals(10, $child->getParsedAttribute('testAttribute', ['custom' => 2]));
	}
	
	public function testObjectParsedAttributeWithParamsSameNameAsFunction(UnitTester $I, $scenario) {
		$scenario->skip(); // Currenct use different name first
		
		$child = new TestClass3;
		$parent = new TestClass2;

		$child->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute' => '={custom: xxx}',
			],
			'params' => [
				'custom' => function($funcParams, $params = []) {
					return 10;
				}
			],
		]);

		$I->assertEquals(10, $child->getParsedAttribute('testAttribute', ['custom' => 2]));
	}
	
	// Get value from expression attribute of another object (parent)
	public function testContext(UnitTester $I) {
		$child = new TestClass3;
		$parent = new TestClass2;

		$child->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute' => '{lookup:{parent.value}}',
			],
			'params' => [
				'lookup' => function($params) {
					return $params;
				}
			],
		]);
		
		$parent->attachBehavior('expressionAttribute', [
			'class' => ExtendedExpressionAttribute::className(),
			'expressions' => [
				'testAttribute3' => '= 1 + {child:testAttribute}',
			],
			'params' => [
				'child' => function($attribute) use($parent, $child) {
					$child->setParent($parent);
					return $child->getParsedAttribute($attribute);
				}
			],
		]);

		$I->assertEquals(6, $parent->getParsedAttribute('testAttribute3'));
	}
}

class ExtendedExpressionAttribute extends ExpressionAttribute {
	protected function renderRender() {
		return 'renderValue';
	}
}

class TestClass3 extends \yii\base\Model {
	protected $_parent;
	
	public function setParent($value) {
		$this->_parent = $value;
	}
	
	public function getParent() {
		return $this->_parent;
	}
}

class TestClass2 extends \yii\base\Model implements \ant\interfaces\GetterSetterTraitInterface{

	use \ant\traits\GetterSetterTrait;

	public function getterOverride($name, $ex)
    {
        if ($this->hasExpressionAttribute($name)) return $this->getParsedAttribute($name);

        throw $ex;
    }

    public function setterOverride($name, $value, $ex)
    {
        if ($this->hasExpressionAttribute($name)) {

            $this->setParsedAttribute($name, $value);

        } else {

            throw $ex;

        }
    }

	public function getValue() {
		return 5;
	}
	
	public function getObject() {
		return new TestClass2;
	}
	
	public function getString() {
		return 'testValue';
	}

	public function __toString() {
		return 'toString';
	}

	public function getTestAttribute2() {
		return 'testValue2';
	}
}
