<?php
$autoSubmit = \Yii::$app->sandbox->autoRedirect;
?>

<?php if (\Yii::$app->request->post('submit')): ?>
	<p>Url: <?= $model->redirectUrl ?></p>
	<p>Backend Url: <?= $model->backendUrl ?></p>
	<form action="<?= $model->redirectUrl ?>" method="post">
		<?php foreach (Yii::$app->request->post() as $name => $value): ?>
			<?php if ($name != 'submit'): ?>
				<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
			<?php endif ?>
		<?php endforeach ?>
		<input type="submit" value="Pay"/>
		
		<pre>
		<?= var_export(Yii::$app->request->post(), 1) ?>
		</pre>
	</form>
	<?php if ($autoSubmit): ?>
		<script>document.forms[0].submit(); document.forms[0].submit();</script>
	<?php endif ?>
<?php else: ?>

	<?php if ($model->hasErrors()): ?>
	<div class="alert alert-danger">
		<?= \yii\helpers\Html::errorSummary($model) ?>
	</div>
	<?php endif ?>

	<?php if ($isValid): ?>
		<form method="post">
			<?php foreach ($model->returnParams as $name => $value): ?>
				<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
			<?php endforeach ?>
			<input type="hidden" name="BackendURL" value="<?= $model->backendUrl ?>" />
			<input type="hidden" name="ResponseURL" value="<?= $model->redirectUrl ?>" />
			<input type="submit" name="submit" value="Pay"/>
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
<?php endif ?>
