<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use ant\file\widgets\Upload;
use common\modules\category\models\Category;
/* @var $this yii\web\View */
/* @var $model common\modules\article\models\ArticleCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-category-form">

    <?php $form = ActiveForm::begin(); ?>
    
	<?php if ($showTypeField && count($categoryTypes)): ?>
		<?= $form->field($model, 'type')->widget(Select2::classname(), [
				'data' => $categoryTypes,
				'maintainOrder' => true,
				'options' => [
					'placeholder' => '', 'multiple' => false
				],
				'pluginOptions' => ['allowClear' => true],
			]);
		?>
	<?php endif ?>
	
    <?php if ($model->isFieldShow('title')): ?>
        <?= $form->field($model, 'title')->textInput() ?>
    <?php endif ?>

    <?php if ($model->isFieldShow('subtitle')): ?>
        <?= $form->field($model, 'subtitle')->textInput() ?>
    <?php endif ?>

    <?php if ($model->isFieldShow('slug')): ?>
        <?php echo $form->field($model, 'slug')
            ->hint(Yii::t('backend', 'If you\'ll leave this field empty, slug will be generated automatically'))
            ->textInput(['maxlength' => 1024])
        ?>
    <?php endif ?>

    <?php if ($model->isFieldShow('body')): ?>
        <?= $form->field($model, 'body')->widget(\ant\widgets\TinyMce::className(), [
        ]) ?>
    <?php endif ?>

	<?php /*
    <?php if ($model->isFieldShow('parent_id')): ?>
        <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Category::find()->all(), 'id', 'title'),
                'maintainOrder' => true,
                'options' => [
                    'placeholder' => '', 'multiple' => false
                ],
                'pluginOptions' => ['allowClear' => true],
            ]);
        ?>
    <?php endif ?> 
	*/ ?>

    <?php if ($model->isFieldShow('attachments')): ?>
        <?= $form->field($model, 'attachments')->widget(
            Upload::className(),
            [
                'url' => ['image-upload', 'type' => 'attachments', 'category-type' => $model->type->id],
                'sortable' => true,
                'maxFileSize' => 10000000, // 10 MiB
                'maxNumberOfFiles' => 100,
                'acceptFileTypes' => new yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png)$/i'),
            ]
        ) ?>
    <?php endif ?>

    <?php if ($model->isFieldShow('thumbnail')): ?>
        <?= $form->field($model, 'thumbnail')->widget(
            Upload::classname(),
            [
                'url' => ['image-upload', 'type' => 'thumbnail', 'category-type' => $model->type->id],
                'sortable' => true,
                'maxFileSize' => 10000000, // 10 MiB
                'maxNumberOfFiles' => 1,
                'acceptFileTypes' => new yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png)$/i'),
            ]
        ) ?>
    <?php endif ?>

    <?php if ($model->isFieldShow('banner')): ?>
        <?= $form->field($model, 'banner')->widget(
            Upload::classname(),
            [
                'url' => ['image-upload', 'type' => 'banner', 'category-type' => $model->type->id],
                'sortable' => true,
                'maxFileSize' => 10000000, // 10 MiB
                'maxNumberOfFiles' => 1,
                'acceptFileTypes' => new yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png)$/i'),
            ]
        ) ?>
    <?php endif ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
