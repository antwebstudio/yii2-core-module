<?php

namespace ant\category;

/**
 * category module definition class
 */
class Module extends \yii\base\Module
{
	public $categoryImageConfig;

	public function getCategoryImageConfig($attachmentType, $categoryType) {
		if (isset($this->categoryImageConfig)) {
			return call_user_func_array($this->categoryImageConfig, [$attachmentType, $categoryType]);
		}
	}
	
	public function behaviors() {
		return [
			'configurable' => [
				'class' => 'common\behaviors\ConfigurableModuleBehavior',
				'formModels' => [
					'default' => [
						'class' => 'ant\category\models\Category',
						'as field' => [
							'class' => 'common\behaviors\ConfigurableModelBehavior',
						],
					],
					'product' => [
						'class' => 'ant\category\models\Category',
						'as field' => [
							'class' => 'common\behaviors\ConfigurableModelBehavior',
						],
					],
				],
			]
		];
	}

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
