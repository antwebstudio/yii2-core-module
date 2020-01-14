<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\article\models\ArticleCategory */

$typeName = isset($model->type->title) ? $model->type->title : 'Category';

$this->title = 'Create '.$typeName;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-category-create">
    <?= $this->render('_form', [
        'model' => $model,
		'showTypeField' => $showTypeField,
		'categoryTypes' => $categoryTypes,
    ]) ?>

</div>
