<?php 
namespace ant\attributev2\traits;

use yii\helpers\ArrayHelper;

trait DynamicAttributeTrait
{
	public $form = [];

	public function rules()
	{
		return ArrayHelper::merge(method_exists($this, '_rules') ? call_user_func_array([$this, '_rules'], []) : [], $this->getDynamicAttribute()->rules);
	}

	public function attributes()
	{
		try {
			return ArrayHelper::merge(parent::attributes(), $this->getDynamicAttribute()->names);
		} catch (\Exception $e) {
			return parent::attributes();
		}
	}

	public function attributeLabels()
	{
		return ArrayHelper::merge(method_exists($this, '_attributeLabels') ? call_user_func_array([$this, '_attributeLabels'], []) : [], $this->getDynamicAttribute()->labels);
	}
}