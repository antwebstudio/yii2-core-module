<?php
namespace ant\dynamicform\models\fieldtypes;

use ant\dynamicform\base\FieldTypes;

class Paragraph extends FieldTypes
{
	/**
	 * @var string
	 */
	public static $name = 'Paragraph';

	/**
	 * @var integer
	 */
	public $row;

	/**
	 * Get view file
	 *
	 * @return string
	 */
	public static function getViewFile()
	{
		return '@common/modules/dynamicform/views/Paragraph';
	}

	public function attributeLabels()
	{
		return
		[
			'row' => 'Row'
		];
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
			[['row'], 'integer'],
		];
	}
}
?>
