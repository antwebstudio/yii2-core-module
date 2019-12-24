<?php

use yii\helpers\Html;
use yii\grid\GridView;
use ant\rbac\Permission;

$controllerClassName = $this->context->className();

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = (isset($type) ? ucfirst($type.' ') : '') . 'Categories';
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-category-index">
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
				'class' => 'ant\widgets\NestedSortableColumn',
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
                        return Permission::can('delete', $controllerClassName);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
