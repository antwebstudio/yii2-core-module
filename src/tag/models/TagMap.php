<?php

namespace ant\tag\models;

use Yii;

/**
 * This is the model class for table "tag_map".
 *
 * @property int $model_class_id
 * @property int $model_id
 * @property int $tag_id
 *
 * @property ModelClass $modelClass
 */
class TagMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag_map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_class_id', 'model_id', 'tag_id'], 'integer'],
            [['tag_id'], 'required'],
            [['model_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModelClass::className(), 'targetAttribute' => ['model_class_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'model_class_id' => 'Model Class ID',
            'model_id' => 'Model ID',
            'tag_id' => 'Tag ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelClass()
    {
        return $this->hasOne(ModelClass::className(), ['id' => 'model_class_id']);
    }
}
