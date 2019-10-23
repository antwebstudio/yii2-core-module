<?php
namespace ant\file\behaviors;

use common\helpers\File;

class UploadBehavior extends \trntv\filekit\behaviors\UploadBehavior {
	public function duplicateUploadedFile() {
		$this->owner->setOldAttribute($this->getAttributeField('path'), null); // This line needed to avoid the old image (image of source of duplication) is deleted.
		
		$path = $this->owner->{$this->attribute}['path'];
		
		$file = File::createFromPath($path);
		$file->setFilename(md5(microtime(true).$file->getFilename(false)));
		$copied = $file->getPath();
		
		if ($this->getStorage()->getFilesystem()->copy($path, $copied)) {
			$this->owner->{$this->attribute}['path'] = $copied;
			$this->owner->setAttribute($this->getAttributeField('path'), $copied);
		}
	}
}