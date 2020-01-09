<?php
return [
    'id' => 'file',
    'class' => \ant\file\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		//'v1' => \ant\cms\api\v1\Module::class,
		'backend' => \ant\file\backend\Module::class,
	],
];
