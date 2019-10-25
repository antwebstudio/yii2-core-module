<?php

namespace ant\file\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\modules\file\models\File;
use common\modules\file\models\Folder;

class FileController extends Controller
{
	public function behaviors()
    {
		return [
			'access' =>
			[
				'class' => \common\rbac\ModelAccessControl::className(),
			],
			'verbs' =>
			[
				'class' => \yii\filters\VerbFilter::className(),
				'actions' =>
				[
					'delete' => ['POST'],
				],
			],
		];
    }
	
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
	
	public function actionDownload($id) {
		$file = File::findOne($id);
		$this->checkAccess('download', $file);
		
		if (!$file->isExpired) {
			Yii::$app->response->sendFile($file->path, $file->filename, ['inline' => true]);
		} else {
			return $this->render('download', [
				
			]);
		}
	}
}
