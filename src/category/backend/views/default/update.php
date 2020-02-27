<?php

use yii\helpers\Html;
use ant\language\widgets\LanguageSelector;

/* @var $this yii\web\View */
/* @var $model common\modules\article\models\ArticleCategory */

$typeName = isset($model->type->title) ? $model->type->title : 'Category';

$this->title = $typeName.': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="article-category-update">

	<div class="row">
		<div class="col">
			<div class="text-right">
				Language: <?= LanguageSelector::widget([]) ?>
			</div>
		</div>
	</div>

    <?= $this->render('_form', [
        'model' => $model,
		'showTypeField' => $showTypeField,
    ]) ?>

</div>
