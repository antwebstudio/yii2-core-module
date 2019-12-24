<?php

namespace ant\file\widgets;

class ElFinder extends \alexantr\elfinder\ElFinder {
	public static function getFilePickerCallback($route, $popupSettings = [], $view = null) {
		return \ant\file\adapters\editors\TinyMce5::getFilePickerCallback($route, $popupSettings, $view);
	}
}