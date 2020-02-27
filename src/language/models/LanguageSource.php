<?php
namespace ant\language\models;

class LanguageSource extends \lajax\translatemanager\models\LanguageSource {
	public static function ensure($category, $message) {
		if (trim($message) == '') return;
		
		$source = self::findOne([
			'category' => $category,
			'message' => $message,
		]);
		if (!isset($source)) {
			$source = new self;
			$source->attributes = [
				'category' => $category,
				'message' => $message,
			];
			
			if (!$source->save()) throw new \Exception(print_r($source->errors, 1));
		}
		return $source;
	}
}