<?php

namespace ant\file\models;

use Yii;
use yii\db\ActiveRecord;

use common\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%file_storage_item}}".
 *
 * @property integer $id
 * @property string $component
 * @property string $base_url
 * @property string $path
 * @property string $type
 * @property integer $size
 * @property string $name
 * @property string $upload_ip
 * @property integer $created_at
 */
class FileStorageItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_storage_item}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false
            ],
            [
                'class' => \common\behaviors\IpBehavior::className(),
				'createdIpAttribute' => 'upload_ip',
				'updatedIpAttribute' => 'upload_ip',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['component', 'path'], 'required'],
            [['size'], 'integer'],
            [['component', 'name', 'type'], 'string', 'max' => 255],
            [['path', 'base_url'], 'string', 'max' => 1024],
            [['type'], 'string', 'max' => 45],
            [['upload_ip'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'component' => 'Component',
            'base_url' => 'Base Url',
            'path' => 'Path',
            'type' => 'Type',
            'size' => 'Size',
            'name' => 'Name',
            'upload_ip' => 'Upload Ip',
            'created_at' => 'Created At'
        ];
    }
	
	public function getUrl() {
		return $this->base_url.'/'.$this->path;
	}
	
	public function toString() {
		return $this->getUrl();
	}
	
	public function __toString() {
		return $this->toString();
	}
}
