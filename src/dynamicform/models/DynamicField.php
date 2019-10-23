<?php  
namespace ant\dynamicform\models;

use yii\helpers\Html;
use yii\db\ActiveRecord;
use ant\behaviors\TimestampBehavior;
use ant\behaviors\SerializeBehavior;
use ant\dynamicform\models\DynamicForm;
use ant\dynamicform\base\FieldTypes;

class DynamicField extends ActiveRecord
{
    private $model;

	/**
     * @inheritdoc
     */
	public function behaviors()
	{
		return 
        [
			'softDeleteBehavior' => [
				'class' => \ant\behaviors\TrashableBehavior::className(),
				'softDeleteAttributeValues' => [
					'is_deleted' => true
				],
			],
			[
				'class' => TimestampBehavior::className(),
			],
            [
                'class' => SerializeBehavior::className(),
                'attributes' => ['setting'],
            ],
		];
	}

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return 
        [
        	[['label'], 'required'],
			//[['label'], 'match', 'pattern' => '/^[a-z0-9\s\-\/\.]+$/i'],
        	[['label'], 'string', 'max' => 255],
        	[['class'], 'string'],
            [['setting', 'value', 'required', 'name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dynamic_form_field}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return 
        [
            'id' => 'ID',
            'label' => 'Label',
            'class' => 'Class',
            'setting' => 'Setting',
            'published_at' => 'Published At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
	public static function find() {
        return new \ant\dynamicform\models\query\DynamicFieldQuery(get_called_class());
	}

    /**
     * @return
     */
    public function getDynamicForm()
    {
        return $this->hasOne(DynamicForm::className(), ['id' => 'dynamic_form_id'])->viaTable('{{%dynamic_form_field_map}}', ['dynamic_form_field_id' => 'id']);
    }

    public function getModel()
    {
        if($this->model === null)
        {
            $class = $this->class ? $this->class : FieldTypes::getDefaultClass()[0];
            $this->model = new $class;
        }
        
        $this->model->load([(new \ReflectionClass($this->model))->getShortName() => $this->setting]);

		if (!isset($this->model)) throw new \Exception('Unexpected error');
        return $this->model;
	}
	
	public function getHandle() {
		return isset($this->name) ? $this->name : $this->getName();
	}
	
	public function getName() {
		return self::resolveIdToName($this->id);
		//return self::resolveLabelToName($this->label);
	}
	
	public function render($form, $model, $options = []) {
		$className = $this->class;
		$attribute = $this->handle;
		
		if (!isset($options['class'])) $options['class'] = 'form-control';
		
		return $form->field($model, $attribute)->widget($this->class, [
			'options' => $options,
			'dynamicField' => $this,
		])->label($this->label);
	}
	
	public function getAllDynamicFormData() {
		return $this->hasOne(DynamicFormData::class, ['dynamic_form_field_id' => 'id']);
	}
	
	public function getDynamicFormData($modelId) {
		return $this->hasOne(DynamicFormData::class, ['dynamic_form_field_id' => 'id'])
			->onCondition(['model_id' => $modelId]);
	}
	
	public function getValue($modelId) {
		$fieldData = $this->getDynamicFormData($modelId)->one();
		if (isset($fieldData)) {
			return $fieldData->value;
		}
	}
	
	public function saveValue($value, $modelId) {
		if ($dynamicFormData = $this->getDynamicFormData($modelId)->one()) {
			// Update
			$dynamicFormData->value = $value;
			
			if (!$dynamicFormData->save()) throw new \Exception(\yii\helpers\Html::errorSummary($dynamicFormData));
		} else {
			// Insert
			$data = new DynamicFormData([
				'dynamic_form_id' => 0, // $this->parent->{$this->relationName}->dynamicForm->id,
				'dynamic_form_field_id' => $this->id,
			]);
			$data->value = $value;
			$data->model_id = $modelId;
			
			if (!$data->save()) throw new \Exception(\yii\helpers\Html::errorSummary($data));
			
			$this->link('allDynamicFormData', $data);
		}
	}
	
	public function getFieldType() {
		$config = $this->setting;
		$config['class'] = $this->class;
		$config['field'] = $this;
		return \Yii::createObject($config);
	}
	
	public function getInputRules() {
		$config = $this->setting;
		$config['class'] = $this->class;
		$fieldType = \Yii::createObject($config);
		
		$attribute = $this->handle;
		
		$rules = [];
		
		foreach ($fieldType->inputRules() as $rule) {
			$ruleName = array_shift($rule);
			$rules[] = [$attribute, $ruleName, $rule];
		}
		
		$rules[] = [$attribute, 'safe', null];
		if ($this->required) {
			$rules[] = [$attribute, 'required', null];
		}
		return $rules;
	}
	
	public function getDynamicFieldMap() {
		return $this->hasMany(DynamicFieldMap::class, ['dynamic_form_field_id' => 'id']);
	}
	
	public static function findByName($name, $dynamicFormId) {
		return self::find()->joinWith('dynamicFieldMap')
			->andWhere(['name' => $name, 'dynamic_form_id' => $dynamicFormId])->one();
	}
	
	public static function resolveIdToName($id) {
		return 'field_'.$id;
	}
	
	public static function resolveLabelToName($label) {
		if (YII_DEBUG) throw new \Exception('Deprecated');
		return $label;
		return str_replace([' ', '-', '/', '\\', '.'], '_', $label);
	}
}