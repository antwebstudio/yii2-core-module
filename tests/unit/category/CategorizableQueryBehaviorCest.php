<?php 
namespace category;

use UnitTester;
use ant\category\models\Category;

class CategorizableQueryBehaviorCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testFilterByTwoCategoryId(UnitTester $I)
    {
		$categoryType1 = $I->grabFixture('categoryType')->getModel(0);
		$categoryType2 = $I->grabFixture('categoryType')->getModel(1);
		
		$category1 = $this->createCategory($categoryType1->id);
		$category2 = $this->createCategory($categoryType2->id);
		
		// Fulfill both categories and productCategory
		$expectedModel = new CategorizableQueryBehaviorCestModel;
		$expectedModel->categories_ids = [$category1->id];
		$expectedModel->productCategory_ids = [$category2->id];
		if (!$expectedModel->save()) throw new \Exception(print_r($expectedModel->errros, 1));
		
		// Fulfill categories only
		$model = new CategorizableQueryBehaviorCestModel;
		$model->categories_ids = [$category1->id];
		if (!$model->save()) throw new \Exception(print_r($model->errros, 1));
		
		// Fulfill productCategory only
		$model = new CategorizableQueryBehaviorCestModel;
		$model->productCategory_ids = [$category2->id];
		if (!$model->save()) throw new \Exception(print_r($model->errros, 1));
		
		
		$query = CategorizableQueryBehaviorCestModel::find()
			->filterByCategoryId([$category1->id], 'default')
			->filterByCategoryId([$category2->id], 'product');
			
		$result = $query->all();
		
		$I->assertEquals(1, count($result));
		$I->assertEquals($expectedModel->id, $result[0]->id);
    }
	
    public function testFilterByCategoryId(UnitTester $I)
    {
		$categoryType1 = $I->grabFixture('categoryType')->getModel(0);
		$categoryType2 = $I->grabFixture('categoryType')->getModel(1);
		
		$category1 = $this->createCategory($categoryType1->id);
		$category2 = $this->createCategory($categoryType2->id);
		
		// Fulfill both categories and productCategory
		$expectedModel = new CategorizableQueryBehaviorCestModel;
		$expectedModel->categories_ids = [$category1->id];
		$expectedModel->productCategory_ids = [$category2->id];
		if (!$expectedModel->save()) throw new \Exception(print_r($expectedModel->errros, 1));
		
		// Fulfill categories only
		$model = new CategorizableQueryBehaviorCestModel;
		$model->categories_ids = [$category1->id];
		if (!$model->save()) throw new \Exception(print_r($model->errros, 1));
		
		// Fulfill productCategory only
		$model = new CategorizableQueryBehaviorCestModel;
		$model->productCategory_ids = [$category2->id];
		if (!$model->save()) throw new \Exception(print_r($model->errros, 1));
		
		
		$query = CategorizableQueryBehaviorCestModel::find()
			->filterByCategoryId([$category1->id], 'default');
			
		$result = $query->all();
		
		$I->assertEquals(2, count($result));
		$I->assertEquals($expectedModel->id, $result[0]->id);
    }
	
	protected function createCategory($categoryTypeId) {
		$model = new Category(['type_id' => $categoryTypeId]);
		$model->title = 'new test category';
		
		$root = Category::ensureRoot($categoryTypeId);
		
		if (!$model->appendTo($root)) throw new \Exception(print_r($model, 1));
		
		return $model;
	}
	
	public function _fixtures() {
		return [
			'category' => [
				'class' => 'tests\fixtures\CategoryFixture',
			],
			'categoryType' => [
				'class' => 'tests\fixtures\CategoryTypeFixture',
			],
			'categoryMap' => [
				'class' => 'tests\fixtures\CategoryMapFixture',
			],
		];
	}
}

class CategorizableQueryBehaviorCestModelQuery extends \yii\db\ActiveQuery {
	public function behaviors() {
		return [
			[
				'class' => 'ant\category\behaviors\CategorizableQueryBehavior',
			],
		];
	}
}

class CategorizableQueryBehaviorCestModel extends \yii\db\ActiveRecord {
	/*public function rules() {
		return [
			[['categories_ids'], 'safe'],
			[['name'], 'default', 'value' => 'test'],
		];
	}*/
	
	public static function find() {
		return new CategorizableQueryBehaviorCestModelQuery(get_called_class());
	}
	
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function behaviors() {
		return [
			[
				'class' => 'ant\category\behaviors\CategorizableBehavior',
				'attribute' => 'categories',
			],
			[
				'class' => 'ant\category\behaviors\CategorizableBehavior',
				'attribute' => 'productCategory',
				'type' => 'product',
			],
		];
	}
	
	public function getProductCategory() {
		return $this->getCategoriesRelation('product');
	}
	
	public function getCategories() {
		return $this->getCategoriesRelation('default');
	}
}
