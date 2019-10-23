<?php

namespace ant\file\models;

use Yii;
use ant\user\models\User;

/**
 * This is the model class for table "file_folder".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $position
 * @property integer $owner_id
 * @property integer $created_by
 * @property integer $collaborator_group
 * @property string $created_at
 *
 * @property File[] $files
 * @property User $owner
 */
class Folder extends \yii\db\ActiveRecord
{
	public function behaviors() {
		return [
			[
				'class' => \common\behaviors\TimeStampBehavior::className(),
				'updatedAtAttribute' => null,
			]
		];
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_folder}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id', 'position', 'owner_id', 'created_by', 'collaborator_group'], 'integer'],
            [['owner_id', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parent_id' => 'Parent ID',
            'position' => 'Position',
            'owner_id' => 'Owner ID',
            'created_by' => 'Created By',
            'collaborator_group' => 'Collaborator Group',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['folder_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }
}
