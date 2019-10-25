<?php

namespace ant\file\controllers;

use Yii;
use common\models\FileStorageItem;
use backend\models\search\FileStorageItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Intervention\Image\ImageManagerStatic;

/**
 * FileStorageController implements the CRUD actions for FileStorageItem model.
 */
class FileStorageItemController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'upload-delete' => ['delete']
                ]
            ]
        ];
    }

    protected function resizeWithAspectRatio($event, $maxWidth = 1024, $maxHeight = 768) {
        $file = $event->file;
        $img = ImageManagerStatic::make($file->read());
        $imgSize = getimagesize(\Yii::getAlias('@storage') . '/web/source' . '/'.$event->path);
        $width = $imgSize[0];
        $height = $imgSize[1];
        $resize = true;

        //long
        if ($width > $height) {
            $height = null;
            if ($width > $maxWidth) {
                $width = $maxWidth;
            }
        //tall
        } elseif ($height > $width) {
            $width = null;
            if ($height > $maxHeight) {
                $height = $maxHeight;
            }
        } else {
        //square
            if ($width > $maxWidth) {
                $width = $maxWidth;
            } elseif ($height > $maxHeight) {
                $height = $maxHeight;
            } else {
                $resize = false;
            }
        }

        if ($resize) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $width = $img->getWidth();
            $height = $img->getHeight();
        }
        $file->put($img->encode());
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                // 'validationRules' => [
                //     [['file'], function ($attribute, $params, $validator) {
                //         print_r($_FILES);
                //         die;
                //     }],
                // ],
                'deleteRoute' => 'upload-delete',
                'on afterSave' => function ($event) {
                    $get = Yii::$app->request->get();
                    if(isset($get['width']) && isset($get['height'])){
                        $maxWidth = $get['width'];
                        $maxHeight = $get['height'];
                        $this->resizeWithAspectRatio($event, $maxWidth, $maxHeight);
                    } else {
                        if (substr($event->file->getMimetype(), 0, strlen('image')) == 'image') {                    
                            $this->module->resizeImage($event->file, 'default');
                        }
                    }
                }
            ],
            'upload-delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction'
            ],
            'upload-imperavi' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'fileparam' => 'file',
                'responseUrlParam'=> 'filelink',
                'multiple' => false,
                'disableCsrf' => true
            ]
        ];
    }
}
