<?php 
namespace ant\attributev2\components;

use kartik\builder\Form;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;

abstract class FieldType extends Component
{
	const EVENT_BEFORE_CONTENT_VALIDATE = 'beforeContentValidate';
	const EVENT_AFTER_CONTENT_VALIDATE 	= 'afterContentFind';
	const EVENT_BEFORE_CONTENT_INSERT 	= 'beforeContentInsert';
	const EVENT_BEFORE_CONTENT_UPDATE 	= 'beforeContentUpdate';
	const EVENT_AFTER_CONTENT_UPDATE 	= 'afterContentUpdate';
	const EVENT_AFTER_CONTENT_INSERT 	= 'afterContentInsert';

	public $attribute = null;

	public function behaviors() 
	{
		return [
			[
				'class' => \common\behaviors\EventHandlerBehavior::className(),
				'events' => $this->events(),
			],
		];
	}

	public function events() 
	{
		return 
		[
			self::EVENT_BEFORE_CONTENT_VALIDATE => [$this, 'beforeContentValidate'],
			self::EVENT_AFTER_CONTENT_VALIDATE 	=> [$this, 'afterContentFind'],
			self::EVENT_BEFORE_CONTENT_INSERT 	=> [$this, 'beforeContentInsert'],
			self::EVENT_BEFORE_CONTENT_UPDATE 	=> [$this, 'beforeContentUpdate'],
			self::EVENT_AFTER_CONTENT_UPDATE 	=> [$this, 'afterContentUpdate'],
			self::EVENT_AFTER_CONTENT_INSERT 	=> [$this, 'afterContentInsert'],
		];
	}

	abstract public function frontendInput();
	public function getFrontendInput()
	{
		$attributeModel = $this->attribute;
		$attributeModel->prepareForSave();
		return ArrayHelper::merge($this->frontendInput(), $attributeModel->fieldtype_setting ? $attributeModel->fieldtype_setting : []);
	}
	abstract public function backendInput();
	public static function getName() {}

	public function fieldForm()
	{
		return [
			'name' 				=> ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Name']],
			'label' 			=> ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Label']],
			'rules[required]' 	=> ['type' => Form::INPUT_CHECKBOX, 'label' => 'Is required'],
		];
	}
	abstract public function settingForm();
	public function getSettingForm()
	{
		$attributeModel = $this->attribute;
		$attributeModel->prepareForSettingForm();
		return ArrayHelper::merge($this->fieldForm(), $this->settingForm());
	}
	
	public function afterContentFind($event) {}
	public function afterContentInsert($event) {}
	public function afterContentUpdate($event) {}
	public function beforeContentUpdate($event) {}
	public function beforeContentInsert($event) {}
	public function beforeContentValidate($event) {}
	public function afterContentValidate($event) {}

	public static function getFieldTypeNamespace()
	{
		return 'ant\attributev2\fieldtypes';
	}

	public static function getFieldTypePath()
	{
		return dirname(__DIR__) . '/fieldtypes';
	}

	public static function getFieldTypes()
	{
		$fieldTypes = [];
		foreach (FileHelper::findFiles(self::getFieldTypePath()) as $file) 
		{
			$class = self::getFieldTypeNamespace() . '\\' . basename($file, '.php');
			$name = $class::getName();
			$fieldTypes[$name] = $class;
		}
		return $fieldTypes;
	}

	public static function getDefaultFieldType()
	{
		$fieldtypes = self::getFieldTypes();
		return array_shift($fieldtypes);
	}

	public function __toString()
	{
		return static::className();
	}

	public function saveFormatToSettingFormFormat($attributeModel) {}
	public function settingFormFormatToSaveFormat($attributeModel) {}

	public function prepareForSettingForm($attributeModel) 
	{
		$rules = $attributeModel->rules;
		foreach ($rules as $i => $rule) 
		{
			if ($rule[0] == 'required')
			{
				$rules['required'] = 1;
				unset($rules[$i]);
			}
		}
		$attributeModel->rules = $rules;
		$this->saveFormatToSettingFormFormat($attributeModel);
	}
	public function prepareForSave($attributeModel) 
	{
		$rules = $attributeModel->rules;
		if (isset($rules['required']))
		{
			if ((bool)$rules['required'] == true) $rules[] = ['required'];
			unset($rules['required']);
		}
		$attributeModel->rules = $rules ? $rules : [['safe']];
		$this->settingFormFormatToSaveFormat($attributeModel);
	}
}