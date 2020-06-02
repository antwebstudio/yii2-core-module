<?php

namespace ant\dynamicform\models;

use Yii;

/**
 * This is the model class for table "em_dynamic_form_data".
 *
 * @property integer $id
 * @property string $label
 * @property integer $dynamic_form_field_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DynamicFormField $dynamicFormField
 */
class DynamicFormData extends \yii\db\ActiveRecord
{
	protected $_value;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dynamic_form_data}}';
    }
	
	public function init() {
		//$this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeUpdate']);
		//$this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeInsert']);
	}
	
	public function behaviors() {
		return [
			[
				'class'=> \ant\behaviors\EventHandlerBehavior::className(),
				'events' => [
					self::EVENT_BEFORE_UPDATE => [$this, 'beforeUpdate'],
					self::EVENT_BEFORE_INSERT => [$this, 'beforeInsert'],
				],
			],
			[
                'class' => \ant\behaviors\SerializableAttribute::class,
                'attributes' => ['value_json'],
			]
		];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['label'], 'required'],
            [['dynamic_form_field_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            //[['label'], 'string', 'max' => 256],
            [['dynamic_form_field_id'], 'exist', 'skipOnError' => true, 'targetClass' => DynamicField::className(), 'targetAttribute' => ['dynamic_form_field_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //'label' => 'Label',
            'dynamic_form_field_id' => 'Dynamic Form Field ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
	public function getValue() {
		return isset($this->_value) ? $this->_value : $this->{$this->getFieldDbColumnName()};
	}
	
	public function setValue($value) {
		$this->_value = $value;
	}
	
	public function beforeUpdate() {
		$this->updateValue($this->_value);
	}
	
	public function beforeInsert() {
		$this->updateValue($this->_value);
	}
	
	protected function updateValue($value) {
		$this->{$this->getFieldDbColumnName()} = $value;
	}
	
	protected function getFieldDbColumnName() {
		$className = $this->dynamicField->class;
		$fieldType = new $className;
		return $fieldType->fieldName;
	}
	
    public function getDynamicFormField() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED');
		return $this->getDynamicField();
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDynamicField()
    {
        return $this->hasOne(DynamicField::className(), ['id' => 'dynamic_form_field_id']);
    }
}
