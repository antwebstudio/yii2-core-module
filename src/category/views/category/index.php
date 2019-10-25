<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ListView;

$this->title = 'Product';

$this->params['breadcrumbs'][] = ['label' => 'Category', 'url' => ['/category/category']];
?>
<div class="product-category products">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_category',
		'summary' => '',
	]) ?>
</div>






