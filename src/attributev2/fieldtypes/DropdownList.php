<?php 
namespace ant\attributev2\fieldtypes;

use yii\helpers\ArrayHelper;
use kartik\builder\Form;
use ant\attributev2\components\FieldType;

class DropdownList extends FieldType
{
	public static function getName() 
	{
		return 'Dropdown';
	}
	public function frontendInput() 
	{
		return [
			'type' => Form::INPUT_DROPDOWN_LIST,
		];
	}
	public function backendInput() {}
	public function settingForm() 
	{
		return [
			'fieldtype_setting[items]' => [
				'label' => 'Dropdown Items',
				'type' => Form::INPUT_WIDGET,
				'widgetClass' => 'unclead\multipleinput\MultipleInput',
				'options' => [
					'data' => $this->attribute->fieldtype_setting['items'],
					'columns' => [
						[
							'name' => 'name',
							'options' => [
								'placeholder' => 'Item Label', 
							],
						],
						[
							'name' => 'value',
							'options' => [
								'placeholder' => 'Item Value', 
							],
						],
					],
				],
			],
		];
	}
	public function saveFormatToSettingFormFormat($attributeModel)
	{
		$setting = $attributeModel->fieldtype_setting;
		$items = [];
		foreach ($setting['items'] as $value => $name)
		{
			$items[] = compact('value', 'name');
		}
		$setting['items'] = $items;
		$attributeModel->fieldtype_setting = $setting;
	}
	public function settingFormFormatToSaveFormat($attributeModel)
	{
		$setting = $attributeModel->fieldtype_setting;
		// if (!$this->isRequired)
		// {
		// 	$setting = ArrayHelper::merge($setting, [
		// 		'options' => [
		// 			'prompt' => '-- Select --'
		// 		],
		// 	]);
		// }
		$items = [];
		foreach ($setting['items'] as $item) $items[$item['value']] = $item['name'];
		$setting['items'] = $items;
		$attributeModel->fieldtype_setting = $setting;
	}
}