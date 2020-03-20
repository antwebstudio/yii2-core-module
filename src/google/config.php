<?php
return [
    'id' => 'google',
    'class' => \ant\google\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		//'v1' => \ant\cms\api\v1\Module::class,
		'backend' => \ant\google\backend\Module::class,
	],
];
