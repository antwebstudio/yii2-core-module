<?php
use yii\web\View;
use yii\helpers\Html;

use ant\helpers\ScriptHelper;
?>
<div id="<?=$widgetId . $key;?>dynamicfield-dropdown-setting">
	<?= $form->field($model, 'min')->textInput([
		'id' => $widgetId . $key . 'setting-textfield-min',
		'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][min]'
	]) ?>

	<?= $form->field($model, 'max')->textInput([
		'id' => $widgetId . $key . 'setting-textfield-max',
		'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][setting][max]'
	]) ?>

	<div id="<?=$widgetId . $key;?>dynamicfield-dropdown-setting-items">
	<b>Items</b>
	<?php foreach ($model->items as $dropDropItemKey => $value): ?>
		<?= $this->render('@ant/dynamicform/fieldtypes/views/DropDown/DropDownItem', [
			'dropDropItemKey' => $dropDropItemKey,
			'form' => $form,
			'model' => $model,
			'widgetId' => $widgetId,
			'key' => $key,
			'fieldNamePrefix' => $fieldNamePrefix,
		]) ?>
	<?php endforeach ?>
	</div>


	<?= Html::a('New Item', 'javascript:void(0);', [
		'id' => $widgetId . $key . 'new-dynamicfield-dropdown-setting-item-button',
		'class' => 'btn btn-default btn-xs ',
		'data-key' => isset($dropDropItemKey) ? (int)str_replace('new', '', $dropDropItemKey) + 1 : 1
	]) ?>
</div>

<?php \ant\widgets\JsBlock::begin() ?>
<script>
(function() {
	var dropDownSetting = $('#<?=$widgetId . $key ?>dynamicfield-dropdown-setting');
	var newDropDownItemBtn = dropDownSetting.find('#<?= $widgetId . $key ?>new-dynamicfield-dropdown-setting-item-button');
	var container = dropDownSetting.find('#<?=$widgetId . $key?>dynamicfield-dropdown-setting-items');

	newDropDownItemBtn.on('click', function(event){

		var btn = $(this);
		var key = btn.data('key');

		var item = '<?=ScriptHelper::jsOneLineString($this->render('@ant/dynamicform/fieldtypes/views/DropDown/DropDownItem', [
			'dropDropItemKey' => '__key__',
			'form' => $form,
			'model' => $model,
			'widgetId' => $widgetId,
			'key' => $key,
			'fieldNamePrefix' => $fieldNamePrefix,
		]));?>';

		item = item.replace(/__key__/g, key);

		btn.data('key', btn.data('key') + 1);

		container.append($(item).hide().fadeIn(300));
	});

	<?php if(empty($model->items)): ?>newDropDownItemBtn.trigger('click');<?php endif ?>
})();
</script>
<?php \ant\widgets\JsBlock::end() ?>
