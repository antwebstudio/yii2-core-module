<?php  
namespace ant\dynamicform\models;

use Yii;
use yii\base\Model;

use ant\dynamicform\models\DynamicForm;
use ant\dynamicform\models\DynamicField;

class DynamicFormForm extends Model
{
	public $dynamicForm;

	private $dynamicFields;

    public function init()
    {
        parent::init();

        if (!isset($this->dynamicForm)) $this->dynamicForm = new DynamicForm(); 
    }

	public function rules()
    {
        return 
        [
            [['dynamicFields'], 'safe'],
        ];
    }

    public function afterValidate()
    {
		foreach ($this->getAllModels() as $model) {
			if (!$model->validate()) {
				$this->addErrors($model->errors);
			}
		}
        parent::afterValidate();
    }

    public function save()
    {
    	if(!$this->validate()) {
			//throw new \Exception('Validation error. '.\yii\helpers\Html::errorSummary($this));
			return false;
		}

        $transaction = Yii::$app->db->beginTransaction();

        if(!$this->dynamicForm->save())
        {
            $transaction->rollBack();
			throw new \Exception('Dynamic form cannot be saved. ');
			$this->addError('dynamicForm', 'Dynamic form cannot be saved. ');
            return false;
        }
        if(!$this->saveDynamicFields())
        {
            $transaction->rollBack();
			throw new \Exception('Dynamic fields cannot be saved. ');
			$this->addError('dynamicFields', 'Dynamic fields cannot be saved. ');
            return false;
        }

        $transaction->commit();
        return true;
    }

    protected function saveDynamicFields()
    {
        $keep = [];

		if (isset($this->dynamicFields)) {
			foreach($this->dynamicFields as $dynamicField)
			{
				//$dynamicField->setting = serialize($dynamicField->setting);
				if(!$dynamicField->save(false)) return false;
				
				$this->dynamicForm->link('dynamicFields', $dynamicField);

				$keep[] = $dynamicField->id;
			}
        
			$dynamicFields = DynamicField::find()->joinWith('dynamicForm dynamicForm')
				->andWhere(['dynamicForm.id' => $this->dynamicForm->id])
				->andWhere(['not in', DynamicField::tableName() . '.id', $keep]);

			//throw new \Exception($this->dynamicForm->id.print_r($keep,1).$dynamicFields->createCommand()->sql);
			
			foreach ($dynamicFields->all() as $dynamicField) {
				$dynamicField->softDelete();
			}
		}

        return true;
    }

    public function getDynamicForm()
    {
    	return $this->dynamicForm;
    }

    public function setDynamicForm($dynamicForm)
    {
    	if($dynamicForm instanceof DynamicForm)
    	{
    		$this->dynamicForm = $dynamicForm;
    	}
    	else if (is_array($dynamicForm))
    	{
            $this->dynamicForm = new DynamicForm();
    		$this->dynamicForm->load($dynamicForm);
    	}
    }

    public function getDynamicFields($notTrashed = true)
    {
        if($this->dynamicFields === null) 
        {
            if ($this->dynamicForm->isNewRecord) {
                $this->dynamicFields =  [];
            } else {
                $this->dynamicFields =  $this->dynamicForm->getDynamicFields()->notTrashed()->indexBy('id')->all();
            }
        }
    	return $this->dynamicFields;
    }


    public function setDynamicFields($dynamicFields)
    {
    	$this->dynamicFields = [];

    	foreach ($dynamicFields as $key => $dynamicField) 
    	{
    		if(is_array($dynamicField))
    		{
    			$this->dynamicFields[$key] = $this->getDynamicField($key);
    			$this->dynamicFields[$key]->load($dynamicField);
    		} 
    		else if ($dynamicField instanceof DynamicField)
    		{
    			$this->dynamicFields[$dynamicField->id] = $dynamicField;
    		}
    	}

    }

    private function getDynamicField($key)
    {
        $dynamicField = $key && strpos($key, 'new') === false ? DynamicField::findOne($key) : false;
        if (!$dynamicField) {
            $dynamicField = new DynamicField();
        }
        return $dynamicField;
    }

    private function getAllModels()
    {
        $models = [
            'DynamicForm' => $this->dynamicForm,
        ];
		
		if (isset($this->dynamicFields)) {
			foreach ($this->dynamicFields as $id => $dynamicField) {
				$models['DynamicField.' . $id] = $this->dynamicFields[$id];
				$models['DynamicField.' . $id . '.model'] = $this->dynamicFields[$id]->model;
			}
		}
        return $models;
    }
}
?>