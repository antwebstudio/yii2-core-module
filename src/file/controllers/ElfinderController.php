<?php

namespace ant\file\controllers;

use Yii;
use yii\web\Controller;
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
					'bind' => [
						'upload.presave' => [
							'Plugin.AutoResize.onUpLoadPreSave'
						]
					],
					'plugin' => [
						'AutoResize' => [
							'enable'         => true,       // For control by volume driver
							'maxWidth'       => 1024,       // Path to Water mark image
							//'maxHeight'      => 1024,       // Margin right pixel
							'quality'        => 95,         // JPEG image save quality
							//'preserveExif'   => false,      // Preserve EXIF data (Imagick only)
							//'forceEffect'    => false,      // For change quality or make progressive JPEG of small images
							//'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
							//'offDropWith'    => null,       // Enabled by default. To disable it if it is dropped with pressing the meta key
															// Alt: 8, Ctrl: 4, Meta: 2, Shift: 1 - sum of each value
															// In case of using any key, specify it as an array
							//'onDropWith'     => null        // Disabled by default. To enable it if it is dropped with pressing the meta key
															// Alt: 8, Ctrl: 4, Meta: 2, Shift: 1 - sum of each value
															// In case of using any key, specify it as an array
						],
					],
                    'roots' => [
                        [
							'driver' => '\ElFinderFlysystemVolume', 
							'path' => '',
							'URL' => Yii::getAlias('@storageUrl/finder'), 
							'tmbURL' => Yii::getAlias('@frontendUrl/assets/.elfinder_tmb'), 
							'tmbPath' => Yii::getAlias('@frontend/assets/.elfinder_tmb'),
							'tmbSize' => 200,
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