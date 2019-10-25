<?php

namespace ant\file\backend\controllers;

use Yii;
use yii\web\Controller;
//use yii\data\ActiveDataProvider;
use common\modules\file\models\File;
use common\modules\file\models\Folder;
//use common\modules\file\models\UploadFileForm;

/**
 * Default controller for the `file` module
 */
class FileController extends Controller
{
	
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
		]);
    }
	
    public function actionDelete($id)
    {
		$model = File::findOne($id);
		
		if ($model->delete()) {
			return $this->redirect(['/file/folder/index', 'folderId' => $model->folder_id]);
		}
    }
}
