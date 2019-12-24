<?php

namespace ant\file\controllers;

use alexantr\elfinder\ConnectorAction;
use Yii;
use yii\web\Controller;
use elFinderVolumeFlysystem as Flysystem;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class ElfinderController extends Controller
{
    public function actions()
    {
        return [
            'connector' => [
                'class' => ConnectorAction::className(),
                'options' => [
                    'roots' => [
                        [
							'driver' => 'Flysystem', 
							'path' => 'images',
							'URL' => Yii::getAlias('@storageUrl/elfinder/images'), 
							'filesystem' => new Filesystem(new Local(Yii::getAlias('@storage/elfinder'))),
							'cache' => 'session', // 'session', 'memory' or false
						],
                    ],
                ],
            ],
            /*'input' => [
                'class' => \alexantr\elfinder\InputFileAction::className(),
                'connectorRoute' => 'connector',
            ],
            'ckeditor' => [
                'class' => \alexantr\elfinder\CKEditorAction::className(),
                'connectorRoute' => 'connector',
            ],*/
            'tinymce' => [
                'class' => \ant\file\adapters\actions\TinyMce5::className(),
                'connectorRoute' => 'connector',
            ],
        ];
    }
}