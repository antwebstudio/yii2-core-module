<?php
use yii\widgets\ListView;

$this->title = 'My Files';
?>

<style>
	.file-thumb .fa {
		font-size: 70px;
	}
	.file-thumb.expired .expired-text {
		color: red;
	}
</style>

<?php if (isset($folder)): ?>
	<div class="row">
		<?= ListView::widget([
			'dataProvider' => $fileDataProvider,
			'itemView' => '_file',
			'summaryOptions' => ['class' => 'summary col-md-12'],
		]) ?>
	</div>
	
<?php endif; ?>