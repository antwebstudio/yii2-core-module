<?php
namespace ant\file\behaviors;
use yii\base\Behavior;
use yii\imagine\Image;

class ThumbnailBehavior extends Behavior {
	public $webSource = '/web/source';
	public $thumbnailPrefix = '_thumb.';
	public $stringBetweenBaseAndFileName = '/1/';

	public function getThumbnailPath($path){
    	$filePath = \Yii::getAlias('@storage') . $this->webSource . $path;
        $path_parts = pathinfo($filePath);
    	$pathBeforeFileName = $this->getPathBeforeFileName($path);
        $thumbnailFileName = $path_parts['filename'] . $this->thumbnailPrefix . $path_parts['extension'];
        return $thumbNailPath = \Yii::getAlias('@storage') . $this->webSource. $pathBeforeFileName . $thumbnailFileName;
    }

    public function getThumbnailUrlPath($baseUrl, $path){
    	$filePath = \Yii::getAlias('@storage') . $this->webSource . $path;
        $path_parts = pathinfo($filePath);
    	$pathBeforeFileName = $this->getPathBeforeFileName($path);
        $thumbnailFileName = $path_parts['filename'] . $this->thumbnailPrefix . $path_parts['extension'];
        return $baseUrl . $this->stringBetweenBaseAndFileName . $thumbnailFileName;
    }
	
	public function saveAsThumbnail($path, $width = 200, $height = 200){

        $filePath = \Yii::getAlias('@storage') . $this->webSource . $path;
        $thumbNailPath = $this->getThumbnailPath($path);
        if (!file_exists($thumbNailPath)) {
	        copy($filePath, $thumbNailPath);
	        Image::thumbnail(
	                $thumbNailPath,
	                $width, $height, 'inset'
	        )->save($thumbNailPath);
        }
        return $thumbNailPath;
    }

    public function addWaterMark($image, $watermark, array $start =[0, 0], $saveLocation = null){
    	return Image::watermark($image, $watermark, $start)->save($saveLocation == null ? $saveLocation : $image);
    }

    protected function getPathBeforeFileName($path){
        $partsOfPath = explode('/', str_replace('\\', '/',$path) );
        $pathBeforeFileName = '';
        foreach ($partsOfPath as $key => $value) {
            if ($value != end($partsOfPath)) {
                $pathBeforeFileName .= $value . '/';
            }
        }
        return $pathBeforeFileName;
    }
}