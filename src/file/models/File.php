<?php

namespace ant\file\models;

use Yii;
use yii\db\ActiveRecord;
use ant\helpers\DateTime;
use ant\user\models\User;
use ant\file\models\Folder;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property integer $file_storage_item_id
 * @property integer $folder_id
 * @property integer $position
 * @property integer $owner_id
 * @property integer $created_by
 * @property integer $collaborator_group
 * @property string $created_at
 *
 * @property FileStorageItem $fileStorageItem
 * @property FileFolder $folder
 * @property User $owner
 */
class File extends ActiveRecord
{
	public $file;
	
	public function behaviors() {
		return [
			[
				'class' => \ant\behaviors\TimeStampBehavior::className(),
				'updatedAtAttribute' => null,
			],
			[
				'class' => \ant\behaviors\EventHandlerBehavior::className(),
				'events' => [
					ActiveRecord::EVENT_BEFORE_VALIDATE => [$this, 'beforeValidateModel'],
				],
			],
		];
	}
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }
	
	public static function iconCssClass($mimeType) {
		$icons = [
			'image/png' => 'fa-file-image-o',
			'image/jpeg' => 'fa-file-image-o',
			'application/pdf' => 'fa-file-pdf-o',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel-o',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word-o',
			'application/msword' => 'fa-file-word-o',
			'application/vnd.ms-powerpoint' => 'fa-file-powerpoint-o',
			'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint-o',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint-o',
			'application/x-rar' => 'fa-file-archive-o',
			'application/zip' => 'fa-file-archive-o',
		];
		
		return isset($icons[$mimeType]) ? $icons[$mimeType] : 'fa-file-o';
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_storage_item_id'], 'required'],
            [['file_storage_item_id', 'folder_id', 'position', 'owner_id', 'created_by', 'collaborator_group'], 'integer'],
            [['file', 'expire_at', 'created_at'], 'safe'],
            [['file_storage_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => FileStorageItem::className(), 'targetAttribute' => ['file_storage_item_id' => 'id']],
            [['folder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Folder::className(), 'targetAttribute' => ['folder_id' => 'id']],
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
            'file_storage_item_id' => 'File Storage Item ID',
            'folder_id' => 'Folder ID',
            'position' => 'Position',
            'owner_id' => 'Owner ID',
            'created_by' => 'Created By',
            'collaborator_group' => 'Collaborator Group',
            'created_at' => 'Created At',
        ];
    }

	public function beforeValidateModel($event) {
		if (isset($this->file)) {
			// Set file name
			$this->name = $this->file['name'];
			
			// Set file_storage_item_id
			$fileItem = \ant\file\models\FileStorageItem::findOne([
				'path' => $this->file['path'],
			]);
			
			if (isset($fileItem)) {
				$this->file_storage_item_id = $fileItem->id;
			}
		}
	}
	
	public function getIsExpired() {
		if (isset($this->expire_at)) {
			$now = new DateTime();
			$expireDate = new DateTime($this->expire_at);
			return ($expireDate < $now);
		}
		return false;
	}
	
	public function getOwnerId() {
		return isset($this->owner_id) ? $this->owner_id : $this->folder->owner_id;
	}
	
	public function getFilename() {
		return isset($this->name) ? $this->name : $this->fileStorageItem->name;
	}
	
	public function getIconCssClass() {
		return self::iconCssClass($this->fileStorageItem->type);
	}
	
	public function getDownloadUrl() {
		return \yii\helpers\Url::to($this->getRoute());
	}
	
	public function getRoute() {
		return ['/file/file/download', 'id' => $this->id];
	}
	
	public function getUrl() {
		return $this->getDownloadUrl();
	}
	
	public function getPath() {
		$dir = \Yii::getAlias('@storage/web/source');
		return $dir.'/'.$this->fileStorageItem->path;
	}
	
	public function getDirectUrl() {
		return $this->fileStorageItem->base_url.'/'.$this->fileStorageItem->path;
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFileStorageItem()
    {
        return $this->hasOne(FileStorageItem::className(), ['id' => 'file_storage_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFolder()
    {
        return $this->hasOne(Folder::className(), ['id' => 'folder_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }
}
