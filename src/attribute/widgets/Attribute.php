<?php  
namespace ant\attribute\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

use unclead\multipleinput\MultipleInput;

use ant\attribute\models\Attribute as AttributeModel;

class Attribute extends InputWidget
{
	public $tester;
	public $lengthLimit = 50;
	
	protected $_modal;
	protected $_data;
	protected $_newAttributes 	= [];
	protected $_savedAttributes = [];

	public function init()
	{
		parent::init();

		// permenent remove default label
		$this->field->label(false);

		$this->view->registerCss('
			#' . $this->id . ' .table tr td { vertical-align: middle; border-top: 0px;}
			#' . $this->id . ' .table tr td:first-child { width: 20%; padding-left: 0px;}
			#' . $this->id . ' .table tr td:last-child { text-align: right; width: 1%; padding-right: 0px; }
			#' . $this->id . ' .table tr td[colspan] { width: auto;}
			#' . $this->id . ' .table.multiple-input-list thead { display: none;}
			#' . $this->id . ' .modal-body { font-size: initial; word-wrap: break-word; white-space: normal;}
		');

		$this->name = Html::getInputName($this->model, $this->attribute);

		$attributes = isset($this->model->{$this->attribute}) ? $this->model->{$this->attribute} : [];

		foreach ($attributes as $key => $attribute) 
		{
			if (is_numeric($key)) {
				$this->_newAttributes[$key] = $attribute;
			} else {
				$this->_savedAttributes[$key] = $attribute;
			}
		}
	}

	public function run()
	{
		ob_start();
		$this->_modal = Modal::begin([
			'header' => '<h4 data-title></h4>',
			//'toggleButton' => ['label' => (strlen($result) > $this->lengthLimit) ? substr($result, 0, $this->lengthLimit - 3) . '...' : $result, 'class' => 'btn'],
		]);
			echo '<div data-body-content></div>';
		Modal::end();
		$modal = ob_get_clean();
		// make modal outside the current form, prevent nested form happen.
		$this->view->registerJs('jQuery(function($) { $("body").append(\'' . \common\helpers\ScriptHelper::jsOneLineString($modal) . '\') });');

		$html = '';

		// default empty value
		$html .= Html::activeHiddenInput($this->model, $this->attribute, ['value' => '']);

		$html .= Html::beginTag('div', ['id' => $this->id]);

		// attribute param setting form
		/* if ($this->getAttributeParams())*/ 
		//html .= $this->getAttributeParamsForm();

		$html .= Html::beginTag('table', ['class' => 'table table-condensed table-renderer', 'style' => 'margin-bottom: 0px;']);

		$html .= Html::beginTag('tbody');

		foreach ($this->_savedAttributes as $key => $attribute) 
		{
			$name 	= $this->model->{$this->attribute}[$key][AttributeModel::FIELD_NAME];
			
			$value 	= $this->model->{$this->attribute}[$key][AttributeModel::FIELD_VALUE];

			$html .= Html::beginTag('tr');

			// Cell 1
			$html .= Html::tag('td', $name);

			// Cell 2
			$html .= Html::beginTag('td');
			$html .= $this->renderRowInput($name);
			$html .= Html::endTag('td');

			// Cell 3
			$html .= Html::beginTag('td');
			$html .= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger btn-sm', 'onClick' => 'return confirm("Are you sure?") ? $(this).closest("tr").fadeOut(500, function(){$(this).remove();}) : false;']);
			$html .= Html::endTag('td');

			$html .= Html::endTag('tr');
		}
		
		$this->view->registerJs('jQuery(function($) { $("[data-toggle=modal]").on("click", function() {
			var $modal = $($(this).attr("data-target"));
			$modal.find("[data-title]").text($(this).attr("data-title"));
			$modal.find("[data-body-content]").html($(this).attr("data-body-content"));
		}); });');


		$html .= Html::endTag('tbody');

		$html .= Html::endTag('table');

		$html .= MultipleInput::widget([
			'name' => $this->name,
			'addButtonPosition' => MultipleInput::POS_FOOTER,
			'allowEmptyList' => true,
			'data' => $this->_newAttributes,
			'addButtonOptions' => [
				'class' => 'btn btn-default btn-sm'
			],
			'removeButtonOptions' => [
				'class' => 'btn btn-danger btn-sm',
				'label' => '<i class="fa fa-trash"></i>'
			],
			'columns' => [
				[
					'name' => 'name',
					'options' => ['placeholder' => 'Name']
				],
				[
					'name' => 'setting',
					'options' => ['placeholder' => 'Setting']
				],
			]
		]);

		$html .= Html::endTag('div');

		return $html;
	}
	
	protected function renderRowValue($name) {
		return isset($this->tester) ? $this->tester->renderParsedAttributeValue($name) : '';
	}
	
	protected function renderRowInput($name) {
		$result = $this->renderRowValue($name);
		
		$html = '';
		$inputName = $this->attribute . '[' . $name . ']';
		
		$label = Html::button(strlen($result) > $this->lengthLimit ? substr($result, 0, $this->lengthLimit - 3) . '...' : $result, [
			'class' => 'btn',
			'data-target' => '#'.$this->_modal->id,
			'data-toggle' => 'modal',
			'data-title' => $name,
			'data-body-content' => $result,
		]);

		if (strlen($result) > 0) $html .= Html::beginTag('div', ['class' => 'input-group']);

		$html .= Html::activeInput('text', $this->model, $inputName.'[' . AttributeModel::FIELD_VALUE . ']', [
			'class' => 'form-control',
			'placeholder' => $name,
		]);
		$html .= Html::activeHiddenInput($this->model, $inputName.'[' . AttributeModel::FIELD_NAME . ']');

		if (strlen($result) > 0) 
		{
			$html .= Html::tag('span', $label, ['class' => 'input-group-btn']);

			$html .= Html::endTag('div');
		}
		return $html;
	}
}