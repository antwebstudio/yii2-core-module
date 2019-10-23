<?php

namespace ant\category\models;

use Yii;

/**
 * This is the model class for table "{{%category_map}}".
 *
 * @property string $id
 * @property string $model_class_id
 * @property string $model_id
 * @property string $category_id
 *
 * @property CategoryType $category
 * @property CategoryType $type
 */
class CategoryMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_map}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_class_id', 'model_id', 'category_id'], 'required'],
            [['model_class_id', 'model_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['model_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryType::className(), 'targetAttribute' => ['model_class_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model_class_id' => Yii::t('app', 'Type ID'),
            'model_id' => Yii::t('app', 'Model ID'),
            'category_id' => Yii::t('app', 'Category ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(CategoryType::className(), ['id' => 'model_class_id']);
    }
}