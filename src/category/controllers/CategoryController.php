<?php
namespace ant\category\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;

use yii\data\ActiveDataProvider;

use common\rbac\ModelAccessControl;
use common\modules\category\models\Category;
use common\modules\category\models\CategoryType;
use common\modules\category\models\CategorySearch;

class CategoryController extends Controller
{
    public function behaviors()
    {
        return
        [
            'access' =>
            [
                'class' => ModelAccessControl::className(),
            ],
        ];
    }

    public function actionIndex($type = CategoryType::DEFAULT_NAME, $id = null) {
		$model = Category::findOne($id);
        $searchModel = new CategorySearch(['type_id' => CategoryType::getIdFor($type), 'parent' => isset($id) ? $id : Category::find()->rootsOfType($type)->one()]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->active();
		
        return $this->render($this->action->id, [
			'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }	

    public function actionView($id = null)
    {
        
    }
}
