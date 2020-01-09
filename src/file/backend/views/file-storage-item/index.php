<?php

use yii\helpers\Html;
use yii\grid\GridView;
use ant\file\models\FileAttachment;

/* @var $this yii\web\View */
/* @var $searchModel ant\file\models\FileStorageItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Storage Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-storage-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create File Storage Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
            //'size',
            //'name',
            //'upload_ip',
            //'created_at',

            [
				'class' => 'ant\grid\ActionColumn',
				'template' => '{delete}',
			],
        ],
    ]); ?>


</div>
