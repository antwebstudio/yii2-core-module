<?php 
namespace ant\attributev2\assets;

use yii\web\AssetBundle;

class Attributev2Asset extends AssetBundle
{
	public $sourcePath = __DIR__ . '/src';
	public $js = [
		'js/jquery.attributev2.js'
	];
	public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        '\rmrevin\yii\fontawesome\AssetBundle',
        'yii\jui\JuiAsset',
    ];
}