<?php
return [
    'id' => 'notification',
    'class' => \ant\notification\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		//'v1' => \ant\cms\api\v1\Module::class,
		'backend' => \ant\notification\backend\Module::class,
	],
];
