<?php
namespace ant\language\controllers;

class DefaultController extends \yii\web\Controller {
	public function actionLanguage($language) {
		\Yii::$app->session->set(\ant\language\Module::SESSION_LANGUAGE, $language);
		return $this->redirect(\Yii::$app->request->referrer ?: \Yii::$app->homeUrl);
	}
}