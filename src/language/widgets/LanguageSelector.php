<?php
namespace ant\language\widgets;

use Yii;
use yii\helpers\Url;
use yii\bootstrap4\ButtonDropdown;
use ant\language\models\Language;
use ant\language\models\LanguageSearch;

class LanguageSelector extends \yii\base\Widget {
	public $paramName = 'language';
	public $activeOnly = true;
	
	protected function getCurrentLanguage() {
		return Yii::$app->request->post('language', Yii::$app->request->get('language', Yii::$app->language));
	}
	
	public function run() {
		if (!\Yii::$app->getModule('translatemanager')) return;
		
		$items = [];
		//$search = new LanguageSearch;
		//$search->status = Language::STATUS_ACTIVE;
		
		//$languages = Language::find()->indexBy('language_id')->andWhere(['status' => Language::STATUS_ACTIVE])->all();
		
		$languages = Language::getLanguageNames($this->activeOnly);
		
		foreach ($languages as $code => $name) {
			$items[] = [
				'label' => $name,
				'url' => Url::current(['language' => $code]),
			];
		}
		
		$current = $this->getCurrentLanguage();
		return ButtonDropdown::widget([
			'label' => isset($languages[$current]) ? $languages[$current] : $current,
			'dropdown' => [
				'items' => $items,
			],
		]);
	}
}