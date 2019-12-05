<?php
namespace ant\dynamicform\fieldtypes\classes;

use Yii;
use ant\dynamicform\base\FieldTypes;

class TextField extends FieldTypes
{
    public static $name = "Text Field";
	protected static $view = '@ant/dynamicform/fieldtypes/views/TextField/TextField';
	
	public $fieldName = 'value_string';
    public $min, $max;

    public function rules()
    {
        return
        [
			[['min', 'max'], 'trim'],
            [['min', 'max'], 'integer'],
        ];
    }
	
	public function inputRules() {
		$rule = ['string'];
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
            'min' => 'Min',
            'max' => 'Max',
        ];
    }
	
	public static function widget($config) {
		$model = $config['model'];
		$attribute = $config['attribute'];
		$field = $config['dynamicField'];
		$options = isset($config['options']) ? $config['options'] : [];
		
		return \yii\helpers\Html::activeTextInput($model, $attribute, $options);
	}
}