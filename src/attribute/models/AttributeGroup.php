<?php

namespace ant\attribute\models;

use Yii;

use common\helpers\ArrayHelper;

/**
 * This is the model class for table "em_attribute_group".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Attribute[] $attributes
 */
class AttributeGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attribute_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDbAttributes()
    {
        return $this->hasMany(Attribute::className(), ['group_id' => 'id']);
    }

    public function getDbAttributesArray()
    {
        return ArrayHelper::map($this->dbAttributes, 'name', 'setting');
    }

    public function dbAttributes()
    {
        return array_keys($this->dbAttributesArray);
    }
}
