<?php

namespace ant\language\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use ant\language\models\Translatable as TranslatableModel;
//use lajax\translatemanager\helpers\Language;
//use lajax\translatemanager\models\LanguageSource;
//use lajax\translatemanager\models\LanguageTranslate;

class Translatable extends \yii\base\Behavior
{
	public $currentLanguage;
	
	public $sourceLanguage;
    /**
     * @var array|string
     */
    public $translateAttributes;
	
	protected $_translatables = [];
	protected $_sourceLanguageAttributes = [];

	public function init() {
		if (!isset($this->sourceLanguage)) $this->sourceLanguage = Yii::$app->sourceLanguage;
		if (!isset($this->currentLanguage)) $this->currentLanguage = Yii::$app->language;
	}

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'translateAttributes',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'saveAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'saveAttributes',
        ];
    }

    /**
     * Saves new language element by category.
     *
     * @param \yii\base\Event $event
     */
    public function saveAttributes($event)
    {
        $isAppInSourceLanguage = Yii::$app->sourceLanguage === $this->getCurrentLanguage();

		if (!$isAppInSourceLanguage) {
			$translated = $this->getTranslatedMessagesByLanguage($this->getCurrentLanguage());
			foreach ($this->translateAttributes as $attribute) {
				/* if (!$this->owner->isAttributeChanged($attribute)) {
					continue;
				} */
				$translated[$attribute] = $this->owner->{$attribute};
				
				if (isset($this->_sourceLanguageAttributes[$attribute])) {
					$this->owner->{$attribute} = $this->_sourceLanguageAttributes[$attribute];
				}
			}
			$this->saveTranslatedByLanguage($translated, $this->getCurrentLanguage());
		}
    }
	
	public function translateAttributes($event)
    {
        $isAppInSourceLanguage = Yii::$app->sourceLanguage === $this->getCurrentLanguage();
		
		if (!$isAppInSourceLanguage) {
			$translated = $this->getTranslatedMessagesByLanguage($this->currentLanguage);
			foreach ($this->translateAttributes as $attribute) {
				$this->_sourceLanguageAttributes[$attribute] = $this->owner->{$attribute};
				
				if (isset($translated[$attribute])) {
					$this->owner->{$attribute} = $translated[$attribute];
				} else {
					// Should set to $this->owner->{$attribute} as empty?
				}
			}
		}
    }
	
	protected function saveTranslatedByLanguage($translated, $language) {
		$translatable = $this->getTranslatableByLanguage($language);
		if (!isset($translatable)) {
			$translatable = new TranslatableModel;
			$translatable->language = $this->getCurrentLanguage();
			$translatable->translatable_id = $this->owner->id; 
			$translatable->translatable_class_id = \ant\models\ModelClass::getClassId($this->owner);
		}
		$translatable->translated = $translated;
		
		if (!$translatable->save()) throw new \Exception(print_r($translatable->errors, 1));
	}
	
	protected function getCurrentLanguage() {
		// May implement aliases of language in future
		return $this->currentLanguage;
	}
	
	protected function getTranslatedMessagesByLanguage($language) {
		$translatable = $this->getTranslatableByLanguage($language);
		return isset($translatable) ? $translatable->translated : [];
	}
	
	protected function getTranslatableByLanguage($language) {
		if (!isset($this->_translatables[$language])) {
			$this->_translatables[$language] = $this->owner->getTranslatable($language)->one();
		}
		return $this->_translatables[$language];
	}
	
	public function getTranslatables() {
		return $this->owner->hasMany(TranslatableModel::class, ['translatable_id' => 'id'])
			->onCondition(['translatable_class_id' => \ant\models\ModelClass::getClassId($this->owner)]);
	}
	
	public function getTranslatable($language) {
		return $this->owner->hasMany(TranslatableModel::class, ['translatable_id' => 'id'])
			->onCondition([
				'language' => $language,
				'translatable_class_id' => \ant\models\ModelClass::getClassId($this->owner)
			]);
	}
}
