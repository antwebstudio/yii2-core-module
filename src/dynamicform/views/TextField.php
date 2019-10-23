<?php
use ant\dynamicform\widgets\DynamicForm;
use ant\dynamicform\models\fieldtypes\TextField;

$model = isset($model->dynamicFields[$key]) ? $model->dynamicFields[$key]->model : new TextField();
?>

<?=$form->field($model, 'min')->textInput([
	'id' => TextField::getScriptPrefix($key) . 'setting-textinput-min',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][min]'
]); ?>

<?=$form->field($model, 'max')->textInput([
	'id' => TextField::getScriptPrefix($key) . 'setting-textinput-max',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][max]'
]); ?>
