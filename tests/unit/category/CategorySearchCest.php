<?php

use ant\category\models\Category;
use ant\category\models\CategorySearch;

class CategorySearchCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSearch(UnitTester $I)
    {
		$nullType = Category::find()->andWhere(['type_id' => null]);
		$normalType = Category::find()->andWhere(['type_id' => 1]);
		
		$search = new CategorySearch(['type_id' => null]);
		$dataProvider = $search->search([]);
		
		$I->assertEquals($nullType->count(), $dataProvider->totalCount);
		
		$search = new CategorySearch(['type_id' => 1]);
		$dataProvider = $search->search([]);
		
		$I->assertEquals($normalType->count(), $dataProvider->totalCount);
		
		$categoryType = $I->grabFixture('categoryType')->getModel('empty');
		
		$search = new CategorySearch(['type_id' => $categoryType->id]);
		$dataProvider = $search->search([]);
		
		$I->assertEquals(0, $dataProvider->totalCount);
    }
	
	public function _fixtures() {
		return [
			'category' => [
				'class' => 'tests\fixtures\CategoryFixture',
			],
			'categoryType' => [
				'class' => 'tests\fixtures\CategoryTypeFixture',
			],
		];
	}
}
