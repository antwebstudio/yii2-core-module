<?php

namespace ant\sandbox\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * Default controller for the `payment` module
 */
class DefaultController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);

    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($sandbox, $requery = false)
    {
		$sandbox = Yii::$app->sandbox->getSandbox($sandbox, [
			'receiverName' => $requery ? 'requery' : 'default',
		]);
		$sandbox->process(\Yii::$app->request);
		
		$model = $sandbox->receiver;
		
		if (\Yii::$app->request->post('submit')) {
			// Pay button is clicked
			$job = new \ant\sandbox\gateway\ipay88\BackendJob([
				'url' => $model->backendUrl, 
				'params' => Yii::$app->request->post(),
			]);
			Yii::$app->queue->push($job);
			//Yii::$app->queue->push($job);
			
			//$job->execute(null);
		}
		
		if ($sandbox->receiver->isCustomResponse) {
			return $sandbox->receiver->response();
		} else {
			return $this->render('index', [
				'sandbox' => $sandbox,
				'model' => $model,
				'isValid' => $sandbox->receiver->validate(),
			]);
		}
	}
	
	public function actionBackend() {
		return $this->render($this->action->id, [
			
		]);
	}
}
