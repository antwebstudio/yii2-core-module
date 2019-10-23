<?php

namespace ant\category\models;

use Yii;

/**
 * This is the model class for table "em_category_lang".
 *
 * @property integer $id
 * @property integer $master_id
 * @property string $language
 * @property string $slug
 * @property string $title
 * @property string $subtitle
 * @property string $body
 *
 * @property ArticleCategory $master
 */
class CategoryLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['master_id'], 'integer'],
            [['language'], 'required'],
            [['body'], 'string'],
            [['language'], 'string', 'max' => 6],
            [['slug'], 'string', 'max' => 1024],
			[['body'], 'safe'],
            [['title', 'subtitle'], 'string', 'max' => 512],
            [['master_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['master_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'master_id' => 'Master ID',
            'language' => 'Language',
            'slug' => 'Slug',
            'title' => 'Title',
            'subtitle' => 'Subtitle',
            'body' => 'Body',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaster()
    {
        return $this->hasOne(Category::className(), ['id' => 'master_id']);
    }
}
