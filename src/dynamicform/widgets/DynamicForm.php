<?php
namespace ant\dynamicform\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\web\View;
use yii\helpers\ArrayHelper;
use ant\dynamicform\base\FieldTypes;

class DynamicForm extends Widget
{

	public $fieldNamePrefix;

	public $url;

	public $form;

	public $model;

	public function init()
	{
		parent::init();
	}

	public function getFields() {
		return $this->model->getDynamicFields();
	}

	public function run()
	{
		return $this->render('dynamic-form', [
			'url' => $this->url,
			'form' => $this->form,
			//'model' => $this->model,
			'fieldNamePrefix' => $this->fieldNamePrefix,
		]);
	}
}
?>
