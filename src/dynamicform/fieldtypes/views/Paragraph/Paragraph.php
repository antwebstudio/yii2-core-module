<?php
use ant\widgets\ActiveForm;
?>
<?=$form->field($model, 'max')->textInput([
	'id' => $widgetId . $key . 'setting-textinput-max',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][max]'
]); ?>
