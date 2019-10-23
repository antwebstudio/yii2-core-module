<div class="">
	<?=$form->field($model, 'items[' . $dropDropItemKey . ']')->textInput([
		'id' => $widgetId . $key . 'setting-dropdown-items-item-' . $dropDropItemKey,
		'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][items][' . $dropDropItemKey . ']',
        'placeholder' => 'item'
	])->label(false);?>
</div>
