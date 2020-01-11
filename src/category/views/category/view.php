<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => 'Category', 'url' => ['/category/category']];
?>
<h1><?= $model->title ?></h1>