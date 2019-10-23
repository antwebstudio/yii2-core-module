<?php

namespace ant\attribute\models;

use Yii;
use ant\attribute\models\AttributeGroup;

/**
 * This is the model class for table "em_attributes".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $name
 * @property string $setting
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AttributeGroup $group
 */
class Attribute extends \yii\db\ActiveRecord
{
    const FIELD_NAME    = 'name';

    const FIELD_VALUE   = 'setting';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attribute}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[Self::FIELD_NAME], 'required'],
            [['group_id', 'created_by', 'updated_by'], 'integer'],
            [[Self::FIELD_VALUE], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [[Self::FIELD_NAME], 'string', 'max' => 64],
            //[['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Group ID',
            Self::FIELD_NAME => 'Name',
            Self::FIELD_VALUE => 'Setting',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(AttributeGroup::className(), ['id' => 'group_id']);
    }

    public function __toString()
    {
        return $this->setting;
    }
}
