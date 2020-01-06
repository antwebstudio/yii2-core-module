<?php

namespace ant\tag\behaviors;

use yii\db\Query;
use ant\tag\models\Tag;
use ant\tag\models\TagMap;

class TaggableBehavior extends \dosamigos\taggable\Taggable {
	public $modelClassId;
	public $modelClassIdAttribute = 'model_class_id';
	public $asArray = true;
	
	public function attach($owner) {
		if (!isset($this->modelClassId)) $this->modelClassId = \ant\models\ModelClass::getClassId($owner);
		parent::attach($owner);
	}
	
	public function __call($name, $params) {
		$attribute = preg_replace('/^get(.*)/isU', '', $name);
		if ($this->isTagAttribute($attribute)) {
			return $this->getTagsRelation(\ant\models\ModelClass::getClassId(get_class($this)));
		}
		return parent::__call($name, $params);
	}

	public function __get($name) {
		if ($this->isTagAttribute($name)) {
			return $this->getTagsRelation($name);
		} else if (isset($this->tagValues)) {
			return $this->tagValues;
		}
        return $this->getTagNames();
	}
  
	public function canGetProperty($name, $checkVars = true) {
		if ($this->isTagAttribute($name)) {
			return true;
		}
		return parent::canGetProperty($name, $checkVars);
	}
  
	public function hasMethod($name) {
		$attribute = preg_replace('/^get(.*)/isU', '', $name);
		return $this->isTagAttribute($attribute) || parent::hasMethod($name);
	}
  
	protected function isTagAttribute($attribute) {
		if (isset($this->relation) && $attribute == $this->relation) {
		
			return true;
		}
		return false;
	}
	
	private function getTagNames()
    {
        $items = [];
        $tags=$this->owner->{$this->relation};
        if (is_array($tags)){
            foreach ($tags as $tag) {
                $items[] = $tag->{$this->name};
            }
        }
		//throw new \Exception(print_r($items,1));
        return $this->asArray ? $items : implode(',', $items);
    }
	
	public function afterSave($event)
    {
        if ($this->tagValues === null) {
            $this->tagValues = $this->owner->{$this->attribute};
        }
        if (!$this->owner->getIsNewRecord()) {
            $this->beforeDelete($event);
        }
        $names = array_unique(preg_split(
            '/\s*,\s*/u',
            preg_replace(
                '/\s+/u',
                ' ',
                is_array($this->tagValues)
                    ? implode(',', $this->tagValues)
                    : $this->tagValues
            ),
            -1,
            PREG_SPLIT_NO_EMPTY
        ));
        $relation = $this->owner->getRelation($this->relation);
        $pivot = $relation->via->from[0];
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        $rows = [];
        $updatedTags = [];
        foreach ($names as $name) {
            $tag = $class::findOne([$this->name => $name, $this->modelClassIdAttribute => $this->modelClassId]);
            if ($tag === null) {
                $tag = new $class();
				$tag->{$this->modelClassIdAttribute} = $this->modelClassId;
                $tag->{$this->name} = $name;
            }
            $tag->{$this->frequency}++;
            if ($tag->save()) {
                $updatedTags[] = $tag;
                $rows[] = [$this->owner->getPrimaryKey(), $tag->getPrimaryKey(), $this->modelClassId];
            } else {
				throw new \Exception('Cannot save tag. '.print_r($tag->errors, 1));
			}
        }
        if (!empty($rows)) {
            $this->owner->getDb()
                ->createCommand()
                ->batchInsert($pivot, [key($relation->via->link), current($relation->link), $this->modelClassIdAttribute], $rows)
                ->execute();
        }
        $this->owner->populateRelation($this->relation, $updatedTags);
    }
	
	public function beforeDelete($event)
    {
        $relation = $this->owner->getRelation($this->relation);
        $pivot = $relation->via->from[0];
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        $query = new Query();
        $pks = $query
            ->select(current($relation->link))
            ->from($pivot)
            ->where([
				key($relation->via->link) => $this->owner->getPrimaryKey(),
				$this->modelClassIdAttribute => $this->modelClassId,
			])
            ->column($this->owner->getDb());
        if (!empty($pks)) {
            $class::updateAllCounters([$this->frequency => -1], ['in', $class::primaryKey(), $pks]);
        }
        $this->owner->getDb()
            ->createCommand()
            ->delete($pivot, [
				key($relation->via->link) => $this->owner->getPrimaryKey(),
				$this->modelClassIdAttribute => $this->modelClassId,
			])
            ->execute();
        
        if ($this->removeUnusedTags)
        {
            $class::deleteAll([$this->frequency => 0]);
        }
    }
	
	public function getTagMap() {
		return $this->owner->hasMany(TagMap::className(), ['model_id' => 'id'])
			->onCondition(['tagMap.model_class_id' => $this->modelClassId]);
	}

	public function getTagsRelation() {
		return $this->owner->hasMany(Tag::className(), ['id' => 'tag_id'])
			//->onCondition(['{{%tag_map}}.model_class_id' => $modelClassId])
			->viaTable('{{%tag_map}}', ['model_id' => 'id'], function($query) {
				$query->andWhere([
				//'category_map.model_id' => $this->owner->id,
					'{{%tag_map}}.model_class_id' => \ant\models\ModelClass::getClassId(get_class($this->owner))
				]);
			});
	}

}