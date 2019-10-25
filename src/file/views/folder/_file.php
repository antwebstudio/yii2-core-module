<?php
use yii\helpers\Url;
use yii\helpers\Html;

/*
 * @params $allowDelete
 * @params $allowDownloadExpired
 */

$allowDelete = isset($allowDelete) ? $allowDelete : false;
$allowDownloadExpired = isset($allowDownloadExpired) ? $allowDownloadExpired : false;
$disabled = $model->isExpired && !$allowDownloadExpired;
$url = $allowDownloadExpired ? $model->directUrl : $model->url;
?>
<div class="col-sm-6 col-md-4 text-center" style="margin-bottom: 10px">
	<a class="file-thumb btn btn-default col-sm-12 text-center <?= $disabled ? 'expired disabled' : ''?>" href="<?= $disabled ? 'javascript:;' : $url ?>">
		<i class="fa <?= $model->iconCssClass ?> fa-4"></i>
		<p><?= $model->filename ?></p>
		<p class="expired-text"><?= $model->isExpired ? 'Expired' : 'Expire date: '.(isset($model->expire_at) ? $model->expire_at : 'Never') ?></p>
	</a>
	
	<?php if ($allowDelete): ?>
		<?= Html::a('Delete', ['/file/file/delete', 'id' => $model->id], [
			'class' => 'btn btn-default col-sm-12 col-md-12',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	<?php endif; ?>
</div>