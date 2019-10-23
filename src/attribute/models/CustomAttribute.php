<?php  
namespace ant\attribute\models;

use Yii;
use yii\base\Model;
use yii\base\UnknownPropertyException;

use ant\helpers\ArrayHelper;
use ant\attribute\models\AttributeGroup;
use ant\attribute\models\Attribute;

class CustomAttribute extends Model
{
	public $context = null;

	protected $_customAttributes = null;

	public function __construct($context, $config = [])
	{
		parent::__construct($config);

		$this->context = $context;
	}

	public function __set($name, $value)
	{
		$setter = 'set' . $name;
		
        if (method_exists($this, $setter)) {

            $this->$setter($value);

        } else if (isset($this->{$name})) {

        	$this->{$name} = $value;

        } else {

    		$this->_customAttributes[$name] = $this->getAttribute($name);
    		$this->_customAttributes[$name]->name = $name;
    		$this->_customAttributes[$name]->setting = $value;

        }
	}

	public function __get($name)
	{
		$getter = 'get' . $name;

		if (method_exists($this, $getter)) {

            return $this->$getter();

        } else if (isset($this->{$name})) {

        	return $this->{$name};

        } else {

        	if ($this->hasAttribute($name)) {

        		return $this->customAttributes[$name];

        	} else {

        		throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);

        	}
        }
	}

	public function __isset($name)
    {
        $getter = 'get' . $name;

        if (method_exists($this, $getter)) return $this->$getter() !== null;

        if (isset($this->{$name})) return true;

        return false;
    }

	public function getAttribute($find = null)
	{
		$attribute = null;

		if($find) 
		{
			$query = Attribute::find()->andWhere(['group_id' => $this->context->attribute_group_id]);

			if (is_integer($find)) {
				$query->andWhere(['name' => $find]);
			} else {
				$query->andWhere(['id' => $find]);
			}

			$attribute = $query->one();
		}

		return $attribute ? $attribute : new Attribute;
	}

	public function getAttributes($names = null, $except = [])
	{
		$values = [];

        if ($names === null) $names = $this->attributes();

        foreach ($names as $name) $values[$name] = $this->$name;

        foreach ($except as $name) unset($values[$name]);

        return $values;
	}

	public function attributes()
	{
		return $this->customAttributes ? array_keys($this->customAttributes) : [];
	}

	public function getCustomAttributes()
	{
		if ($this->_customAttributes === null && isset($this->context->attributeGroup->dbAttributess)) 
		{
			foreach ($this->context->attributeGroup->dbAttributess as $attribute) 
			{
				$this->_customAttributes[$attribute->name] = $attribute;
			}
		}

		return $this->_customAttributes;
	}

	public function setAttributes($values, $safeOnly = true)
    {

        if (is_array($values) && !empty($values)) {

        	$this->_customAttributes = null;

            foreach ($values as $name => $value) 
            {
            	$this->$name = $value;
            }

        } else {
			$this->_customAttributes = [];    		
    	}
    } 

	public function hasAttribute($name)
	{
		return isset($this->customAttributes[$name]);
	}

	public function afterValidate()
    {
        if (!Model::validateMultiple($this->getAllModels())) 
        {
            $this->addError(null);
        }

        parent::afterValidate();
    }

	public function save()
	{
		//if (!$this->context->save()) return false;

		if (!$this->saveAttributeGroup()) return false;

		if (!$this->saveAttributes()) return false;

		return true;
	}

	public function saveAttributeGroup()
	{
		$isNewRecord = false;

		$attributeGroup = $this->context->attributeGroup;

		if (!$attributeGroup) 
		{
			$attributeGroup = new AttributeGroup;
			$isNewRecord = true;
		}

		if (!$attributeGroup->save()) return false;

		if ($isNewRecord) $this->context->link('attributeGroup', $attributeGroup);

		return true;
	}

	public function saveAttributes()
	{
		$transaction = Yii::$app->db->beginTransaction();

		$keep = [];

		foreach ($this->customAttributes as $name => $attribute) 
		{
			$isNewRecord = $attribute->isNewRecord;	
			
			$attribute->group_id = $this->context->attributeGroup->id;
			
			if(!$attribute->save()) 
			{
				$transaction->rollBack();

				return false;
			}

			//if($isNewRecord) $attribute->link('group', $this->context->attributeGroup);

			$keep[] = $attribute->id;
		}

		$query = Attribute::find()
			->andWhere(['group_id' => $this->context->attribute_group_id]);

		if ($keep) $query->andWhere(['not in', 'id', $keep]);

		foreach ($query->all() as $attribute) $attribute->delete();

		$transaction->commit();

		return true;
	}

	protected function getAllModels()
    {
        $models = [];
        
        foreach ($this->customAttributes as $key => $attribute) 
        {
            $models['Attribute.' . $attribute->id] = $this->customAttributes[$key];
        }

        return $models;
    }

    public function errorSummary($form)
    {
        $errorLists = [];

        foreach ($this->getAllModels() as $id => $model) 
        {
            $errorList = $form->errorSummary($model, [
              'header' => '<p>Please fix the following errors for <b>' . $id . '</b></p>',
            ]);
            $errorList = str_replace('<li></li>', '', $errorList); // remove the empty error
            $errorLists[] = $errorList;
        }

        return implode('', $errorLists);
    }
}
?>