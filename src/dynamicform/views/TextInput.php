<?php  
use ant\dynamicform\widgets\DynamicForm;
use ant\dynamicform\models\fieldtypes\TextInput;

$model = isset($model->dynamicFields[$key]) ? $model->dynamicFields[$key]->model : new TextInput();
?>

<?=$form->field($model, 'type')->dropdownList(TextInput::getTypesDropDownList(), [
	'id' => TextInput::getScriptPrefix($key) . 'setting-textinput-type',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][type]',
]); ?>

<?=$form->field($model, 'min')->textInput([
	'id' => TextInput::getScriptPrefix($key) . 'setting-textinput-min',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][min]'
]); ?>

<?=$form->field($model, 'max')->textInput([
	'id' => TextInput::getScriptPrefix($key) . 'setting-textinput-max',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][max]'
]); ?>