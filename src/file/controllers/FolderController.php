<?php

namespace ant\file\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\modules\file\models\File;
use common\modules\file\models\Folder;

class FolderController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
	
	public function actionMyFile() {
		$userId = Yii::$app->user->id;
		
		$folder = Folder::findOne(['owner_id' => $userId]);
		
		$fileDataProvider = isset($folder) ? new ActiveDataProvider(['query' => File::find()->joinWith('fileStorageItem')->andWhere(['folder_id' => $folder->id])]) : null;
		
        return $this->render('my-file', [
			'userId' => $userId,
			'folder' => $folder,
			'fileDataProvider' => $fileDataProvider,
		]);
	}
}
