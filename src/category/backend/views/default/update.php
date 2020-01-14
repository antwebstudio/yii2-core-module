<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\article\models\ArticleCategory */

$typeName = isset($model->type->title) ? $model->type->title : 'Category';

$this->title = $typeName.': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="article-category-update">
    <?= $this->render('_form', [
        'model' => $model,
		'showTypeField' => $showTypeField,
    ]) ?>

</div>
