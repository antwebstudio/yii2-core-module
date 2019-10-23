<?php 
namespace ant\category\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use ant\category\models\Category;
use ant\category\models\CategoryMap;
use ant\category\models\CategoryType;

class CategorizableQueryBehavior extends Behavior 
{
    public function filterByCategoryId($id, $categoryType = null){
        if ($id) {
			$alias = isset($categoryType) ? 'map_'.$categoryType : 'map';
			
            $query = $this->owner->joinWith('categoryMap '.$alias);
			if (isset($categoryType)) {
				$query->andFilterWhere([$alias.'.category_type_id' => CategoryType::getIdFor($categoryType)]);
			}
            return $query->andFilterWhere([$alias.'.category_id' => $id]); 
        } else {
            return $this->owner;
        }
    }
}
