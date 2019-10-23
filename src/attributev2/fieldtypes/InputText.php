<?php 
namespace ant\attributev2\fieldtypes;

use yii\helpers\ArrayHelper;
use kartik\builder\Form;
use ant\attributev2\components\FieldType;

class InputText extends FieldType
{
	public $minLength;
	public $maxLength;
	public $required = false;

	public static function getName() 
	{
		return 'Text Input';
	}
	public function frontendInput() {
		return ['type' => Form::INPUT_TEXT];
	}
	public function backendInput() {}
	public function settingForm() {
		return [
			// 'rules' => ['type' => Form::INPUT_TEXT]
		];
	}
}