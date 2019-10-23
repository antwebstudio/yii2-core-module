<?php
use yii\helpers\Html;
use ant\dynamicform\widgets\DynamicForm;
use ant\dynamicform\models\fieldtypes\Dropdown;

$model = isset($model) ? $model : new Dropdown();
?>
<tr id="<?=Dropdown::getScriptPrefix($key)?>setting-dropdown-item-<?=$dropdownItemKey; ?>">
	<td>
		<?=$form->field($model, 'items[' . $dropdownItemKey . ']')->textInput([
			'id' => Dropdown::getScriptPrefix($key) . 'setting-dropdown-item-' . $dropdownItemKey,
			'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][items][' . $dropdownItemKey . ']',
			'placeholder' => 'item'
		])->label(false); ?>
	</td>
	<td>
		<?php $isDeleteAble = isset($isDeleteAble) ? $isDeleteAble : true; ?>
		<?php if ($isDeleteAble): ?>
		<?=Html::a('<i class="fa fa-close"></i>', 'javascript:void(1);', [
			'class' => 'btn btn-danger',
			'onclick' => '$("#' . Dropdown::getScriptPrefix($key) . 'setting-dropdown-item-' . $dropdownItemKey . '").fadeOut(300, function(){$("#' . Dropdown::getScriptPrefix($key) . 'setting-dropdown-item-' . $dropdownItemKey . '").remove()});'
		])?>
		<?php endif ?>
	</td>
</tr>
