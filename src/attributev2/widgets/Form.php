<?php 
namespace ant\attributev2\widgets;

use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\builder\Form as Field;
use kartik\form\ActiveForm;
use ant\attributev2\components\FieldType;
use ant\attributev2\assets\Attributev2Asset;
use ant\attributev2\actions\AttributeV2Action;

class Form extends Widget
{
	public $url = null;
	public $editable = true;
	public $model = null;
	public $form = null;
	public $formOptions = [];

	public $emptyText = 'No fields found.';

	protected $_modal = null;
	protected $_formAttributes = null;

	public function init()
	{
		parent::init();
		Attributev2Asset::register($this->view);
	}

	public function run()
	{
		echo Html::beginTag('div', ['id' => $this->id, 'attributev2' => true]);
		echo $this->renderModalSettingForm();
		$this->form = ActiveForm::begin(ArrayHelper::merge(['id' => $this->id . '-form'], $this->formOptions));
		echo $this->renderFields();
		echo $this->renderSubmitButton();
		if ($this->editable) echo $this->renderSettingFormButton();
		ActiveForm::end();
		echo Html::endTag('div');

		if ($this->editable) $this->registerJs();
	}

	protected function registerJs()
	{
		$model = $this->model;
		$options = json_encode([
			'id' => $this->id,
			'modelClass' => $model::className(),
			'modalId' => $this->_modal->id,
			'formId' => $this->form->id,
			'addButton' => Html::tag('div', Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-xs']), ['class' => 'text-center']),
			'url' => Url::to([$this->url]),
			'urlAction' => [
				'postKey' => AttributeV2Action::ACTION_POST_KEY,
				'on' => [
					'create' 	=> AttributeV2Action::ACTION_ON_CREATE,
					'validate' 	=> AttributeV2Action::ACTION_ON_VALIDATE,
					'load' 		=> AttributeV2Action::ACTION_ON_LOAD,
				],
			],
			'defaultFieldtype' => FieldType::getDefaultFieldType(),
		]);
		$this->view->registerJs("$('#{$this->id}').attributev2($options);");
	}

	protected function getFormAttributes()
	{
		if ($this->_formAttributes === null)
		{
			$this->_formAttributes = [];

			foreach ($this->model->formAttributes as $attribute => $formAttribute) 
			{
				if (is_array($formAttribute)) {
					$this->_formAttributes[$attribute] = $formAttribute;
				} else {
					$this->_formAttributes[$attribute] = [
						'type' => Field::INPUT_RAW,
					];
				}

				$attribtueModels = $this->model->getDynamicAttribute()->attributeModels;
				if (isset($attribtueModels[$attribute]))
				{
					$attributeModel = $this->model->getDynamicAttribute()->attributeModels[$attribute];
					$this->_formAttributes[$attribute] = ArrayHelper::merge($this->_formAttributes[$attribute], [
						'fieldConfig' => [
							'template' => "{label}\n{input}\n{hint}\n{error}",
						],
						'options' => [
							'data-field' => json_encode([
								'name' => Html::getInputName($this->model, 'form[' . $attribute . ']'),
								'value' => [
									'name' 				=> $attributeModel->name,
									'label' 			=> $attributeModel->label,
									'fieldtype' 		=> $attributeModel->fieldtype,
									'fieldtype_setting' => $attributeModel->fieldtype_setting,
									'rules' 			=> $attributeModel->rules
								],
							]),
						],
					]); 
				}
			}
		}
		return $this->_formAttributes;
	}

	protected function udpateFieldBuilderForm($modal)
	{
		return Html::button('<i class="fa fa-cog"></i>', [
			'class' => 'btn btn-xs',
			'data' => [
				'target' => '#'.$this->_modal->id,
				'toggle' => 'modal',
				'modal' => $modal,
			],
			'encode' => false,
		]);
	}

	public function renderModalSettingForm()
	{
		$model = $this->model;
		ob_start();
		$items = [];
		$itemsOption = [];
		foreach (FieldType::getFieldTypes() as $name => $fieldtype) 
		{
			$url = Url::to([$this->url, 'fieldtype' => $fieldtype, 'modelClass' => $model::className()]);
			$items[$url] = $name;
			$itemsOption[$url] = ['fieldtype' => $fieldtype];
		}
		$this->_modal = Modal::begin(['id' => $this->id . '-modal', 'header' => '<h4 data-title></h4>']);
		echo Html::beginTag('div', ['class' => 'form-group']);
		echo Html::dropDownList('fieldtypes', null, $items, ['class' => 'form-control', 'data-fieldtypes' => true, 'options' => $itemsOption]);
		echo Html::endTag('div');
		echo '<div data-body-content></div>';
		Modal::end();
		return ob_get_clean();
	}

	public function renderEmpty()
	{
		return Html::tag('div', $this->emptyText);
	}

	public function renderFields()
	{
		return Field::widget([
			'model' => $this->model,
			'form' => $this->form,
			'attributes' => $this->formAttributes,
		]);
	}

	public function renderSubmitButton()
	{
		return Html::submitButton("Submit", ['class' => 'btn btn-default']);
	}

	public function renderSettingFormButton()
	{
		$model = $this->model;
		return Html::button("<i class='fa fa-plus'></i> Add Field", [
			'class' => 'btn btn-default', 
			'data' => [
				'target' => '#'.$this->_modal->id,
				'toggle' => 'modal',
				'setting-form' => [
					'url' => Url::to([$this->url, 'modelClass' => $model::className()]),
				],
			] 
		]);
	}
}