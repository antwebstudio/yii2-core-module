<?php

namespace ant\category\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use ant\category\models\Category;
use ant\category\models\CategoryType;
use ant\category\models\CategoryAttachment;
use ant\category\models\CategorySearch;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return
        [
            'image-upload' =>
            [
                'class' => UploadAction::className(),
                'deleteRoute' => 'image-delete',
                'on afterSave' => function ($event)
                {
                    $attachmentType = \Yii::$app->request->get('type');
                    $categoryType = \Yii::$app->request->get('category-type');
                    $file = $event->file;
					$sizeConfig = Yii::$app->getModule('category')->getCategoryImageConfig($attachmentType, $categoryType);
					
					if (isset($sizeConfig)) {
						$method = isset($sizeConfig[0]) ? $sizeConfig[0] : 'resize';
						
						$img = \Intervention\Image\ImageManagerStatic::make($file->read());
						$method = array_shift($sizeConfig);
						
						call_user_func_array([$img, $method], $sizeConfig);
						$file->put($img->encode());
					}
                }
            ],
            'image-delete' =>
            [
                'class' => DeleteAction::className()
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex($type = 'default')
    {
		$categoryType = CategoryType::findOne(['name' => $type]);
        $searchModel = new CategorySearch(['type' => CategoryType::getIdFor($type)]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;

        return $this->render('index', [
            'type' => $type,
			'categoryType' => $categoryType,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'showTypeField' => Yii::$app->request->get('showType', !isset($type)),
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type = 'default') // Should be null, or else the showTypeField will have invalid data
    {
		$root = Category::ensureRoot($type);
		
		$categoryType = CategoryType::findOne(['name' => $type]);
		$categoryTypes = CategoryType::find()->all();
		
        $model = new Category([ 'type_id' => CategoryType::getIdFor($type) ]);

        if ($model->load(Yii::$app->request->post()) && $model->appendTo($root)) {
			Yii::$app->session->setFlash('success', Yii::t('category', 'New {categoryType} is added. ', [
				'categoryType' => lcfirst(isset($categoryType->title) ? $categoryType->title : 'category'),
			]));
            return $this->redirect(['index', 'type' => $type]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'showTypeField' => Yii::$app->request->get('showType', !isset($type)),
				'categoryTypes' => ArrayHelper::map($categoryTypes, 'id', 'name'),
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		// Category type should not be formModel name
		//$this->module->configureModel($model, $model->type);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('category', '{categoryType} is updated. ', [
				'categoryType' => ucfirst(isset($model->type->title) ? $model->type->title : 'category'),
			]));
            return $this->redirect(['index', 'type' => $model->type->name]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'showTypeField' => Yii::$app->request->get('showType', !isset($model->type)),
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index', 'type' => $model->type]);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
