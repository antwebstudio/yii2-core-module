<?=$form->field($model, 'min')->textInput([
    'id' => $widgetId . $key . 'setting-textfield-min',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][min]'
]); ?>

<?=$form->field($model, 'max')->textInput([
    'id' => $widgetId . $key . 'setting-textfield-max',
	'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][max]'
]); ?>
