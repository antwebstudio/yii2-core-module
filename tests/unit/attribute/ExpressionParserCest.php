<?php
namespace attribute;
use \UnitTester;
use ant\attribute\components\ExpressionParser;

class ExpressionParserCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

	public function testPattern(UnitTester $I, $scenario) {
		$scenario->skip();
		
		$test = [
			'{testAttribute}' => [['{testAttribute}', 'testAttribute']],
			'{testAttribute2}' => [['{testAttribute2}', 'testAttribute2']],
			'{testAttribute3:test}' => [['{testAttribute3:test}', 'testAttribute3:test']],
		];
		$expr = new ExpressionParser();
		$pattern = $I->getProperty($expr, '_pattern');

		foreach ($test as $expression => $expected) {
			preg_match_all('/'.$pattern.'/i', $expression, $result, PREG_SET_ORDER);
			$I->assertEquals($expected, $result);
		}
	}

	public function testParseMath(UnitTester $I, $scenario) {
		$scenario->skip();
		
		$tests = [
			'1 + 2' => 3,
			'3 * 4' => 12,
			'( 1 + 2 ) * ( 3 + 4 )' => 21,
		];
		$expr = new ExpressionParser('');
		//$expr->setContext($object);

		foreach ($tests as $mathExpression => $expected) {
			$parsed = $I->invokeMethod($expr, 'parseMath', [$mathExpression]);
			$I->assertEquals($expected, $parsed);
		}
	}

    // tests
    public function testParse(UnitTester $I, $scenario)
    {
		$scenario->skip();
		
		$expressions = [
			'={abc} + {def}' => '123',
		];
		$expected = array_values($expressions);
		
		$parser = new ExpressionParser([
			'params' => [
				'abc' => '123',
				'def' => '123',
			],
		]);
		
		$result = [];
		foreach ($expressions as $expression => $e) {
			$result[] = $parser->parse($expression);
		}
		
		$I->assertEquals($expected, $result);
    }
	
	public function testParseToken(UnitTester $I, $scenario) {
		
		$scenario->skip();
		
		$tokens = [
			'{abc}' => ['name' => '{abc}', 'paramsString' => ''], // Simple variable
			'{abc:def}' => ['name' => '{abc}', 'paramsString' => 'def'], // Simple function
		];
		$expected = array_values($tokens);
		
		$parser = new ExpressionParser([
			'params' => [
				'abc' => '123',
				'def' => '123',
			],
		]);
		
		$result = [];
		foreach ($tokens as $expression => $e) {
			$result[] = $I->invokeMethod($parser, 'parseToken', [$expression]);
		}
		
		$I->assertEquals($expected, $result);
	}
}
