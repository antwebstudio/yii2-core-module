<?php
namespace ant\dynamicform\fieldtypes\classes;

use Yii;
use ant\dynamicform\base\FieldTypes;

class DropDown extends FieldTypes
{
	public static $idCount = 1;
    public static $name = "Drop Down List";
	protected static $view = '@ant/dynamicform/fieldtypes/views/DropDown/DropDown';

	public $fieldName = 'value_json';
    public $items = [];
	public $min, $max;

    public function rules()
    {
        return
        [
            [['items'], 'each', 'rule' => ['required']],
        ];
    }
	
	public function inputRules() {
		$rule = ['ant\validators\ArrayValidator'];
		if ($this->min != '') $rule['min'] = $this->min;
		if ($this->max != '') $rule['max'] = $this->max;
		
		return [
			$rule,
		];
	}

    public function attributeLabels()
    {
        return
        [
            'items' => 'Items',
        ];
    }
	
	public static function widget($config = []) {
		$model = $config['model'];
		$attribute = $config['attribute'];
		$field = $config['dynamicField'];
		$options = isset($config['options']) ? $config['options'] : [];
		
		$options['prompt'] = '';
		
		if ($field->setting['max'] > 1 || $field->setting['max'] == '') {
			$options['multiple'] = true;
		}

		$items = isset($field->setting['items']) ? array_combine($field->setting['items'], $field->setting['items']) : [];
		
		$options['id'] = 'dropdown_'.(self::$idCount++);
		return \kartik\select2\Select2::widget([
			'model' => $model,
			'attribute' => $attribute,
			'data' => $items,
			'options' => $options,
		]);
		//return \yii\helpers\Html::activeDropDownList($model, $attribute, $items, $options);
	}
}