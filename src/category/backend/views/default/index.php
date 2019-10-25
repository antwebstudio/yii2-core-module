<?php

use yii\helpers\Html;
use yii\grid\GridView;
$controllerClassName = $this->context->className();

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = (isset($type) ? ucfirst($type.' ') : '') . 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?php if ($showTypeField): ?>
		<?= $this->render('_categoryTab') ?>
	<?php endif ?>
	
    <p>
        <?= Html::a('Create Category', ['create', 'type' => $type, 'showType' => $showTypeField], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n<div class=\"table-responsive\">{items}</div>\n{pager}",
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'slug',
            //'title',
            //'body:ntext',
            //'parent_id',
            // 'status',
            // 'created_at',
			[
				'class' => 'common\widgets\NestedSortableColumn',
				'attribute' => 'title',
				'structureId' => 1,
				'moveElementUrl' => ['/category/category/move-tree-node'],
			],
            [
                'label' => 'Updated At',
                'attribute' => 'updated_at',
            ],
            // 'subtitle',
            // 'icon_base_url:url',
            // 'icon_path',
            // 'thumbnail_base_url:url',
            // 'thumbnail_path',
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'delete' => function($model, $key) {
                        $controllerClassName = \ant\category\backend\controllers\DefaultController::className();
                        return Yii::$app->user->can(common\rbac\Permission::of('delete', $controllerClassName)->name);  
                    },
                ],
            ],
        ],
    ]); ?>
</div>
