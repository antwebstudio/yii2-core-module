<?php

namespace ant\category\models;

use Yii;

/**
 * This is the model class for table "{{%category_type}}".
 *
 * @property string $id
 * @property string $type
 * @property string $model
 * @property integer $status
 *
 * @property CategoryMap[] $categoryMaps
 * @property CategoryMap[] $categoryMaps0
 */
class CategoryType extends \yii\db\ActiveRecord
{
	const DEFAULT_NAME = 'default';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_type}}';
    }
	
	public static function ensure($type, $attributes = []) {
		$model = self::find()->cache(7200)->andWhere(['name' => $type])->one();
		
		if (!isset($model)) {
			$model = new self;
			$model->attributes = $attributes;
			$model->name = $type;
			if (!isset($model->title)) $model->title = \yii\helpers\Inflector::camel2words($model->name);
			
			if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		}
		return $model;
	}
	
	public static function getIdFor($type) {
		if (isset($type)) {
			if (is_int($type)) {
				return $type;
			} else {
				$model = self::find()->cache(7200)->andWhere(['name' => $type])->one();
				if (!isset($model)) {
					$model = new self;
					$model->name = $type;
					$model->title = \yii\helpers\Inflector::camel2words($type);
					
					if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
				}
				return $model->id;
			}
		}
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name', 'model'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'model' => Yii::t('app', 'Model'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryMaps()
    {
        return $this->hasMany(CategoryMap::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryMaps0()
    {
        return $this->hasMany(CategoryMap::className(), ['type_id' => 'id']);
    }
}