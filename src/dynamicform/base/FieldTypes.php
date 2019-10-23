<?php
namespace ant\dynamicform\base;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\Model;

class FieldTypes extends Model
{
	public static $name = 'name';
	
	public $field;

	private static $_fileTypeClassExtention = '.php';

	public static function getClasses()
	{
		$classes = [];

		$namespace = __NAMESPACE__;

		$namespace = explode('\\', $namespace);

		array_pop($namespace);

		$namespace = implode('\\', $namespace) . '\fieldtypes\classes';

		$dir = realpath(__DIR__ . '/../fieldtypes/classes/');

		$paths = FileHelper::findFiles($dir, ['only'=>['*' . self::$_fileTypeClassExtention]]);

		foreach ($paths as $key => $path)
		{
			$className = str_replace($dir, '', realpath($path));

			$className = str_replace('/', '\\', $className);

			$className = str_replace(self::$_fileTypeClassExtention, '', $className);

			$classes[] = $namespace . $className;
		}

		return $classes;
	}

	public static function getDefaultClass()
	{
		return self::getClasses()[0];
	}

	public static function getDropDownList()
	{
		$dropDownList = [];

        foreach (self::getClasses() as $class) $dropDownList[$class] = $class::$name;

        return $dropDownList;
	}
}