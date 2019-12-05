<?php
namespace ant\dynamicform\fieldtypes\classes;

use Yii;
use ant\dynamicform\base\FieldTypes;

class Paragraph extends FieldTypes
{

    public $max;

	public $fieldName = 'value_text';
    public static $name = 'Paragraph';
	protected static $view = '@ant/dynamicform/fieldtypes/views/Paragraph/Paragraph';

    public function rules()
    {
        return
        [
            [['max'], 'integer'],
        ];
    }
	
	public function inputRules() {
		$rule = ['string'];
		//if ($this->min != '') $rule['min'] = $this->min;
		if ($this->max != '') $rule['max'] = $this->max;
		
		return [
			$rule,
		];
	}

    public function attributeLabels()
    {
        return
        [
            'max' => 'Max',
        ];
    }
	
	public static function widget($config) {
		$model = $config['model'];
		$attribute = $config['attribute'];
		$field = $config['dynamicField'];
		$options = isset($config['options']) ? $config['options'] : [];
		
		return \yii\helpers\Html::activeTextarea($model, $attribute, $options);
	}
}