<?php
namespace ant\attributev2\components;

use yii\base\Component;
use common\helpers\ArrayHelper;
use kartik\builder\Form;

class ModelDefaultFormAttributeInfo extends Component
{
	public $model = [];
	protected $_formAttributes = null;

	public $map = [
		'boolean' 	=> [
			'type' => Form::INPUT_CHECKBOX,
		],
		'captcha' 	=> [
			'type' => Form::INPUT_TEXT,
		],
		'date' 		=> [
			'type' => Form::INPUT_WIDGET,
			'widgetClass' => '\kartik\date\DatePicker',
			'options' => [
				'pluginOptions' => [
					'format' => 'yyyy-mm-dd',
				],
			],
		],
		'default' 	=> [
			'type' => Form::INPUT_TEXT,
		],
		'double' 	=> [
			'type' => Form::INPUT_TEXT,
			'options' => [
				'type' => 'number',
			],
		],
		'email' 	=> [
			'type' => Form::INPUT_TEXT,
			'options' => [
				'type' => 'number',
			],
		],
		'file' 		=> [
			'type' => Form::INPUT_TEXT,
		],
		'image' 	=> [
			'type' => Form::INPUT_TEXT,
		],
		'ip' 		=> [
			'type' => Form::INPUT_TEXT,
		],
		'in' 		=> [
			'type' => Form::INPUT_DROPDOWN_LIST,
			'items' => [],
		],
		'integer' 	=> [
			'type' => Form::INPUT_TEXT,
			'options' => [
				'type' => 'number',
			],
		],
		'number' 	=> [
			'type' => Form::INPUT_TEXT,
			'options' => [
				'type' => 'number',
			],
		],
		'string' 	=> [
			'type' => Form::INPUT_TEXT,
		],
		'url' 		=> [
			'type' => Form::INPUT_TEXT,
		],
		'safe' 		=> [
			'type' => Form::INPUT_TEXT,
		],
	];

	public $paramMap = [
		'in' => 'inParams',
	];

	public function getFormAttributes()
	{
		if ($this->_formAttributes === null)
		{
			$fields = [];

			foreach ($this->rules as $rule) 
			{

				$params 	= $rule;
				$attributes = array_shift($params);
				$validator 	= array_shift($params);

				if (!is_array($attributes)) $attributes = [$attributes]; 

				foreach ($attributes as $attribute) 
				{
					if (isset($this->map[$validator])) $fields[$attribute][$validator] = $params;
				}
			}

			foreach ($fields as $attribute => $field) 
			{
				$validator = $this->getPriorityValidator(array_keys($field));
				$formAttribute = $this->map[$validator];
				if (isset($this->paramMap[$validator]))
				{
					$formAttribute = call_user_func_array([$this, $this->paramMap[$validator]], [$formAttribute, $field[$validator]]);
				}
				$this->_formAttributes[$attribute] = $formAttribute;
			}
		}
		return $this->_formAttributes;
	}

	protected function getPriorityValidator(Array $validators)
	{
		if (count($validators) > 1) {
			$posValidators = [];
			foreach ($validators as $validator) $posValidators[array_search($validator, array_keys($this->map))] = $validator;
			return $posValidators[min(array_keys($posValidators))];
		} else {
			return $validators[0];
		}
	}

	protected function getRules()
	{
		return method_exists($this->model, '_rules') ? $this->model->_rules() : $this->model->rules();
	}

	protected function inParams($formAttribute, $params)
	{
		$items = $params['range'];
		$items = array_combine($items, $items);
		return ArrayHelper::merge($formAttribute, compact('items'));
	}
}