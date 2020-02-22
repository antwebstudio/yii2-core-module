<?php
$autoSubmit = \Yii::$app->sandbox->autoRedirect;
$debug = \Yii::$app->sandbox->debug;
$amount = $model->returnParams['Amount'];

$this->title = $this->params['title'] = 'Payment Gateway Sandbox';
?>
<style>
form.inline { display: inline; }
</style>

<p>Amount: <?= $amount ?></p>

<?php if (\Yii::$app->request->post('submit')): ?>
	<?php if ($debug): ?>
		<p>Url: <?= $model->redirectUrl ?></p>
		<p>Backend Url: <?= $model->backendUrl ?></p>
	<?php endif ?>
	<form action="<?= $model->redirectUrl ?>" method="post">
		<?php foreach (Yii::$app->request->post() as $name => $value): ?>
			<?php if ($name != 'submit'): ?>
				<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
			<?php endif ?>
		<?php endforeach ?>
		
		<input class="btn btn-primary" type="submit" value="Pay"/>
		
		<?php if ($debug): ?>
			<pre>
			<?= var_export(Yii::$app->request->post(), 1) ?>
			</pre>
		<?php endif ?>
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
		<form class="inline"  method="post">
			<?php foreach ($model->returnParams as $name => $value): ?>
				<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
			<?php endforeach ?>
			<input type="hidden" name="BackendURL" value="<?= $model->backendUrl ?>" />
			<input type="hidden" name="ResponseURL" value="<?= $model->redirectUrl ?>" />
			<input class="btn btn-primary" type="submit" name="submit" value="Pay"/>
		</form>
		
		<?php if ($debug): ?>
			<p>Cancel URL： <?= $model->cancelUrl ?></p>
		<?php endif ?>
		<form class="inline"  action="<?= $model->cancelUrl ?>" method="post">
			<?php foreach ($model->cancelReturnParams as $name => $value): ?>
				<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
			<?php endforeach ?>
			<input class="btn btn-secondary" type="submit" value="Cancel"/>
		</form>
			
		<?php if ($debug): ?>
			<h2>Return Error</h2>
			<pre>
				<?= print_r($model->errorReturnParams, 1) ?>
			</pre>
		
			<form action="<?= $model->redirectUrl ?>" method="post">
				<?php foreach ($model->errorReturnParams as $name => $value): ?>
					<input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
				<?php endforeach ?>
				<input type="submit" value="Error"/>
			</form>
		<?php endif ?>
	<?php endif ?>

	<?php if ($debug): ?>
		<h2>POST: </h2>
		<pre>
			<?= print_r(\Yii::$app->request->post(), 1) ?>
		</pre>
		<h2>Return Params:</h2>
		<pre>
			<?= print_r($model->returnParams, 1) ?>
		</pre>
	<?php endif ?>
<?php endif ?>
