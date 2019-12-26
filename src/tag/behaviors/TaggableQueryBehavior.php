<?php 
namespace ant\tag\behaviors;
/*
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use ant\category\models\Category;
use ant\category\models\CategoryMap;
use ant\category\models\CategoryType;
*/
class TaggableQueryBehavior extends \yii\base\Behavior 
{
    public function filterByTagId($id){
        if ($id) {
			$alias = 'tagMap';
			$query = $this->owner->joinWith('tagMap '.$alias);
			
            return $query->andFilterWhere([$alias.'.tag_id' => $id]); 
        } else {
            return $this->owner;
        }
    }
}
