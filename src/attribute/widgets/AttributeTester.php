<?php  
namespace ant\attribute\widgets;

use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use ant\widgets\Modal;
use ant\attribute\exceptions\ParseException;

class AttributeTester extends \yii\base\Widget {
	const SESSION_PARAMS_NAME = 'attribute_session';
	
	public $model;
	
	protected $_modal;
	protected $_attributeParamsFormModel;
	protected $_savedAttributes = [];
	
	public function init() {
		
		$this->getAttributeParamsFormModel()->defineAttribute('form', '');
		
		if ($this->getAttributeParams()) {
			$this->getAttributeParamsFormModel()->setAttributes($this->getAttributeParams());
		}

		if ($this->getAttributeParamsFormModel()->load(Yii::$app->request->post())) 
		{
			$this->setSessionParams($this->getAttributeParamsFormModel()->attributes);
			Yii::$app->controller->refresh();
		}
	}
	
	public function run() {
		$this->_modal = Modal::begin([
			'header' => '<h4 data-title></h4>',
			//'toggleButton' => ['label' => (strlen($result) > $this->lengthLimit) ? substr($result, 0, $this->lengthLimit - 3) . '...' : $result, 'class' => 'btn'],
		]);
			echo '<div data-body-content></div>';
		Modal::end();
		
		return $this->getAttributeParamsForm();
	}
	
	public function renderParsedAttributeValue($attribute) {
		$params = $this->getAttributeParams();
		$product = $this->model->model;
		
		$processed = \ant\ecommerce\models\ProductVariant::getValidOptionsForProduct($product, $params);
		
		try {
			if ($product instanceof \ant\ecommerce\models\ProductMap) {
				$product = $product;
			} else {
				$product = $product->getVariant($processed);
				//throw new \Exception(print_r($processed,1));
				//throw new \Exception($product->type);
				if (!isset($product)) throw new \Exception('Product variant with current combination is not exist. ');
			}
			$result = $product->getParsedAttribute($attribute, $this->getAttributeParams());
		} catch (ParseException $ex) {
			$result = $this->renderParseException($ex);
			//throw $ex;
		} catch (\Exception $ex) {
			$result = '<b>Error: </b>'.$ex->getMessage();
		}
		return $result;
	}
	
	protected function renderParseException($ex, $depth = 0) {
		$indentString = str_repeat(' | ', $depth);
		
		$html = $indentString.'<b>Parsed failed</b>: ' . $ex->getMessage().'<br/>';
		$html .= $indentString.'<b>Params: </b><br/>';
		//$result .= '<pre>'.var_dump($ex->getParamsUsed(), 1).'</pre>';
		if ($previous = $ex->getPrevious()) {
			$html .= $this->renderParseException($previous, ++$depth);
		}
		
		return $html;
	}

	protected function getDefaultAttributeParams() {
		return [];
	}
	
	protected function getEmptyAttributes() {
		$emptyAttributes = [];
		foreach ($this->_savedAttributes as $key => $attribute) 
		{
			$name 	= $this->model->{$this->attribute}[$key][AttributeModel::FIELD_NAME];
			$value 	= $this->model->{$this->attribute}[$key][AttributeModel::FIELD_VALUE];
			
			if (!isset($value) || trim($value) == '') {
				$emptyAttributes[] = $name;
			}
		}
		return $emptyAttributes;
	}

	protected function getAttributeParams() {
		return ArrayHelper::merge(
			$this->getDefaultAttributeParams(),
			$this->getEmptyAttributes(),
			$this->getSessionParams()
		);
	}

	protected function getAttributeParamsFormModel() {

		if ($this->_attributeParamsFormModel === null) {
			$attributes = $this->getAttributesName();
			$this->_attributeParamsFormModel = new \yii\base\DynamicModel($attributes);
			foreach ($attributes as $attribute) $this->_attributeParamsFormModel->addRule([$attribute], 'safe');
			
		}
		$this->_attributeParamsFormModel->addRule(['form'], 'safe');

		return $this->_attributeParamsFormModel;
	}

	protected function getAttributeParamsForm() {

		ob_start();

		$form = ActiveForm::begin();

		foreach ($this->getParams() as $field => $options) 
		{
			$items = isset($options['items']) ? $options['items'] : null;
			$field = is_array($options) ? $options['name'] : $options;
			$label = isset($options['label']) ? $options['label'] : null;
			
			if (isset($items)) {
				echo $form->field($this->getAttributeParamsFormModel(), $field)->dropdownList(array_combine($items, $items), ['prompt' => '-- Select --'])->label($label);
			} else {
				echo $form->field($this->getAttributeParamsFormModel(), $field)->label($label);
			}
		}

		echo Html::submitButton('Submit', ['class' => 'btn btn-default']);
		ActiveForm::end();

		return Html::button('<i class="fa fa-cogs"></i> Formula Testing Data', [
			'class' => 'btn',
			'data-target' => '#'.$this->_modal->id,
			'data-toggle' => 'modal',
			'data-title' => 'Formula Testing Data',
			'data-body-content' => ob_get_clean(),
			'encode' => false,
		]);
	}
	
	protected function getSessionParams() {
		return (
			Yii::$app->session->has(self::SESSION_PARAMS_NAME) 
			
		) ? Yii::$app->session->get(self::SESSION_PARAMS_NAME) : [];
	}
	
	protected function getAttributesName() {
		$return = [];
		foreach ($this->getParams() as $params) {
			$return[] = is_array($params) ? $params['name'] : $params;
		}
		return $return;
	}
	
	protected function getParams() {
		return $this->model->getParams();
	}

	protected function setSessionParams($params) {
		Yii::$app->session->set(self::SESSION_PARAMS_NAME, $params);
	}
}