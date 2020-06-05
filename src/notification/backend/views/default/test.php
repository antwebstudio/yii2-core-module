<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ant\widgets\Alert;

?>
<?php $this->beginBlock('header') ?>
<div>
	<?php $form = ActiveForm::begin() ?>
		<?= Alert::widget() ?>
		<?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
	<?php $form = ActiveForm::end() ?>
</div>
<?php $this->endBlock('header') ?>

<?= $this->render($message->view, $message->viewData) ?>