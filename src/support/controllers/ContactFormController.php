<?php 
namespace ant\support\controllers;

use Yii;

class ContactFormController extends \yii\web\Controller {
	public function actionCreate() {
		$model = $this->module->getFormModel('contact');
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
				return ['success' => true];
			}
		}
		
		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return ['errors' => \yii\widgets\ActiveForm::validate($model)];
		}
		
		return $this->render($this->action->id, [
			'model' => $model,
		]);
	}
}