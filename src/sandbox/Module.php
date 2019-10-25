<?php

namespace ant\sandbox;

/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{
	
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'ant\sandbox\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
