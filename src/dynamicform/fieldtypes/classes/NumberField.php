<?php
namespace ant\dynamicform\fieldtypes\classes;

use Yii;
use ant\dynamicform\base\FieldTypes;

class NumberField extends FieldTypes
{
    public static $name = "Number Field";
	
	public $fieldName = 'value_number';
    public $min, $max;

    public function rules()
    {
        return
        [
            [['min', 'max'], 'integer'],
        ];
    }
	
	public function inputRules() {
		$rule = ['integer'];
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
	
	public function andFilterBy($query, $value) {
		$values = explode(',', $value);
		
		return $query->andFilterWhere(['dynamicField_'.$this->field->id.'.value_number' => $values]);
	}
	
	public static function widget($config) {
		$model = $config['model'];
		$attribute = $config['attribute'];
		$field = $config['dynamicField'];
		$options = isset($config['options']) ? $config['options'] : [];
		
		$options['type'] = 'number';
		
		return \yii\helpers\Html::activeTextInput($model, $attribute, $options);
	}

    public static function render($params = [])
    {
        return Yii::$app->view->render('@common/modules/dynamicform/fieldtypes/views/NumberField/NumberField', $params);
    }
}