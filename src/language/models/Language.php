<?php
namespace ant\language\models;

use Yii;

class Language extends \lajax\translatemanager\models\Language {
	public static function getSelected() {
		return Yii::$app->request->post('language', Yii::$app->request->get('language', Yii::$app->language));
	}
}