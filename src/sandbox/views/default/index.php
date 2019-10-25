<?php
?>

<?php if ($model->hasErrors()): ?>
<div class="alert alert-danger">
	<?= \yii\helpers\Html::errorSummary($model) ?>
</div>
<?php endif ?>

<?php if ($isValid): ?>
	<form action="<?= $model->redirectUrl ?>" method="post">
		<?php foreach ($model->returnParams as $name => $value): ?>
			<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
		<?php endforeach ?>
		<input type="submit" value="Pay"/>
	</form>
	
	<p>Cancel URL： <?= $model->cancelUrl ?></p>
	<form action="<?= $model->cancelUrl ?>" method="post">
		<?php foreach ($model->cancelReturnParams as $name => $value): ?>
			<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
		<?php endforeach ?>
		<input type="submit" value="Cancel"/>
	</form>
		
	<h2>Return Error</h2>
	<pre>
		<?= print_r($model->errorReturnParams, 1) ?>
	</pre>
	<form action="<?= $model->redirectUrl ?>" method="post">
		<?php foreach ($model->errorReturnParams as $name => $value): ?>
			<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
		<?php endforeach; ?>
		<input type="submit" value="Error"/>
	</form>
<?php endif ?>

<h2>POST: </h2>
<pre>
	<?= print_r(\Yii::$app->request->post(), 1) ?>
</pre>
<h2>Return Params:</h2>
<pre>
	<?= print_r($model->returnParams, 1) ?>
</pre>
