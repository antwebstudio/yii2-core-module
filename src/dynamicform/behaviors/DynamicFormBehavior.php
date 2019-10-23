<?php
namespace ant\dynamicform\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

use ant\dynamicform\models\DynamicFormForm;
use ant\dynamicform\models\DynamicFormBuild;
use ant\dynamicform\models\DynamicForm;

class DynamicFormBehavior extends Behavior
{
    const RELATION_COLUMN_NAME = 'dynamic_form_id';

	protected $_dynamicFormForm;
	protected $_dynamicFields;
	protected $_labelToIdMap;

	public function events()
    {
        return
        [
            ActiveRecord::EVENT_AFTER_FIND      => 'afterFind',
            ActiveRecord::EVENT_AFTER_VALIDATE  => 'afterValidate',
            ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT    => 'afterInsert',
        ];
    }

    public function afterFind()
    {
        //$this->dynamicFormForm = new DynamicFormForm();
        //$this->dynamicFormForm = $this->owner->dynamicForm;
    }

    public function afterValidate()
    {
        if($this->dynamicFormForm) $this->dynamicFormForm->afterValidate();
    }
	
	public function afterUpdate() {
		$this->updateDynamicForm();
	}

    public function afterInsert()
    {
		$this->updateDynamicForm();
    }
	
	protected function updateDynamicForm() {
        if(isset($this->dynamicFormForm))
        {
            if($this->dynamicFormForm->save())
            {
                if (!$this->owner->getDynamicForm()->count()) $this->owner->link('dynamicForm', $this->dynamicFormForm->dynamicForm);
            } else {
				// Don't throw exception here or else exception will be thrown whenever validation of dynamic field form invalid.
				//throw new \Exception(\yii\helpers\Html::errorSummary($this->dynamicFormForm));
			}
        }
	}

    public function setDynamicFormForm($dynamicFormForm)
    {
        if($dynamicFormForm instanceof DynamicFormForm)
    	{
    		$this->dynamicFormForm = $dynamicFormForm;
        }
        else if (is_array($dynamicFormForm))
        {
            $this->dynamicFormForm = new DynamicFormForm(['dynamicForm' => $this->getDynamicForm()->one()]);
            $this->dynamicFormForm->load($dynamicFormForm);
        }
    }

    public function getDynamicFormForm()
    {
		if (!isset($this->_dynamicFormForm)) {
			$this->_dynamicFormForm = new DynamicFormForm(['dynamicForm' => $this->owner->dynamicForm]);
		}
		//if ($this->owner->dynamicForm->id != 8) throw new \Exception('test:'.$this->owner->dynamicForm->id.' : '.(isset($this->dynamicFormForm) ? 'y':'n'));
    	//$return = isset($this->_dynamicFormForm) ? $this->dynamicFormForm : new DynamicFormForm(['dynamicForm' => $this->owner->dynamicForm]);
		
		return $this->_dynamicFormForm;
    }

    public function getDynamicForm()
    {
        return $this->owner->hasOne(DynamicForm::className(), ['id' => self::RELATION_COLUMN_NAME]);
    }

    /*public function getDynamicFormBuild()
    {
        $model = new DynamicFormBuild(compact('name'));

        return $model;
    }*/
	
	public function getDynamicFieldByLabel($label) {
		//if (!isset($this->_dynamicFields)) {
			$this->_dynamicFields = $this->getDynamicFields();
		//}
		
		$fieldId = isset($this->_labelToIdMap[$label]) ? $this->_labelToIdMap[$label] : null;
		//if (!isset($this->_dynamicFields[$label])) throw new \Exception($label.'not set'.print_r(array_keys($this->_dynamicFields),1));
		return isset($this->_dynamicFields[$fieldId]) ? $this->_dynamicFields[$fieldId] : null;
	}
	
	public function getDynamicFields() {
		if (!isset($this->_dynamicFields)) {
			$this->_dynamicFields = $this->owner->dynamicForm->getDynamicFields()->indexBy('id')->all();
			
			$this->_labelToIdMap = [];
			
			foreach ($this->_dynamicFields as $id => $field) {
				$this->_labelToIdMap[$field->label] = $id;
			}
			//throw new \Exception('count: '.count($this->_dynamicFields));
		}
		//throw new \Exception('#count: '.count($this->_dynamicFields));
		return $this->_dynamicFields;
	}
}
?>
