<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Inflector;
use kartik\builder\Form;
use kartik\form\ActiveForm;
use ant\attributev2\actions\AttributeV2Action;

$jsId = 'setting-form-' . Inflector::slug($attributeModel::className());
?>
<?php $form = ActiveForm::begin([
	'id' => $jsId . '-form',
	'enableAjaxValidation' => true,
	'validationUrl' => Url::current([AttributeV2Action::ACTION_POST_KEY => AttributeV2Action::ACTION_ON_VALIDATE]),
]); ?>
<?= Form::widget([
	'model' => $attributeModel,
	'form' => $form,
	'attributes' => $attributeModel->fieldtype()->settingForm,
]); ?>
<?= Html::submitButton('Submit', ['class' => 'btn btn-default']); ?>
<?php ActiveForm::end(); ?>