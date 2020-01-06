<?php
namespace ant\tag\controllers;

use ant\tag\models\Tag;

class TagController extends \yii\web\Controller {
	public function actionView($id) {
		$model = Tag::findOne($id);
		
		return $this->render($this->action->id, [
			'model' => $model,
		]);
	}
}