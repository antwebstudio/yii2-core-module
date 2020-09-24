<?php 
namespace ant\category\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use ant\category\models\Category;
use ant\category\models\CategoryMap;
use ant\category\models\CategoryType;

class CategorizableBehavior extends Behavior 
{
  public $attribute = null; // relation = this
  public $modelScenario = 'default';
  public $categoryType = null;

  public $modelClassId;
  public $type = CategoryType::DEFAULT_NAME;

  //not using
  public $linkModelExtraAttributes = [];
  
  protected static $linkerRelations = [];
  
  public function __call($name, $params) {
    $attribute = preg_replace('/^get(.*)/isU', '', $name);
	  if ($this->isCategoryTypeAttribute($attribute)) {
			return $this->getCategoriesRelation($attribute);
	  }
	  return parent::__call($name, $params);
  }
  
  public function hasMethod($name) {
    $attribute = preg_replace('/^get(.*)/isU', '', $name);
    return $this->isCategoryTypeAttribute($attribute) || parent::hasMethod($name);
  }
  
	public function canGetProperty($name, $checkVars = true) {
		if ($this->isCategoryTypeAttribute($name)) {
			return true;
		}
		return parent::canGetProperty($name, $checkVars);
	}

	public function __get($name) {
		if ($this->isCategoryTypeAttribute($name)) {
			return $this->getCategoriesRelation($name);
		}
		return parent::__get($name);
	}
  
  protected function isCategoryTypeAttribute($attribute) {
		if (is_array($this->type)) {
			return in_array($attribute, $this->type);
		} else if (isset($this->attribute) && $attribute == $this->attribute) {
			return true;
		} else if ($attribute == $this->type) {
			return true;
		}
		return false;
  }
  
  public function attach($owner) {
	    if (is_array($this->type) && isset($this->attribute)) {
			throw new \Exception('Configuration of CategorizableBehavior is invalid. Please don\'t set attribute when type property is an array. ');
		}
		if (is_array($this->type)) {
			foreach ($this->type as $name) {
				$type = $name;
				
				$relations[$name.'_ids'] = [$name, 'updater' => [
					'viaTableAttributesValue' => [
						'model_class_id' => \ant\models\ModelClass::getClassId((get_class($owner))),
						'category_type_id' => CategoryType::getIdFor($type),
					],
					
					'viaTableCondition' => [
						'category_type_id' => CategoryType::getIdFor($type),
					],
				]];
			}
		} else {
			$name = $this->attribute;
			$type = $this->type;
			
			$relations[$name.'_ids'] = [$name, 'updater' => [
				'viaTableAttributesValue' => [
					'model_class_id' => \ant\models\ModelClass::getClassId((get_class($owner))),
					'category_type_id' => CategoryType::getIdFor($type),
				],
				
				'viaTableCondition' => [
					'category_type_id' => CategoryType::getIdFor($type),
				],
			]];
		}
		
		$owner->attachBehaviors([
			[
				'class' => \voskobovich\linker\LinkerBehavior::className(),
				'relations' => $relations,
			]
		]);
		return parent::attach($owner);
  }

  public function getTypeId() {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
	  return CategoryType::getIdFor($this->type);
  }
  
  public function getCategoryMap() {
	  return $this->owner->hasMany(CategoryMap::className(), ['model_id' => 'id']);
  }
  
  public function getCategoriesRelation($categoryType = CategoryType::DEFAULT_NAME) {
	$query = $this->owner->hasMany(Category::className(), ['id' => 'category_id'])
		->cache(7200)
		->viaTable('{{%category_map}}', ['model_id' => 'id'], function ($query) use ($categoryType) {
			$conditions = [
        'or', [
          '{{%category_map}}.model_class_id' => \ant\models\ModelClass::getClassId(get_class($this->owner)),
        ],
        [
          '{{%category_map}}.model_class_id' => null,
        ],
			];
			
			$query->andWhere($conditions);
			
			if ($categoryType != '*' && isset($categoryType)) {
				$query->andWhere([
          'or', [
            '{{%category_map}}.category_type_id' => CategoryType::getIdFor($categoryType),
          ],
          [
            '{{%category_map}}.category_type_id' => null,
          ],
        ]);
			}
		});
		
	/*if (isset($categoryType)) {
		$query->typeOf($categoryType);
	}*/
	
	return $query;
  }

