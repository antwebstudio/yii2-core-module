<?php 
namespace ant\attributev2\actions;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use kartik\builder\Form;
use kartik\form\ActiveForm;
use ant\attributev2\actions\BaseAction;
use ant\attributev2\models\Attributev2;
use ant\attributev2\components\FieldType;

class AttributeV2Action extends BaseAction
{
	const ACTION_POST_KEY 	= 'action';

	const ACTION_ON_CREATE 		= 'create';
	const ACTION_ON_VALIDATE 	= 'validate';
	const ACTION_ON_LOAD 		= 'load';

	protected $_model = null;

	public function run($modelClass, $name = null, $fieldtype = null)
	{
		$attributeModel = $this->getAttributeModel($modelClass, $name);
		if (!($fieldtype === null)) $attributeModel->fieldtype = $fieldtype;

		if (Yii::$app->request->isAjax)
		{
			Yii::$app->response->format = Response::FORMAT_JSON;

			if (Yii::$app->request->get(self::ACTION_POST_KEY)) {
				return $this->actionHandler(Yii::$app->request->get(self::ACTION_POST_KEY), $attributeModel);
			} else {
				return $this->controller->renderAjax('index', ['attributeModel' => $attributeModel]);
			}
		}
	}

	protected function getActions()
	{
		return [
			self::ACTION_ON_CREATE		=> 'actionCreate',
			self::ACTION_ON_VALIDATE	=> 'actionValidate',
			self::ACTION_ON_LOAD		=> 'actionLoad',
		];
	}

	protected function actionHandler($action, $attributeModel)
	{
		if (isset($this->actions[$action])) {
			return call_user_func_array([$this, $this->actions[$action]], [$attributeModel]);
		} else {
			throw new ForbiddenHttpException('Invalid action "' . $action . '"" provided.');
		}
	}

	protected function actionValidate($attributeModel)
	{
		if ($attributeModel->load(Yii::$app->request->post())) {
			return ActiveForm::validate($attributeModel);
		} else {
			return null;
		}
	}

	protected function actionLoad($attributeModel)
	{
		$attributeModel->load(Yii::$app->request->post());
		return $this->controller->renderAjax('index', ['attributeModel' => $attributeModel]);
	}

	protected function actionCreate($attributeModel)
	{
		if ($attributeModel->load(Yii::$app->request->post()))
		{
			$modelClass = $attributeModel->model;
			$ownerModel = new $modelClass();
			$ownerModel->getDynamicAttribute()->addTempAttributeModel($attributeModel->name, $attributeModel);
			return ['success' => true, 'response' => [
				'html' => Form::widget([
				    'model' => $ownerModel,
				    'form' => new ActiveForm,
				    'attributes' => [
				        $attributeModel->name => ArrayHelper::merge($attributeModel->fieldtype()->frontendInput, [
				        	'options' => [
								'data-field' => json_encode([
									'name' => Html::getInputName($ownerModel, 'form[' . $attributeModel->name . ']'),
									'value' => [
										'name' 				=> $attributeModel->name,
										'label' 			=> $attributeModel->label,
										'fieldtype' 		=> $attributeModel->fieldtype,
										'fieldtype_setting' => $attributeModel->fieldtype_setting,
										'rules' 			=> $attributeModel->rules,
										'label' 			=> $attributeModel->label,
									],
								]),
							],
					    ]),
				    ],
				]),
			]];
		} else {
			return null;
		}
	}

	protected function getAttributeModel($modelClass, $name = null, $fieldtype = null)
	{
		if ($this->_model === null)
		{
			$query = Attributev2::find()
				->andWhere(['model' => $modelClass])
				->andWhere(compact('name'));

			if ($name === null || !$query->exists()) {
				$this->_model = new Attributev2([
					'model' => $modelClass,
					'name' => $name,
					'fieldtype' => $fieldtype === null ? $fieldtype : FieldType::getDefaultFieldType(),
					'format' => Attributev2::FORMAT_SETTING_FORM,
				]);
			} else {
				$findQuery = clone $query;
				$this->_model = $findQuery->one();
				$this->_model->prepareForSettingForm();
			}
		}
		return $this->_model;
	}
}