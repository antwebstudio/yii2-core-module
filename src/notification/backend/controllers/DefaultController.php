<?php
namespace ant\notification\backend\controllers;

use Yii;

class DefaultController extends \yii\web\Controller
{
	public $mailLayout;
	
    public function actionTest($class = null)
    {
		$user = \ant\user\models\User::findOne(9);
		$notification = new \ant\user\notifications\SignupWelcome($user);
		
		if (Yii::$app->request->isPost) {
			Yii::$app->notifier->send($user, $notification);
			Yii::$app->session->setFlash('success', 'Sent succesfully. ');
			return $this->refresh();
		}
		
		$this->layout = 'mail';
		$this->mailLayout = '@project/mails/layouts/html';
		
		$message = $notification->exportForMail();
		return $this->render($this->action->id, [
			'message' => $message,
		]);
    }
}
