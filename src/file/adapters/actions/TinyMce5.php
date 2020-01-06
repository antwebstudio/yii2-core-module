<?php

namespace ant\file\adapters\actions;

use yii\web\JsExpression;

/**
 * TinyMCE action
 */
class TinyMce5 extends \alexantr\elfinder\ClientBaseAction
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->settings['getFileCallback'] = new JsExpression('
			function (file) {
				parent.postMessage({mceAction: "FileSelected", file: file}, "*");
			}
		');

        return parent::run();
    }
}
