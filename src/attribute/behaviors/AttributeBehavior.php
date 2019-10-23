<?php  
namespace ant\attribute\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

use ant\helpers\ArrayHelper;
use ant\attribute\models\AttributeGroup;
use ant\attribute\models\Attribute;

class AttributeBehavior extends Behavior
{
	public $dbAttribtueGroupRelation = 'dbAttributeGroup';

	public $attribute = 'dbAttributes';

	protected $_dbAttributeGroup = null;

	// events
	public function events()
	{
		return 
		[
			ActiveRecord::EVENT_AFTER_FIND 		=> 'afterFind',
			// ActiveRecord::EVENT_AFTER_VALIDATE 	=> 'afterValidate',
			ActiveRecord::EVENT_AFTER_INSERT 	=> 'afterSave',
   			ActiveRecord::EVENT_AFTER_UPDATE 	=> 'afterSave',
   			ActiveRecord::EVENT_AFTER_REFRESH 	=> 'afterRefresh',
		];
	}

	public function afterFind()
	{
		$this->refreshDbAttribute();
	}

	public function afterSave()
	{
		if (!$this->saveDbAttributesToRelation(is_array($this->owner->{$this->attribute}) ? $this->owner->{$this->attribute} : [])) return false;

		$this->refreshDbAttribute();

		return true;
	}

	public function afterRefresh()
	{
		$this->refreshDbAttribute();
	}

	// getter and setter helpers
	public function getDbAttributeValue($name)
	{
		if (!isset($this->owner->{$this->attribute})) return false;

		foreach ($this->owner->{$this->attribute} as $dbAttribute) 
		{
			if ($dbAttribute[Attribute::FIELD_NAME] == $name) 
			{
				return $dbAttribute[Attribute::FIELD_VALUE];
			}
		}

		return null;
	}

	public function setDbAttributeValue($name, $value)
	{
		if (isset($this->owner->{$this->attribute}))
		{
			foreach ($this->owner->{$this->attribute} as $index => $dbAttribute) 
			{
				if ($dbAttribute[Attribute::FIELD_NAME] == $name) 
				{
					return $this->owner->{$this->attribute}[$index][Attribute::FIELD_VALUE] = $value;
				}
			}
		}
	}

	// attribute
	public function getDbAttributeModels()
	{
		return $this->dbAttributeGroup->dbAttributes;
	}

	public function getDbAttributeModel($name)
	{
		return $this->dbAttributeGroup
			->getDbAttributes()
			->andWhere([Attribute::FIELD_NAME => $name])
			->one();
	}

	public function hasDbAttributeModel($name)
	{
		return $this->dbAttributeGroup
			->getDbAttributes()
			->andWhere([Attribute::FIELD_NAME => $name])
			->count() > 0;
	}

	protected function getDbAttributeGroup()
	{
		if ($this->_dbAttributeGroup === null) 
		{
			try {
				// try to get replation from owner if exists, else throw exception by default. 
				$dbAttribtueGroupRelation = $this->owner->getRelation($this->dbAttribtueGroupRelation);

				$dbAttributeGroup = $dbAttribtueGroupRelation->one() ? $dbAttribtueGroupRelation->one() : new $dbAttribtueGroupRelation->modelClass;

				$this->_dbAttributeGroup = $dbAttributeGroup;

			} catch (\Exception $ex) {

				$this->_dbAttributeGroup = null;

				if (YII_DEBUG) throw $ex;
			}
		}

		return $this->_dbAttributeGroup;
	}

	public function getDbAttributesArray($names = null, $except = [])
	{
		$values = [];

		$query = $this->dbAttributeGroup
			->getDbAttributes();

		if (!($names === null)) 
		{
			$query->andWhere(['in', Attribute::FIELD_NAME, $names]);
		}

		$query->andWhere(['not in', Attribute::FIELD_NAME, $except]);

		foreach ($query->all() as $dbAttribute) 
		{
			$values[$dbAttribute->{Attribute::FIELD_NAME}] = $dbAttribute->{Attribute::FIELD_VALUE};
		}

		return $values;
	}

	// data
	protected function getData()
	{
		$data = [];

		foreach ($this->dbAttributeGroup->dbAttributes as $dbAttribute) 
		{
			$data[$dbAttribute->{Attribute::FIELD_NAME}] = 
			[
				Attribute::FIELD_NAME 	=> $dbAttribute->{Attribute::FIELD_NAME},
				Attribute::FIELD_VALUE 	=> $dbAttribute->{Attribute::FIELD_VALUE},
			]; 
		}

		return $data;
	}

	// save
	protected function saveDbAttributesToRelation(Array $dbAttributes)
	{	
		if ($this->dbAttributeGroup->isNewRecord) 
		{
			if (!$this->dbAttributeGroup->save()) 
			{
				if (YII_DEBUG) throw new \Exception('Unable to save AttributeGroup', 1);

				return false;
			}

			$this->owner->link($this->dbAttribtueGroupRelation, $this->dbAttributeGroup);
		}

		$transaction = Yii::$app->db->beginTransaction();

		$keep = []; // for keep alive attribute id

		foreach ($dbAttributes as $dbAttribute) 
		{

			$isNewRecord = false;

			$name 	= $dbAttribute[Attribute::FIELD_NAME];
			$value 	= $dbAttribute[Attribute::FIELD_VALUE];

			$model = null;

			if ($this->hasDbAttributeModel($name)) {

				$model = $this->getDbAttributeModel($name);
				$model->{Attribute::FIELD_VALUE} = $value;

			} else {

				$model = new Attribute;
				$model->setAttributes([
					Attribute::FIELD_NAME 	=> $name,
					Attribute::FIELD_VALUE 	=> $value,
				]);

				$isNewRecord = true;
			}

			if (!$model->save()) 
			{
				$transaction->rollBack();

				if (YII_DEBUG) throw new \Exception(print_r($model->getErrors(), 1), 1);

				return false;
			}

			if ($isNewRecord) $model->link('group', $this->dbAttributeGroup);
			
			$keep[] = $model->id; // keep attribute id

		}

		$query = $this->dbAttributeGroup->getDbAttributes();

		if ($keep) $query->andWhere(['not in', 'id', $keep]);

		foreach ($query->all() as $outDatedModel) 
		{
			$outDatedModel->delete();
		}

		$transaction->commit();

		return true;
	}

	//refresh
	protected function refreshDbAttribute()
	{
		if (isset($this->owner->{$this->attribute}))
		{
			$this->owner->{$this->attribute} = $this->data;
		}
	}
}
