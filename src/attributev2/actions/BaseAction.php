<?php 
namespace ant\attributev2\actions;

use yii\base\Action;

class BaseAction extends Action
{
	public function init()
	{
		parent::init();
		$this->controller->viewPath = dirname(__DIR__) . '/views/';
	}
}