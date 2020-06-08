<?php
namespace ant\language\assets;

class LocalizedString extends \yii\web\AssetBundle {
	public $sourcePath = __DIR__.'/assets/localized-string';
	
    public $js = [
		'LocalizedStrings.js',
    ];
}