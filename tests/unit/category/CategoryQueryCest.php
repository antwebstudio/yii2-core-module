<?php
//namespace tests\codeception\common\category;
//use tests\codeception\common\UnitTester;
use ant\category\models\Category;
use ant\category\models\CategoryType;

class CategoryQueryCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
	public function testChildrenOf(UnitTester $I) {
		$type = 'testEnsureRoot';
		
		$root = Category::ensureRoot($type);
		
		// Create first level of children
		$category = $this->newCategory($type);
		$category->appendTo($root); // Children A
		$this->newCategory($type)->appendTo($root); // Children B
		
		// Create second level of children
		$this->newCategory($type)->appendTo($category); // Children C - Child of Children A
		
		$I->assertEquals(4, Category::find()->typeOf($type)->count()); // A, B, C
		$I->assertEquals(1, Category::find()->typeOf($type)->childrenOf($category->id)->count()); // C only
	}
	
	protected function newCategory($type, $title = 'test category') {
		$model = new Category(['type_id' => CategoryType::getIdFor($type)]);
		$model->title = $title;
		$model->slug = uniqid();
		
		return $model;
	}
}
