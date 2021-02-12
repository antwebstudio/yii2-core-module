<?php

namespace ant\file\models;

use Yii;
use yii\imagine\Image;
use yii\helpers\Url;
use ant\helpers\StringHelper as Str;
use ant\helpers\Html;
use ant\helpers\File;
use yii\helpers\FileHelper;
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
			$thumbHeight = Str::endsWith($height, 'px') ? substr($height, 0, -2) : null;
			$thumbWidth = Str::endsWith($width, 'px') ? substr($width, 0, -2) : null;
			if (isset($thumbHeight) || isset($thumbWidth)) {
				$url = self::thumb($attachment, $thumbWidth, $thumbHeight);
			} else {
				$url = self::getUrl($attachment);	
			}
			
			if (self::isImage($attachment)) {
				$caption = isset($attachment['caption']) && $attachment['caption'] ? Html::tag('div', $attachment['caption'], ['class' => 'slide__caption']) : '';
				$return[] = Html::tag('div', Html::img($url, ['style' => 'width: '.$width.'; height: '.$height.'; object-fit: contain; ']).$caption, ['class' => 'background-filter', 'style' => 'position: relative; background-image: url(\''.$url.'\');']);
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
	
	public static function generateThumbnail($url, $width = null, $height = null, $fitType = 'fit', $position = null, $width2 = null, $height2 = null) {
		$notUrl = true;
		if ($notUrl) {
			$fullPathOrUrl = FileHelper::normalizePath(\Yii::$app->fileStorage->getFilesystem()->getAdapter()->applyPathPrefix($url));
		}
		$savePath = self::getThumbnailPath($url, $width, $height, $fitType, $position, $width2, $height2);
		if (file_exists($savePath)) return $savePath;
		 
		try {
			$img = \Intervention\Image\ImageManagerStatic::make($fullPathOrUrl);
		} catch (\Intervention\Image\Exception\NotReadableException $ex) {
			throw new \Exception($ex->getMessage().': '.$fullPathOrUrl);
		}
		
		// Calculation of width/height based on ratio
		if ($width && $height) {
		} else if ($width) {
			$height = (int) ($img->height() / $img->width() * $width);
		} else if ($height) {
			$width = (int) ($img->width() / $img->height() * $height);
		}
		
		if ($fitType == 'cover') {
			$wRatio = $img->width() / $width;
			$hRatio = $img->height() / $height;
			$useRatio = min($wRatio, $hRatio);
			$img->fit((int) ($img->width() / $useRatio), (int) ($img->height() / $useRatio))->crop($width, $height);
		} else if ($fitType == 'crop-and-fit') {
			$position = explode(',', $position);
			if ($width && $height) $img->crop((int) $width, (int) $height, (int) $position[0], (int) $position[1]);
			if ($width2 && $height2) $img->fit((int) $width2, (int) $height2);	
		} else if ($fitType == 'crop') {
			$position = explode(',', $position);
			if ($width && $height) $img->{$fitType}((int) $width, (int) $height, (int) $position[0], (int) $position[1]);	
		} else {
			if ($width && $height) $img->{$fitType}($width, $height, null, $position);
		}

		\ant\helpers\File::ensureDir(dirname($savePath));
		//throw new \Exception($savePath);
		$img->save($savePath);
		
		$symlink = \Yii::getAlias('@webroot/thumb');
		if (!file_exists($symlink)) {			
			//throw new \Exception(\Yii::getAlias('@webroot/thumb'));
			symlink(\Yii::getAlias('@runtime/thumbCache'), \Yii::getAlias('@webroot/thumb'));	
		} else {
			//throw new \Exception('t'); 
		}
		return $img;
	}
	
	public static function getThumbnailPath($url, $width = null, $height = null, $fitType = null, $position = null, $width2 = null, $height2 = null) {
		$thumbDir = (isset($width) ? $width : '') . 'x' . (isset($height) ? $height : '') . '/' . $fitType . '/' . $position; // Before calculation of width/ height
		if (isset($width2) || isset($height2)) $thumbDir .= '/'.(isset($width2) ? $width2 : '') . 'x' . (isset($height2) ? $height2 : '');
		
		return FileHelper::normalizePath(\Yii::getAlias('@runtime/thumbCache/'.$thumbDir).'/'.	$url);
	}
	
	public static function thumb($attachmentArray, $width, $height = null, $fitType = 'fit', $position = null, $pathAttribute = 'path') { 
		if (is_array($attachmentArray) && !isset($attachmentArray[$pathAttribute])) $attachmentArray = $attachmentArray[0];
		
		try {
			if (isset($attachmentArray['cropper'])) {
				$cropper = json_decode($attachmentArray['cropper'], true);
				$fitType = 'crop-and-fit';
				$x = $cropper['x'];
				$y = $cropper['y'];
				$position = $x . ',' . $y;

				$height2 = $width / $cropper['width'] * $cropper['height'];
				$width2 = $width;

				$width = (int) $cropper['width'];
				$height = (int) $cropper['height'];
			}

			self::generateThumbnail($attachmentArray['path'], $width, $height, $fitType, $position, $width2 ?? null, $height2 ?? null);
				
			return Url::to(['/site/thumb', 
				'url' => $attachmentArray['path'],
				'width' => $width,
				'height' => $height,
				'width2' => $width2 ?? null,
				'height2' => $height2 ?? null,
				'position' => $position,
				'fitType' => $fitType,
			], true);
		} catch (\Exception $ex) {
			// if (YII_DEBUG) throw $ex;
		}
		return self::getFirstUrl($attachmentArray);
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
				'class' => \ant\behaviors\TimestampBehavior::class,
				'updatedAtAttribute' => false,
			],
			[
				'class' => \ant\behaviors\SerializableAttribute::class,
				'serializeMethod' => \ant\behaviors\SerializableAttribute::METHOD_JSON,
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
            [['order', 'size'], 'integer'],
            [['path', 'base_url', 'type', 'name'], 'string', 'max' => 255],
			[['model', 'model_class_id', 'model_id', 'caption', 'description', 'data', 'cropper'], 'safe'],
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
