<?php

namespace ant\attributev2\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\behaviors\TimestampBehavior;
use common\behaviors\SerializeBehavior;
use common\behaviors\DefaultValueBehavior;
use ant\attributev2\components\FieldType;

/**
 * This is the model class for table "{{%attributev2}}".
 *
 * @property string $id
 * @property string $name
 * @property string $model
 * @property string $model_id
 *
 * @property Attributev2Value[] $attributev2Values
 */
class Attributev2 extends \yii\db\ActiveRecord
{
    const FORMAT_SAVE         = 'formatSave';
    const FORMAT_SETTING_FORM = 'formatSettingForm';

    public $format = self::FORMAT_SAVE;
    protected $_fieldType = null;
    protected $_isRequired = null;

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className()],
            [
                'class' => SerializeBehavior::className(),
                'serializeMethod' => SerializeBehavior::METHOD_JSON,
                'attributes' => ['rules', 'fieldtype_setting'],
            ],
            [
                'class' => DefaultValueBehavior::className(),
                'attributes' => ['fieldtype' => FieldType::getDefaultFieldType()],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attributev2}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], function ($attribute, $param, $validator) {
                if (!preg_match('/(^[a-zA-Z][a-zA-Z0-9_]*)|(^[_][a-zA-Z0-9_]+)/i', $this->$attribute))
                {
                    $this->addError($attribute, 'Invalid name format.');
                }
            }],
            [['name', 'model'], 'required'],
            [['model', 'name'], 'unique', 'targetAttribute' => ['model', 'name']],
            [['model_id'], 'integer'],
            [['name', 'model', 'fieldtype'], 'string', 'max' => 255],
            [['rules', 'fieldtype_setting', 'label'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->format == self::FORMAT_SETTING_FORM) $this->prepareForSave();
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'model' => 'Model',
            'model_id' => 'Model ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributev2Values()
    {
        return $this->hasMany(Attributev2Value::className(), ['attributev2_id' => 'id']);
    }

    public function fieldtype()
    {
        if ($this->_fieldType === null)
        {
            $this->_fieldType = Yii::createObject(ArrayHelper::merge(['class' => $this->fieldtype], [
                'attribute' => $this,
            ]));
        }
        return $this->_fieldType;
    }

    public function prepareForSettingForm()
    {
        if ($this->format == self::FORMAT_SAVE) 
        {
            $this->fieldtype()->prepareForSettingForm($this);
            $this->format = self::FORMAT_SETTING_FORM;
        }
    }

    public function prepareForSave()
    {
        if ($this->format == self::FORMAT_SETTING_FORM) 
        {
            $this->fieldtype()->prepareForSave($this);
            $this->format = self::FORMAT_SAVE;
        }
    }

    public function isRequired()
    {
        if ($this->_isRequired === null)
        {
            $this->_isRequired = false;
            foreach ($this->rules as $rule) 
            {
                if ($rule[0] == 'required')
                {
                    $this->_isRequired = true;
                    break;
                }
            }
        }
        return $this->_isRequired;
    }
}
