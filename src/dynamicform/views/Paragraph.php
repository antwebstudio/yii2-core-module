<?php
use common\widgets\ActiveForm;

use ant\dynamicform\widgets\DynamicForm;
use ant\dynamicform\models\fieldtypes\Paragraph;
$hasForm = isset($form);
$model = isset($model->dynamicFields[$key]) ? $model->dynamicFields[$key]->model : new Paragraph();
?>
<?php if (!$hasForm) $form = ActiveForm::begin(); ?>
<?=$form->field($model, 'row')->textInput([
	'id' => Paragraph::getScriptPrefix($key) . 'setting-textinput-min',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][min]'
]); ?>
<?php if (!$hasForm) ActiveForm::end(); ?>
