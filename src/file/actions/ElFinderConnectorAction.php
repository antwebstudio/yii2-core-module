<?php

namespace ant\file\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\UploadedFile;
use ant\file\models\FileStorageItem;
use ant\file\models\FileAttachment;

/**
 * Connector action
 */
class ElFinderConnectorAction extends Action
{
	public $component = 'elfinder';
	public $modelId;
	public $modelClassId;
    /**
     * @var array elFinder connector options
     */
    public $options = [];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return (new \ant\file\adapters\ElFinderConnector(new \elFinder($this->options)))->run([$this, 'callback']);
    }
	
	public function callback($cmd, $args, $result, $elfinder) {
		if (trim($cmd) == '') throw new \Exception('Invalid cmd. ');
		
		$callback = $cmd.'Callback';
		if ($this->hasMethod($callback)) {
			$this->{$callback}($args, $result, $elfinder);
		} else {
			if (isset($result['added'])) {
				$this->processAdded($result['added'], $elfinder);
			}
			if (isset($result['removed'])) {
				$this->processRemoved($result['removed'], $elfinder);
			}
			if (isset($result['updated'])) {
				$this->processUpdated($result['updated'], $elfinder);
			}
		}
	}
	
	protected function rmCallback($args, $result, $elfinder) {
		
		if (isset($result['removed'])) {
			$this->processRemoved($result['removed'], $elfinder);
		}
	}
	
	protected function uploadCallback($args, $result, $elfinder) {
        /*
		$uploadedFiles = UploadedFile::getInstancesByName('upload');
		
        foreach ($uploadedFiles as $uploadedFile) {
            if ($uploadedFile->error === UPLOAD_ERR_OK) {
				
				$path = $this->getFileStorage()->save($uploadedFile, false, false, [], $uploadPath);
				//throw new \Exception($path);
			}
		}
		*/
		
		if (isset($result['added'])) {
			$this->processAdded($result['added'], $elfinder);
		}
		if (isset($result['removed'])) {
			$this->processRemoved($result['removed'], $elfinder);
		}
		if (isset($result['updated'])) {
			$this->processUpdated($result['updated'], $elfinder);
		}
	}
	
	protected function processAdded($added, $elfinder) {
		$errors = [];
		foreach ($added as $file) {
			if ($file['mime'] != 'directory') {
				$volume = $elfinder->getVolume($file['phash']);
				$options = $volume->options($file['phash']);
				$baseUrl = $options['url'];
			
				$fileItem = new FileAttachment;
				$fileItem->attributes = [
					//'component' => $this->component,
					'base_url' => $baseUrl,
					'path' => $volume->getPath($file['hash']),
					'name' => $file['name'],
					'size' => $file['size'],
					'type' => $file['mime'],
					'model_id' => $this->modelId,
					'model_class_id' => $this->modelClassId,
				];
				
				/*$fileItem = new FileStorageItem;
				$fileItem->attributes = [
					'component' => $this->component,
					'base_url' => $baseUrl,
					'path' => $volume->getPath($file['hash']),
					'name' => $file['name'],
					'size' => $file['size'],
					'type' => $file['mime'],
				];*/
				
				if (!$fileItem->save()) $errors[$path] = $fileItem->errors;
			}
		}
		if (count($errors)) throw new \Exception(print_r($errors, 1));
	}
	
	protected function processUpdated($updated, $elfinder) {
		\Yii::error(print_r($updated,1).'uuu');
		$errors = [];
		foreach ($updated as $file) {
			if ($file['mime'] != 'directory') {
				$volume = $elfinder->getVolume($file);
				$options = $volume->options($file['phash']);
				$baseUrl = $options['url'];
				$path = $volume->getPath($file['hash']);
				
				$fileItem = FileAttachment::findOne([
					'path' => $volume->getPath($file),
					'model_class_id' => $this->modelClassId,
				]);
				$fileItem->attributes = [
					'base_url' => $baseUrl,
					'path' => $path,
					'name' => $file['name'],
					'size' => $file['size'],
					'type' => $file['mime'],
					'model_id' => $this->modelId,
					'model_class_id' => $this->modelClassId,
				];
				if (!$fileItem->save()) $errors[$path] = $fileItem->errors;
			}
		}
		if (count($errors)) throw new \Exception(print_r($errors, 1));
	}
	
	protected function processRemoved($removed, $elfinder) {
		foreach ($removed as $file) {
			if (!isset($file['mime']) || $file['mime'] != 'directory') {
				$volume = $elfinder->getVolume($file);
				
				FileAttachment::deleteAll([
					//'component' => $this->component,
					'path' => $volume->getPath($file),
					'model_class_id' => $this->modelClassId,
				]);
			}
		}
	}
	
    protected function getFileStorage()
    {
		$fileStorage = 'fileStorage';
        $fileStorage = \yii\di\Instance::ensure($fileStorage, \trntv\filekit\Storage::className());
        return $fileStorage;
    }
	
	protected function getProperty($object, $propertyName) {
		$class = new \ReflectionClass($object);
		$property = $class->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->getValue($object);
	}
}
