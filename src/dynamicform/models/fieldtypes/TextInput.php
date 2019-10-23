<?php  
namespace ant\dynamicform\models\fieldtypes;

use ant\dynamicform\base\FieldTypes;

class TextInput extends FieldTypes
{
	/**
	 * @var string
	 */
	public static $name = 'Text';
	
	/**
	 * @var string
	 */
	public $type;
	/**
	 * @var integer
	 */
	public $min;
	/**
	 * @var integer
	 */
	public $max;

	/**
	 * Types drop down list
	 * 
	 * @return array
	 */
	public static function getTypesDropDownList()
	{
		return 
		[
			'string' => 'Text',
			'integer' => 'Number',
		];
	}

	/**
	 * Get view file
	 * 
	 * @return string
	 */
	public static function getViewFile()
	{
		return '@common/modules/dynamicform/views/TextInput';
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
			[['type'], 'in', 'range' => array_keys(self::getTypesDropDownList())],
			[['min',  'max'], 'integer'],
		];
	}
}
?>