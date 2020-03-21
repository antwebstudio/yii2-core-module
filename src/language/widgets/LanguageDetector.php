<?php
namespace ant\language\widgets;

use Yii;
use yii\helpers\Url;
use yii\bootstrap4\ButtonDropdown;
use ant\language\models\Language;
use ant\language\models\LanguageSearch;

class LanguageDetector extends \yii\base\Widget {
	public $url = ['/site/language'];
	
	const SESSION_NAME = 'language-is-set-by-detector';
	//public $paramName = 'language';
	//public $activeOnly = true;
	
	protected function getCurrentLanguage() {
		return Yii::$app->request->post('language', Yii::$app->request->get('language', Yii::$app->session->get(\ant\language\Module::SESSION_LANGUAGE, Yii::$app->language)));
	}
	
	protected function languageIsSet() {
		return Yii::$app->session->get(\ant\language\Module::SESSION_LANGUAGE) || Yii::$app->session->get(self::SESSION_NAME);
	}
	
	public function run() {
		if (!$this->languageIsSet()) {
			Yii::$app->session->set(self::SESSION_NAME, true);
			
			$url = Url::to($this->url);
			$this->view->registerJs('
				(function() {
					var language = navigator.languages ? navigator.languages[0] : (navigator.language || navigator.userLanguage);
					var current = "'.$this->getCurrentLanguage().'";
					
					//alert(language + ": "+current);
				
					var xhr = new XMLHttpRequest();
					xhr.open("GET", "'.$url.'?language=" + language);
					xhr.send();
					
					if (language != current) location.reload();
				})();
			');
		}
	}
}