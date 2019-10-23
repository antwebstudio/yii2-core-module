<?php  
namespace ant\dynamicform\models;

class DynamicFieldMap extends \yii\db\ActiveRecord
{

	/**
     * @inheritdoc
     */
	public function behaviors()
	{
		return 
        [
		];
	}

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return 
        [
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dynamic_form_field_map}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return 
        [
        ];
    }
}