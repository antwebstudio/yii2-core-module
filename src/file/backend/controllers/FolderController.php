<?php

namespace ant\file\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\modules\file\models\File;
use common\modules\file\models\Folder;
use common\modules\file\models\UploadFileForm;

/**
 * Default controller for the `file` module
 */
class FolderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                //'deleteRoute' => 'my-custom-delete', // my custom delete action for deleting just uploaded files(not yet saved)
                //'fileStorage' => 'myfileStorage', // my custom fileStorage from configuration
                'multiple' => true,
                'disableCsrf' => true,
                'responseFormat' => \yii\web\Response::FORMAT_JSON,
                'responsePathParam' => 'path',
                'responseBaseUrlParam' => 'base_url',
                'responseUrlParam' => 'url',
                'responseDeleteUrlParam' => 'delete_url',
                'responseMimeTypeParam' => 'type',
                'responseNameParam' => 'name',
                'responseSizeParam' => 'size',
                'deleteRoute' => 'delete',
                'fileStorage' => 'fileStorage', // Yii::$app->get('fileStorage')
                'fileStorageParam' => 'fileStorage', // ?fileStorage=someStorageComponent
                'sessionKey' => '_uploadedFiles',
                'allowChangeFilestorage' => false,
            ],
            'delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction',
            ],
            'view' => [
                'class' => 'trntv\filekit\actions\ViewAction',
            ]
        ];
    }
	
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($userId = null, $folderId = null)
    {
		
		if (isset($folderId)) {
			$folder = Folder::findOne($folderId);
			$userId = $folder->owner_id;
		} else {
			$folder = $this->getFolderForUser($userId);
		}
		
		$fileDataProvider = isset($folder) ? new ActiveDataProvider(['query' => File::find()->joinWith('fileStorageItem')->andWhere(['folder_id' => $folder->id])]) : null;
		
		$model = new UploadFileForm(['folderId' => isset($folder) ? $folder->id : null, 'multiple' => true]);
		
		if (Yii::$app->request->post('submit-type') != 'select-user' && $model->load(Yii::$app->request->post()) && $model->upload()) {
			return $this->refresh();
		}
		
        return $this->render('index', [
			'userId' => $userId,
			'folder' => $folder,
			'fileDataProvider' => $fileDataProvider,
			'model' => $model,
			'users' => \yii\helpers\ArrayHelper::map(\common\modules\user\models\User::find()->all(), 'id', 'username'),
		]);
    }
	
	protected function getFolderForUser($userId) {
		if (!isset($userId)) return null;
		
		$folder = Folder::findOne(['owner_id' => $userId]);
		if (!isset($folder)) {
			$folder = new Folder;
			$folder->attributes = [
				'name' => 'user-'.$userId,
				'owner_id' => $userId,
			];
			
			if (!$folder->save()) throw new \Exceptions('Failed to create folder for user. ');
		}
		return $folder;
	}
}
