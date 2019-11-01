<?php
namespace ant\support\actions;
use Yii;
use ant\support\models\ContactForm;

class CreateAction extends \yii\base\Action {
	public function run() {
		
        $model = new ContactForm();
		
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save() /*$model->sendEmail(Yii::$app->params['adminEmail'])*/) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }
			
			if (Yii::$app->request->isAjax) {
				return $this->controller->renderAjax('contact', ['model' => new ContactForm]);
			}
			return $this->controller->redirect(isset(Yii::$app->request->referrer) ? Yii::$app->request->referrer : ['contact', '#' => 'contact-form']);
        }
		
		return $this->controller->render('contact', [
			'model' => $model,
		]);
	}
}