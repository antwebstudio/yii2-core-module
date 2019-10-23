<?php
namespace ant\dynamicform\models\fieldtypes;

use ant\dynamicform\base\FieldTypes;

class TextField extends FieldTypes
{
	/**
	 * @var string
	 */
	public static $name = 'Text Field';

	/**
	 * @var integer
	 */
	public $min;
	/**
	 * @var integer
	 */
	public $max;

	/**
	 * Get view file
	 *
	 * @return string
	 */
	public static function getViewFile()
	{
		return '@ant/dynamicform/views/TextField';
	}

	/**
	 * Rules
	 *
	 * @return array
	 */
	public function rules()
	{
		return
		[
			[['min',  'max'], 'integer'],
		];
	}
}