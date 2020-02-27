<?php

namespace ant\language\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use lajax\translatemanager\helpers\Language;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\models\LanguageTranslate;

class Translatable extends \lajax\translatemanager\behaviors\TranslateBehavior
{
	public $currentLanguage;
    /**
     * @var array|string
     */
    public $translateAttributes;

    /**
     * @var string Category of message.
     */
    public $category = 'database';

    /**
     * @var BaseActiveRecord the owner model of this behavior
     */
    public $owner;

    /**
     * @inheritdoc
     */
    public function init()
    {
        //parent::init();

        //$this->category = str_replace(['{', '%', '}'], '', $this->category);
    }

    /**
     * Saves new language element by category.
     *
     * @param \yii\base\Event $event
     */
    public function saveAttributes($event)
    {
        $isAppInSourceLanguage = Yii::$app->sourceLanguage === $this->getCurrentLanguage();

        foreach ($this->translateAttributes as $attribute) {
            if (!$this->owner->isAttributeChanged($attribute)) {
                continue;
            }

            if ($isAppInSourceLanguage || !$this->saveAttributeValueAsTranslation($attribute)) {
                Language::saveMessage($this->owner->attributes[$attribute], $this->getCategory());
            }
        }
    }
	
	protected function getCurrentLanguage() {
		return isset($this->currentLanguage) ? $this->currentLanguage : (is_object(Yii::$app->language) ? Yii::$app->language->name : Yii::$app->language);
	}
	
	protected function getCategory() {
		return strtr($this->category, [
			'{id}' => $this->owner->id,
		]);
	}
	
	public function translateAttributes($event)
    {
        foreach ($this->translateAttributes as $attribute) {
            $this->owner->{$attribute} = Yii::t($this->getCategory(), $this->owner->attributes[$attribute], [], $this->getCurrentLanguage());
        }
    }

    /**
     * @param string $attribute The name of the attribute.
     *
     * @return bool Whether the translation is saved.
     */
    private function saveAttributeValueAsTranslation($attribute)
    {
        $sourceMessage = $this->owner->getOldAttribute($attribute);
        $translatedMessage = $this->owner->attributes[$attribute];

        // Restore the original value, so it won't be replaced with the translation in the database.
        $this->owner->{$attribute} = $sourceMessage;

        $translateSource = $this->findSourceMessage($sourceMessage);
        if (!$translateSource) {
            return false; // The source does not exist, the message cannot be saved as translation.
        }

        $translation = new LanguageTranslate();
        foreach ($translateSource->languageTranslates as $tmpTranslate) {
            if ($tmpTranslate->language === $this->getCurrentLanguage()) {
                $translation = $tmpTranslate;
                break;
            }
        }

        if ($translation->isNewRecord) {
            $translation->id = $translateSource->id;
            $translation->language = $this->getCurrentLanguage();
        }

        $translation->translation = $translatedMessage;
        $translation->save();

        return true;
    }

    /**
     * Finds the source record with case sensitive match.
     *
     * @param string $message
     *
     * @return LanguageSource|null Null if the source is not found.
     */
    private function findSourceMessage($message)
    {	
        $sourceMessages = LanguageSource::findAll(['message' => $message, 'category' => $this->getCategory()]);

        foreach ($sourceMessages as $source) {
            if ($source->message === $message) {
                return $source;
            }
        }
    }
}
