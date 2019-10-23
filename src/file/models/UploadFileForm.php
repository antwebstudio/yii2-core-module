<?php
namespace ant\file\models;

use common\helpers\StringHelper;
use ant\file\models\File;

class UploadFileForm extends \yii\base\Model {
	public $files;
	public $folderId;
	public $multiple;
	public $expireAt;
	public $expireTime = '23:59:59';
	
	public function rules() {
		return [
			[['files'], 'required'],
			[['files', 'expireAt'], 'safe'],
		];
	}
	
	public function upload() {
		if ($this->multiple) {
			foreach ($this->files as $file) {
				$this->_upload($file);
			}
		}
		return true;
	}
	
	public function _upload($post) {
		$file = new File;
		$file->attributes = [
			'file' => $post,
			'folder_id' => $this->folderId,
			'expire_at' => isset($this->expireAt) && !StringHelper::isEmpty($this->expireAt) ? $this->expireAt.' '.$this->expireTime : null,
		];
		if (!$file->save()) throw new \Exception(\yii\helpers\Html::errorSummary($file));
	}
}