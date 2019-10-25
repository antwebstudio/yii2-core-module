<?php
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\helpers\Html;
?>
<style>
	.file-thumb .fa {
		font-size: 70px;
	}
	.file-thumb.expired .expired-text {
		color: red;
	}
</style>

<div class="file-default-index">
	<?php $form = ActiveForm::begin([]) ?>
		<?= $form->errorSummary($model) ?>
		
		<?= \kartik\select2\Select2::widget([
			'name' => 'userId',
			'value' => $userId,
			'data' => $users,
			'options' => [
				'placeholder' => 'Select a user',
				'data' => [
					'method' => 'get',
					'params' => ['submit-type' => 'select-user']
				],
			],
		]) ?>
	<?php ActiveForm::end() ?>
	
		
		<?php if (isset($folder)): ?>
			<div class="row">
				<?= ListView::widget([
					'dataProvider' => $fileDataProvider,
					'itemView' => '_file',
					'viewParams' => ['allowDelete' => true, 'allowDownloadExpired' => true],
					'summaryOptions' => ['class' => 'summary col-md-12'],
				]) ?>
			</div>
			
			<div class="row">&nbsp</div>
			
			<?php $form = ActiveForm::begin([]) ?>
			<div class="row">
				<div class="col-md-12">
					<h2>Upload</h2>
					
					<?= $form->field($model, 'expireAt')->widget(\kartik\date\DatePicker::className(), [
						'model' => $model,
						'attribute' => 'expireAt',
						'pluginOptions' => [
							'autoclose' => true,
							'todayHighlight' => true,
							'format' => 'yyyy-mm-dd'
						],
					]) ?>
					
					<?= $form->field($model, 'files')->widget(\trntv\filekit\widget\Upload::className(), [
						//'name' => 'files',
						'model' => $model,
						'attribute' => 'files',
						'multiple' => true,
						'url' => ['upload'],
						'maxNumberOfFiles' => 10,
					]) ?>
					
					
					<?= Html::submitButton('Select User', ['name' => 'submit-type', 'value' => 'select-user', 'class' => 'btn btn-primary hidden']) ?>
					<?= Html::submitButton('Upload', ['class' => 'submit', 'class' => 'btn btn-primary']); ?>
				</div>
			</div>
			<?php ActiveForm::end() ?>
			
		<?php endif; ?>
</div>
