<?php
return [
    'id' => 'category',
    'class' => \ant\category\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		//'v1' => \ant\category\api\v1\Module::class,
		'backend' => \ant\category\backend\Module::class,
	],
	'depends' => ['file'],
];