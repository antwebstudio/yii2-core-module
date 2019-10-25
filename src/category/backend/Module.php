<?php

namespace ant\category\backend;

/**
 * category module definition class
 */
class Module extends \common\modules\category\Module
{
    /**
     * @inheritdoc
     */
    /*public $controllerNamespace = 'ant\category\backend\controllers';
    //public $formModelCategoryAttributeToBeShow = null;
    public $categoryTypeSelection = [
        'article' => 'article',
    ];

    public $defaultFormCategoryModelAttributeToBeShow = [
        'title',
        'subtitle',
        'slug',
        'body',
        'parent_id',
        'thumbnail',
        'icon',
    ];*/

    //public $editable = null; // no longer need
    /*public $model = [
        'default' => [
            'model' => [
                'article' => [   
                    'class' => 'common\modules\article\models\Article',
                    'status' => 0,
                ],
                'book' => [
                    'class' => 'common\modules\category\models\Category',
                ],
            ],
        ],
    ];

    public function getConfigurableModel($modelType, $type) {
        return \Yii::createObject($this->model[$modelType]['model'][$type]);
    }*/

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
