<?php

namespace ant\language\models;

use Yii;

/**
 * This is the model class for table "{{%language_translatable}}".
 *
 * @property int $id
 * @property int|null $translatable_id
 * @property int|null $translatable_class_id
 * @property string|null $language
 */
class Translatable extends \yii\db\ActiveRecord
{
	public function behaviors() {
		return [
			[
				'class' => \ant\behaviors\SerializableAttribute::class,
				'attributes' => ['translated'],
			],
		];
	}
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%language_translatable}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['translatable_id', 'translatable_class_id'], 'integer'],
            [['language'], 'string', 'max' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'translatable_id' => 'Translatable ID',
            'translatable_class_id' => 'Translatable Class ID',
            'language' => 'Language',
        ];
    }
}
