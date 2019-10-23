<?php
namespace ant\dynamicform\fieldtypes\classes;

use Yii;
use ant\dynamicform\base\FieldTypes;

class DateField extends FieldTypes
{
	//public static $idCount = 1;
    public static $name = "Date Field";

	public $fieldName = 'value_string';
    //public $items = [];
	//public $min, $max;

    public function rules()
    {
        return
        [
            //[['items'], 'each', 'rule' => ['required']],
        ];
    }
	
	public function inputRules() {
		/*$rule = ['common\validators\ArrayValidator'];
		if ($this->min != '') $rule['min'] = $this->min;
		if ($this->max != '') $rule['max'] = $this->max;
		*/
		
		return [
			//$rule,
		];
	}

    public function attributeLabels()
    {
        return
        [
            //'items' => 'Items',
        ];
    }
	
	public static function widget($config = []) {
		$model = $config['model'];
		$attribute = $config['attribute'];
		$field = $config['dynamicField'];
		$options = isset($config['options']) ? $config['options'] : [];
		
		/*if ($field->setting['max'] > 1 || $field->setting['max'] == '') {
			$options['multiple'] = true;
		}*/

		$items = isset($field->setting['items']) ? array_combine($field->setting['items'], $field->setting['items']) : [];
		
		//$options['id'] = 'dropdown_'.(self::$idCount++);
		return \kartik\date\DatePicker::widget([
			'model' => $model,
			'attribute' => $attribute,
			//'data' => $items,
			'options' => $options,
			'pluginOptions' =>
            [
                'autoclose' => true,
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd'
            ],
		]);
		//return \yii\helpers\Html::activeDropDownList($model, $attribute, $items, $options);
	}

    public static function render($params = [])
    {
		// Use renderAjax will cause the container cannot work properly with bootstrap collapse
        return Yii::$app->view->render('@common/modules/dynamicform/fieldtypes/views/date-field', $params);
    }
}

?>
