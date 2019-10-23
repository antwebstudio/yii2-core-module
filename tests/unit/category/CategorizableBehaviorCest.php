<?php
namespace category;

use \UnitTester;
use yii\helpers\ArrayHelper;
use \ant\category\models\Category;

class CategorizableBehaviorCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSave(UnitTester $I)
    {
		$categoryType = $I->grabFixture('categoryType')->getModel(0);
		
		$category = $this->createCategory($categoryType->id);
		
		$model = new CategorizableBehaviorCestModel;
		$model->attributes = ['categories_ids' => [$category->id]];
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = CategorizableBehaviorCestModel::findOne($model->id);
		
		//throw new \Exception($model->categories[0]->id);
		
		$I->assertEquals($category->id, $model->categories[0]->id);
		$I->assertEquals([$category->id], $model->categories_ids);
    }
	
    public function testSaveOnFormModel(UnitTester $I)
    {
		$categoryType = $I->grabFixture('categoryType')->getModel(0);
		
		$category = $this->createCategory($categoryType->id);
		
		$model = new CategorizableBehaviorCestFormModel;
		$model->load([
			(new CategorizableBehaviorCestModel)->formName() => ['categories_ids' => [$category->id]],
		]);
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = CategorizableBehaviorCestModel::findOne($model->sub->id);
		
		//throw new \Exception($model->categories[0]->id);
		
		$I->assertEquals($category->id, $model->categories[0]->id);
		$I->assertEquals([$category->id], $model->categories_ids);
    }
	
	public function testUpdate(UnitTester $I)
    {
		$categoryType = $I->grabFixture('categoryType')->getModel(0);
		
		$category = $this->createCategory($categoryType->id);
		
		$model = new CategorizableBehaviorCestModel;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = CategorizableBehaviorCestModel::findOne($model->id);
		$model->attributes = ['categories_ids' => [$category->id]];
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = CategorizableBehaviorCestModel::findOne($model->id);
		
		//throw new \Exception($model->categories[0]->id);
		
		$I->assertEquals($category->id, $model->categories[0]->id);
		$I->assertEquals([$category->id], $model->categories_ids);
    }
	
	public function testUpdateTwoCategoriazbleAttribute(UnitTester $I)
    {
		$categoryType = $I->grabFixture('categoryType')->getModel(0);
		
		$anotherCategoryType = $I->grabFixture('categoryType')->getModel(1);
		
		$category = $this->createCategory($categoryType->id);
		$anotherCategory = $this->createCategory($anotherCategoryType->id);
		
		$model = new ExtendedCategorizableBehaviorCestModel;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = ExtendedCategorizableBehaviorCestModel::findOne($model->id);
		$model->attributes = [
			'categories_ids' => [$category->id],
			'anotherCategories_ids' => [$anotherCategory->id],
		];
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		//throw new \Exception($I->renderDbTable('{{%category_map}}'));
		
		//throw new \Exception(print_r($model->categories_ids, 1));
		
		$model = ExtendedCategorizableBehaviorCestModel::findOne($model->id);
		
		//throw new \Exception($I->renderDbTable('{{%category_map}}'));
		
		$I->assertEquals(1, count($model->categories));
		$I->assertEquals(1, count($model->categories_ids));
		$I->assertEquals($category->id, $model->categories[0]->id);
		$I->assertEquals([$category->id], $model->categories_ids);
		
		$I->assertEquals(1, count($model->anotherCategories));
		$I->assertEquals(1, count($model->anotherCategories_ids));
		$I->assertEquals($anotherCategory->id, $model->anotherCategories[0]->id);
		$I->assertEquals([$anotherCategory->id], $model->anotherCategories_ids);
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
		];
	}
}

class CategorizableBehaviorCestFormModel extends \common\base\FormModel {
	public function models() {
		return [
			'sub' => [
				'class' => CategorizableBehaviorCestModel::class,
			],
		];
	}
}

class CategorizableBehaviorCestModel extends \yii\db\ActiveRecord {
	public function rules() {
		return [
			[['categories_ids'], 'safe'],
			[['name'], 'default', 'value' => 'test'],
		];
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
		];
	}
	
	public function getCategories() {
		return $this->getCategoriesRelation();
	}
}

class ExtendedCategorizableBehaviorCestModel extends CategorizableBehaviorCestModel {
	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[['anotherCategories_ids'], 'safe'],
		]);
	}
	
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => 'ant\category\behaviors\CategorizableBehavior',
				'attribute' => 'anotherCategories',
				'type' => 'anotherCategories',
			],
		]);
	}
	
	public function getAnotherCategories() {
		return $this->getCategoriesRelation('anotherCategories');
	}
}