  public function getAttribute(){
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    if ($this->attribute) {
      return $this->attribute;
    } else {
      throw new \Exception("Set model className to attribute", 1);
    }
  }

  /*public function events()
  {
      return [
          //ActiveRecord::EVENT_AFTER_FIND => 'afterFindMultiple',
          //ActiveRecord::EVENT_AFTER_INSERT => 'afterInsertMultiple',
          //ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdateMultiple',
          //ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDeleteMultiple',
          //ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
      ];

      return $multipleEvents;
  }*/

  public function afterInsertMultiple()
  {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    if ($this->owner->{$this->attribute}) {
          $this->saveModelsToRelation($this->owner->{$this->attribute});
      }
  }

  /*public function getCategory($categoryId)
  {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    $query=$this->owner->hasMany(Category::className(), ['id' => 'category_id'])
      ->viaTable('{{%category_map}} category_map', ['model_id' => 'id'], function ($query) {
       $query->andWhere([
          'category_map.model_id' => $this->owner->id,
          'category_map.category_id' => $categoryId,
          'cetegory_map.model_class_id' => $this->getTypeId()
        ]);
    });
    if ($this->categoryType) {
      $query->onCondition(['type' => $this->categoryType]);
    }
    return $query;
  }*/

  public function haveCategory($id)
  {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    $relationQuery = $this->getCategory($id);
    if (isset($relationQuery)) {
      return $relationQuery->andWhere(['id' => $id])->one() == null ? false : true ;
    }
  }

  public function afterFindMultiple() {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    /*$models = $this->getRelation()->all();
    $data = [];
    foreach ($models as $k => $model) {
      $data[] = $model->id;
    }
    $this->owner->{$this->attribute} = $data;*/
  }

  protected function getRelation() {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    if (!isset($this->attribute)) throw new \Exception('Property "attribute" for '.get_class().' is not set or invalid. ');
    
    $getter = 'get'.ucfirst($this->attribute);
    return $this->owner->{$getter}();
  }

  public function getString() {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
   $models = $this->getRelation()->all();
   $string = '';
   foreach ($models as $key => $category) {
       if ($key == 0) {
           $string = $category->slug;
       } else {
          $string .= ', ' . $category->slug;
        }
   }
   return $string == '' ? null : $string;
  }

  public function afterUpdateMultiple() {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    /*$newIds = $this->owner->{$this->attribute};
    $models = $this->getRelation()->all();
    $modelIds = ArrayHelper::getColumn($models, 'id');

    $newModels = $updatedModels = [];

    if (is_array($newIds)) {
      $deletedIds = array_diff($modelIds, $newIds);
      $this->deleteModelRelation($deletedIds);
      
      $newId = array_diff($newIds, $modelIds);
      $this->saveModelsToRelation($newId);
    } else {
      if ($modelIds != $newIds) {
        $this->deleteModelRelation($modelIds);
        $this->saveModelstoRelation($newIds);
      } else {
        $this->saveModelsToRelation($newIds);
      }
    }*/
  }

  public function deleteModelRelation($deletedIds) {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    CategoryMap::deleteAll(['category_id' => $deletedIds, 'model_id' => $this->owner->id]);
  }

  protected function getGroup($categoryId) {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    $group = $this->owner->hasMany(CategoryMap::className(), ['model_id' => 'id'])
    ->onCondition([
      'category_id' => $categoryId,
      'model_class_id' => $this->getTypeId(),
    ])->one();
    if (!$group) {
      $group = new CategoryMap;
      $group->model_id = $this->owner->id;
      $group->category_id = $categoryId;
      $group->model_class_id = $this->getTypeId();
    }
    $group->scenario = $this->modelScenario;
    return $group;
  }

  protected function saveToMap($categoryId){
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    $group = $this->getGroup($categoryId);
    $group->save();
  }

  protected function saveModelsToRelation($categoryIds)
  {
	  if (YII_DEBUG) throw new \Exception('DEPRECATED');
    if (is_array($categoryIds)) {
      foreach ($categoryIds as $key => $categoryId) {
        $this->saveToMap($categoryId);
      }
    } else {
      $this->saveToMap($categoryIds);
    }
  }
}
