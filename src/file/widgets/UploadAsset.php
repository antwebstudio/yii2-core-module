<?php
/**
 * Author: Eugine Terentev <eugine@terentev.net>
 */

namespace ant\file\widgets;

use yii\web\AssetBundle;

class UploadAsset extends AssetBundle
{

    public $depends = [
        'yii\web\JqueryAsset',
        //'yii\bootstrap\BootstrapAsset',
        'trntv\filekit\widget\BlueimpFileuploadAsset'
    ];

    public $sourcePath = __DIR__ . '/assets';
    
    public $css = [
        YII_DEBUG ? 'css/upload-kit.css' : 'css/upload-kit.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/cropper/4.1.0/cropper.min.css',
    ];

    public $js = [
        YII_DEBUG ? 'js/upload-kit.js' : 'js/upload-kit.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/cropper/4.1.0/cropper.min.js',
    ];
}
