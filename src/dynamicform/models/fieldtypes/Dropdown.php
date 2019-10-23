<?php
namespace ant\dynamicform\models\fieldtypes;

use Yii;
use yii\helpers\ArrayHelper;
use ant\dynamicform\base\FieldTypes;

class Dropdown extends FieldTypes
{
	/**
	 * @var string
	 */
	public static $name = 'Dropdown';

	/**
	 * @var array
	 */
	public $items;

	/**
	 * Get view file
	 *
	 * @return string
	 */
	public static function getViewFile()
	{
		return '@ant/dynamicform/views/Dropdown';
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
			[['items'], 'each', 'rule' => ['required']],
		];
	}

	/**
	 * Item template
	 *
	 * @param  array  $params
	 * @param  boolean $isDeleteAble
	 * @param  boolean $cleanOutput
	 * @return string
	 */
	public static function renderItems($params, $isDeleteAble = true, $cleanOutput = false)
	{
		$params =  ArrayHelper::merge($params, ['isDeleteAble' => $isDeleteAble]);
		$output = Yii::$app->view->render('@ant/dynamicform/views/DropdownItem', $params);
		return $cleanOutput ? parent::cleanOutput($output) : $output;
	}

	public static function registerJs($params = [])
	{
		$params = ArrayHelper::merge($params, [
			'key' => '__key__',
			'dropdownItemKey' => '__dropdownItemKey__',
		]);

		return
		[
			['js' => "
				$(document).on('click', '." . self::getScriptPrefix() . "setting-item-add-button', function (e) {
					
					var key = $(this).data('key');

					$(this).data('dropdown-item-key', $(this).data('dropdown-item-key') + 1);

					var dropdownItemKey = $(this).data('dropdown-item-key');

					var html = '" . self::renderItems($params, true, true) . "';
					var item = $(html.replace(/__key__/g, key).replace(/__dropdownItemKey__/g, dropdownItemKey));

					$(this).closest('." . self::getScriptPrefix() . "items-container').find('tbody').append(item.hide().fadeIn(300));
			    });
			", 'key' => self::getScriptPrefix()],
		];
	}
}