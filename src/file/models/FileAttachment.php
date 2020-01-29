<?php

namespace ant\file\models;

use Yii;
use yii\imagine\Image;
use ant\helpers\Html;
use ant\helpers\File;
use ant\models\ModelClass;

/**
 * This is the model class for table "s_file_attachment".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property integer $file_storage_item_id
 * @property integer $order
 * @property string $path
 * @property string $base_url
 * @property string $type
 * @property integer $size
 * @property string $name
 * @property integer $created_at
 */
class FileAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_attachment}}';
    }
	
	public static function storeFromPath($filePath) {
		
		$file = \trntv\filekit\File::create($filePath);

		$path = Yii::$app->fileStorage->save($filePath);
		
		$model = new \ant\file\models\FileAttachment;
		$model->attributes = [
			'base_url' => Yii::$app->fileStorage->baseUrl,
			'path' => $path,
			'type' => $file->getMimeType(),
			'size' => $file->getSize(),
			'name' => $file->getPathInfo('basename'),
		];
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		return $model;
	}
	
	public static function toSliderFormat($attachmentArray, $width = '100%', $height = '420px') {
		$return = [];
		foreach ($attachmentArray as $attachment) {
			$url = self::getUrl($attachment);
			
			if (self::isImage($attachment)) {
				$return[] = Html::tag('div', Html::img($url, ['style' => 'width: '.$width.'; height: '.$height.'; object-fit: contain; ']), ['class' => 'background-filter', 	'style' => 'background-image: url(\''.$url.'\');']);
			} else {
				$return[] = Html::video($url, ['controls' => 'controls', 'width' => '100%']);
			}
		}
		return $return;
	}
	
	public static function isVideo($attachmentArray) {
		return File::isVideoTypeMime($attachmentArray['type']);
	}
	
	public static function isImage($attachmentArray) {
		return File::isImageTypeMime($attachmentArray['type']);
	}
	
	public static function getFirstUrl($attachmentArray, $useOwnBaseUrl = true, $baseUrlAttribute = 'base_url', $pathAttribute = 'path') {
		if (isset($attachmentArray[$baseUrlAttribute])) {
			return self::getUrl($attachmentArray, $useOwnBaseUrl, $baseUrlAttribute, $pathAttribute);
		} else if (isset($attachmentArray[0][$baseUrlAttribute])) {
			return self::getUrl($attachmentArray[0], $useOwnBaseUrl, $baseUrlAttribute, $pathAttribute);
		}
	}
	
	public static function getUrl($attachmentArray, $useOwnBaseUrl = true, $baseUrlAttribute = 'base_url', $pathAttribute = 'path') {
		$baseUrl = $useOwnBaseUrl ? $attachmentArray['base_url'] : Yii::$app->fileStorage->baseUrl;
		return isset($attachmentArray) ? self::normalizeUrl($baseUrl.'/'.$attachmentArray['path']) : null;
	}
	
	protected static function normalizeUrl($url) {
		return str_replace(['\\', '\/\/'], '\/', $url);
	}
	
	public function behaviors() {
		return [
			[
				'class' => 'ant\behaviors\SerializeBehavior',
				'serializeMethod' => \ant\behaviors\SerializeBehavior::METHOD_JSON,
				'attributes' => ['data'],
			],
			[
				'class' => 'ant\behaviors\EventHandlerBehavior',
				'events' => [
					\ant\behaviors\DuplicableBehavior::EVENT_AFTER_DUPLICATE => function($event) {
						$attachment = $event->sender;
						$attachment->path = Yii::$app->fileStorage->save($attachment->getFullPath());
						
						if (!$attachment->save()) throw new \Exception('Failed to save. ');
					},
				],
			],
		];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path'], 'required'],
            [['order', 'size', 'created_at'], 'integer'],
            [['path', 'base_url', 'type', 'name'], 'string', 'max' => 255],
			[['model', 'model_class_id', 'model_id', 'caption', 'description', 'data'], 'safe'],
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
            //'file_storage_item_id' => 'File Storage Item ID',
            'order' => 'Order',
            'path' => 'Path',
            'base_url' => 'Base Url',
            'type' => 'Type',
            'size' => 'Size',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }
	
	public function getModel() {
		if (isset($this->model_class_id)) {
			$class = ModelClass::getClassName($this->model_class_id);
			return $this->hasOne($class, ['id' => 'model_id']);
		}
	}
	
	public function getFullPath() {
		return Yii::$app->fileStorage->filesystem->getAdapter()->applyPathPrefix($this->path);
	}

    public function getGroup() {
        return $this->hasOne(FileAttachmentGroup::className(), ['id' => 'group_id']);
    }
}
