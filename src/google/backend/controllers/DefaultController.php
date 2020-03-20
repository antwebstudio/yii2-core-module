<?php
namespace ant\google\backend\controllers;

use Yii;

class DefaultController extends \yii\web\Controller {
	public function actionIndex() {
		return $this->render($this->action->id, [
		]);
	}
	
	public function actionOauth($code = null) {
		if (Yii::$app->google->isAuthenticated) {
			return $this->redirect('index');
		} else if (isset($code)) {
			Yii::$app->google->authenticate($code);
			return $this->redirect(Yii::$app->google->redirectUrl);
		} else {
			Yii::$app->google->authorize();
		}
		return $this->redirect('index');
	}
	
	public function actionLogoutOauth() {
		Yii::$app->google->logout();
		return $this->redirect(isset(Yii::$app->request->referrer) ? Yii::$app->request->referrer : ['index']);
	}
}