<?php

use yii\helpers\Html;
use yii\grid\GridView;
use ant\rbac\Permission;

$controllerClassName = $this->context->className();

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$typeName = isset($categoryType->title) ? $categoryType->title : 'Category';

$this->title = 'Manage '.$categoryType->title;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

if (!$categoryType->is_uncategorized_show) {
	$dataProvider->query->andWhere(['>', 'category.depth', 0]);
}
?>
<div class="article-category-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?php if ($showTypeField): ?>
		<?= $this->render('_categoryTab') ?>
	<?php endif ?>
	
    <p>
        <?= Html::a('Create '.$typeName, ['create', 'type' => $type, 'showType' => $showTypeField], ['class' => 'btn btn-success']) ?>
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
				'class' => 'ant\widgets\NestedSortableColumn',
				'sortable' => $categoryType->is_hierarchical,
				'attribute' => 'title',
				'structureId' => 1,
				'moveElementUrl' => ['/category/backend/category/move-tree-node'],
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
                'class' => 'ant\grid\ActionColumn',
                'visibleButtons' => [
                    'view' => function($model) {
                        return false;
                    },
                    'delete' => function($model, $key) {
                        $controllerClassName = \ant\category\backend\controllers\DefaultController::className();
                        return !$model->isRoot() && Permission::can('delete', $controllerClassName);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
