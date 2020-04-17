<?php
namespace ant\language\controllers;

class DefaultController extends \yii\web\Controller {
	public function actionLanguage($language, $redirect = null) {
		\Yii::$app->session->set(\ant\language\Module::SESSION_LANGUAGE, $language);
		$redirect = isset($redirect) ? $redirect : \Yii::$app->request->referrer;
		return $this->redirect($redirect ?: \Yii::$app->homeUrl);
	}
}