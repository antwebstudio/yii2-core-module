<?php

namespace ant\file\controllers;

use Yii;
use yii\web\Controller;
use elFinderVolumeFlysystem as Flysystem;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use ant\file\actions\ElFinderConnectorAction;

class ElfinderController extends Controller
{
    public function actions()
    {
        return [
            'connector' => [
                'class' => ElFinderConnectorAction::className(),
				'modelId' => Yii::$app->request->get('model_id'),
				'modelClassId' => Yii::$app->request->get('model_class_id'),
                'options' => [
                    'roots' => [
                        [
							'driver' => 'Flysystem', 
							'path' => '',
							'URL' => Yii::getAlias('@storageUrl/finder'), 
							'filesystem' => new Filesystem(new Local(Yii::getAlias('@storage/web/finder'))),
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
                'connectorRoute' => [
					'connector', 
					'model_class_id' => Yii::$app->request->get('model_class_id'),
					'model_id' => Yii::$app->request->get('model_id')
				],
            ],
        ];
    }
}