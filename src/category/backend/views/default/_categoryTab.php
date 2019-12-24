<?php 
use ant\widgets\Nav;
?>
<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMenu('categoryType') : [],
]) ?>