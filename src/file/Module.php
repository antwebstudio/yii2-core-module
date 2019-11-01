<?php

namespace ant\file;

/**
 * file module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */

    /*public $custom = false;
    public $customWidth = null;
    public $customHeight = null;*/

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /* @params $file \League\Flysystem\File */
    public function resizeImage($file) {
        ini_set('memory_limit','256M');
        
        $img = \Intervention\Image\ImageManagerStatic::make($file->read());
        $height = $img->height() / $img->width() * 600;
        $img->resize(600, $height);
        
        $file->put($img->encode());
    }
}
