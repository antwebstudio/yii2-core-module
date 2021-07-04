<?php

namespace ant\category\models;

use trntv\filekit\behaviors\UploadBehavior;
use ant\category\models\query\CategoryQuery;
use ant\category\models\CategoryType;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_category".
 *
 * @property integer $id
 * @property string $slug
 * @property string $title
 * @property integer $status
 *
 * @property Article[] $articles
 * @property ArticleCategory $parent
 */
class Category extends ActiveRecord
{
	public $icon;
	public $thumbnail;
    public $attachments;
	public $banner;
    //public $attachments2;
    	
    const STATUS_ACTIVE = 0;
    const STATUS_DRAFT = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @return ArticleCategoryQuery
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
	
	protected static function _ensureByTitles($values, $categoryType, $categoriesAsArray = true) {
		
		$query = self::find()
			->alias('category')
			->typeOf($categoryType)
			->indexBy('title')
			->andWhere(['category.title' => $values]);
		
		if ($categoriesAsArray) $query->asArray();
		
		$categories = $query->all();
		
		$ids = [];
		foreach ((array) $values as $value) {
			if (!isset($categories[$value])) {
				$root = self::ensureRoot($categoryType);
				
				$category = new self;
				$category->title = $value;
				$category->type_id = CategoryType::getIdFor($categoryType);
				if (!$category->appendTo($root)) throw new \Exception(print_r(array_keys($categories),1).$categoryType.print_r($ids, 1).print_r($category->errors, 1).$value);
				
				$categories[$value] = $category;
			}
			$ids[$value] = $categories[$value]['id'];
			//throw new \Exception('t'.$value.print_r($ids,1).$categoryType);
		}
		return $categories;
	}
	
	public static function ensureByTitle($value, $categoryType) {
		$categories = self::_ensureByTitles([$value], $categoryType, false);
		return current($categories);
	}
	
	public static function ensureByTitles($values, $categoryType) {
		$categories = self::_ensureByTitles($values, $categoryType);
		foreach ($categories as $value => $category) {
			$ids[$value] = $category['id'];
		}
		return $ids;
	}
	
	public static function ensureRoot($type = 'default', $rootTitle = 'Uncategorized') {
		$root = Category::find()->rootsOfType($type)->one();
        if (!isset($root)) {
            $root = Category::find()->type($type)->where(['slug' => 'uncategorized'])->one();
        }
		if (!isset($root)) {
			$root = new self(['type_id' => CategoryType::getIdFor($type)]);
			$root->title = $rootTitle;
			$type = $root->type_id;
			if (!$root->makeRoot()) throw new \Exception(print_r($root->errors, 1));
		}
		return $root;
	}

    public function behaviors()
    {
        $behaviors = [
            \ant\behaviors\TimestampBehavior::className(),
			\ant\behaviors\AttachBehaviorBehavior::class,
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'immutable' => true
            ],
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
				'visibility' => function($attribute) {
					if (isset($this->type->visible_fields)) {
						$fields = explode(',', $this->type->visible_fields);
						foreach ($fields as $field) {
							if (trim($field) == $attribute) {
								return true;
							}
						}
						return false;
					}
					return true;
				}
			],
            // [
            //     'class' => \trntv\filekit\behaviors\UploadBehavior::className(),
            //     'attribute' => 'icon',
            //     'pathAttribute' => 'icon_path',
            //     'baseUrlAttribute' => 'icon_base_url',
            // ],
            'image' =>
            [
                'class' => \ant\file\behaviors\AttachmentBehavior::className(),
				'modelType' => Category::className(),
                'attribute' => 'attachments',
                'multiple' => true,
                'type' => 'default',
            ],
            'thumbnail' => 
            [
                'class' => \ant\file\behaviors\AttachmentBehavior::className(),
				'modelType' => Category::className(),
                'attribute' => 'thumbnail',
                'uploadRelation' => 'thumbnailAttachment',
                'multiple' => false,
                'type' => 'thumbnail',
            ],
            'banner' => 
            [
                'class' => \ant\file\behaviors\AttachmentBehavior::className(),
				'modelType' => Category::className(),
                'attribute' => 'banner',
                'uploadRelation' => 'bannerAttachment',
                'multiple' => false,
                'type' => 'banner',
            ],
            'tree' => [
                'class' => \creocoder\nestedsets\NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                'leftAttribute' => 'left',
                'rightAttribute' => 'right',
                // 'depthAttribute' => 'depth',
            ],
        ];
		
		if (class_exists('creocoder\translateable\TranslateableBehavior')) {
			$behaviors['translateable'] = [
                'class' => \creocoder\translateable\TranslateableBehavior::className(),
                'translationAttributes' => [
					'slug', 'title', 'body', 'subtitle',
				],
                // translationRelation => 'translations',
                // translationLanguageAttribute => 'language',
            ];
		}
		
		if (\Yii::$app->getModule('translatemanager')) {
			/*$behaviors[] = [
                'class' => \ant\language\behaviors\Translatable::className(),
                'translateAttributes' => ['title', 'subtitle', 'body', 'short_description'],
				'currentLanguage' => Yii::$app->request->post('language', Yii::$app->request->get('language', Yii::$app->language)),
				'category' => 'category-{id}',
            ];*/
		}
		
		return $behaviors;
    }
	
	public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
        ];
    }
	
	public function getTranslations()
    {
        return $this->hasMany(CategoryLang::className(), ['master_id' => 'id']);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'subtitle'], 'string', 'max' => 512],
            [['slug'], 'unique', 'targetAttribute' => ['slug', 'type_id']],
            [['slug'], 'string', 'max' => 1024],
            [['short_description', 'body', 'attachments', 'attachments2', 'thumbnail', 'banner'], 'safe'],
            ['status', 'integer'],
			['parent_id', 'default', 'value' => 0],
            ['parent_id', 'exist', 'when' => function($model) { return $model->parent_id != 0; }, 'targetClass' => Category::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'slug' => Yii::t('common', 'Slug'),
            'title' => Yii::t('common', 'Title'),
            'parent_id' => Yii::t('common', 'Parent Category'),
            'status' => Yii::t('common', 'Active')
        ];
    }
	
	public function getRoute() {
		return ['/category/category/view', 'slug' => $this->slug, 'id' => $this->id, 'type' => $this->type->name];
	}
	
	public function getUrl() {
		return \yii\helpers\Url::to($this->getRoute());
	}
	
	/*
	public function getTranslations() {
		
		throw new \Exception($this->langClassName.' [ '.$this->langForeignKey.' => '.$this->ownerPrimaryKey.']');
		return $this->hasMany(\ant\models\ArticleCategoryLang::className(), ['master_id' => 'id']);
	}*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
		if (YII_DEBUG) throw new \Exception('Deprecated');
        return $this->hasMany(Article::className(), ['category_id' => 'id']);
    }
	
    public function getType()
    {
        return $this->hasOne(CategoryType::className(), ['id' => 'type_id']);
    }
	
	public function getSubCategories() {
		return $this->children(1);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->parents(1);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFileAttachments() {
        return $this->getAttachmentsRelation('default');
    }

    public function getThumbnailAttachment() {
        return $this->getAttachmentsRelation('thumbnail');
    }

    public function getBannerAttachment() {
        return $this->getAttachmentsRelation('banner');
    }
}
