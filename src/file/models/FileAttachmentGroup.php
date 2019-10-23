<?php

namespace ant\file\models;

use Yii;

/**
 * This is the model class for table "ss_file_attachment_group".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property string $type
 * @property string $created_at
 */
class FileAttachmentGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_attachment_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id'], 'required'],
            [['model_id'], 'integer'],
            [['created_at'], 'safe'],
            [['model', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'model_id' => 'Model ID',
            'type' => 'Type',
            'created_at' => 'Created At',
        ];
    }

    public function getAttachments() {
        return $this->hasMany(FileAttachment::className(), ['group_id' => 'id']);
    }
}
