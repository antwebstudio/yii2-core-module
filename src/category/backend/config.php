<?php

return [
    'id' => 'category',
    'class' => \ant\category\backend\Module::className(),
    'isCoreModule' => false,
	'depends' => ['file'], // Payment module should not depends on any other module
];
?>