<?php

use yii\helpers\Html;
use yii\grid\GridView;
use ant\file\models\FileAttachment;

/* @var $this yii\web\View */
/* @var $searchModel ant\file\models\FileStorageItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Attachments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-attachment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
			[
				'format' => 'html',
				'value' => function($model) {
					return Html::img(FileAttachment::getFirstUrl($model), ['height' => 120]);
				}
			],
            //'component',
            //'base_url:url',
            //'path',
            'type',
            'size',
            'name',
			[
				'format' => 'raw',
				'label' => 'Attached To',
				'value' => function($model) {
					return isset($model->model) ? Html::a($model->model->name, \Yii::$app->urlManagerFrontEnd->createUrl($model->model->route), ['target' => '_blank', 'data-pjax' => 0]) : null;
				}
			],
            //'upload_ip',
            //'created_at',

            [
				'class' => 'ant\grid\ActionColumn',
				'template' => '{delete}',
			],
        ],
    ]); ?>


</div>
