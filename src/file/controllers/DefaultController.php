<?php

namespace ant\file\controllers;

use yii\web\Controller;

/**
 * Default controller for the `file` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
	
	public function actionFile($path, $name) {
		$root = \Yii::getAlias('@storage/web/source');
		return \Yii::$app->response->sendFile($root.$path, $name, ['inline' => true]);
	}
}
