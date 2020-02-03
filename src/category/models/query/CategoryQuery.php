<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/4/14
 * Time: 2:31 PM
 */

namespace ant\category\models\query;

use ant\category\models\Category;
use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery
{
	public function behaviors() {
        return [
            \ant\behaviors\NestedSetsQueryBehavior::className(),
        ];
    }
	
    /**
     * @return $this
     */
    public function active()
    {
        $this->alias('category')->andWhere(['category.status' => Category::STATUS_ACTIVE]);

        return $this;
    }

    /**
     * @return $this
     */
    public function noParents()
    {
        $this->andWhere('{{%category}}.parent_id IS NULL');

        return $this;
    }
	
	public function rootsOfType($type) {
		return $this->roots()->typeOf($type);
	}
	
	public function nodesOfDepth($depth) {
		$modelClass = $this->modelClass;
		$model = new $modelClass;
		$depthAttribute = $model->getBehavior('tree')->depthAttribute;
		
		return $this->andWhere([$depthAttribute => $depth]);
	}
	
	public function except($ids) {
		return $this->alias('category')->andWhere(['NOT IN', 'category.id', (array) $ids]);
	}
	
	public function type($type) {
		return $this->typeOf($type);
	}
	
	public function typeOf($type) {
		if (is_int($type)) {
			return $this->joinWith('type type')->andWhere(['type.id' => $type]);
		} else {
			return $this->joinWith('type type')->andWhere(['type.name' => $type]);
		}
	}
	
	public function childrenOf($parent, $depth = null) {
		if (!isset($parent)) throw new \Exception('Parent node is null. ');
		
		$modelClass = $this->modelClass;
		$parent = is_object($parent) ? $parent : $modelClass::findOne($parent);

		$model = new $modelClass;
		$leftAttribute = $model->getBehavior('tree')->leftAttribute;
		$rightAttribute = $model->getBehavior('tree')->rightAttribute;
		$depthAttribute = $model->getBehavior('tree')->depthAttribute;
		$treeAttribute = $model->getBehavior('tree')->treeAttribute;
		
		$condition = [
            'and',
            ['>', $leftAttribute, $parent->getAttribute($leftAttribute)],
            ['<', $rightAttribute, $parent->getAttribute($rightAttribute)],
			[$treeAttribute => $parent->getAttribute($treeAttribute)],
        ];
		
		if ($depth !== null) {
            $condition[] = ['<=', $depthAttribute, $parent->getAttribute($depthAttribute) + $depth];
        }
		
		return $this->andWhere($condition);
	}
}
