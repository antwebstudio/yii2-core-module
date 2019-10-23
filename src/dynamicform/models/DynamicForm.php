<?php
namespace ant\dynamicform\models;

use yii\db\ActiveRecord;
use ant\behaviors\TimestampBehavior;
use ant\dynamicform\models\DynamicField;

class DynamicForm extends ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dynamic_form}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return
        [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dynamicFields'], 'safe']
        ];
    }
	
	public static function ensureFor($modelClassId, $modelId) {
		$model = self::findOne(['model_class_id' => $modelClassId, 'model_id' => $modelId]);
		
		if (!isset($model)) {
			$model = new self;
			$model->model_class_id = $modelClassId;
			$model->model_id = $modelId;
			
			if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		}
		return $model;
	}


    public function getDynamicFields()
    {
        return $this->hasMany(DynamicField::className(), ['id' => 'dynamic_form_field_id'])->viaTable('{{%dynamic_form_field_map}}', ['dynamic_form_id' => 'id'])
			->indexBy('id');
    }
}
