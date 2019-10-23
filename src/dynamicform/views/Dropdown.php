<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use ant\dynamicform\widgets\DynamicForm;
use ant\dynamicform\models\fieldtypes\Dropdown;

$model = isset($model->dynamicFields[$key]) ? $model->dynamicFields[$key]->model : new Dropdown();
$model->items = $model->items ? $model->items : [0 => ''];
?>

<div>
	<table class="table <?=Dropdown::getScriptPrefix(); ?>items-container">
		<thead>
			<tr>
				<th>items</th>
				<th width="1px">
				<?=Html::a('<i class="fa fa-plus"></i>', 'javascript:void(0);', [
					'id' => Dropdown::getScriptPrefix($key) . 'setting-item-add-button',
					'class' => 'btn btn-success ' . Dropdown::getScriptPrefix() . 'setting-item-add-button',
					'data-key' => $key,
					'data-dropdown-item-key' => max(array_keys($model->items)),
				])?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $firstItem = true; ?>
			<?php foreach ($model->items as $dropdownItemKey => $item): ?>
			<?=Dropdown::renderItems(ArrayHelper::merge($__params, [
				'model' => $model,
				'dropdownItemKey' => $dropdownItemKey,
			]), !$firstItem); ?>
			<?php $firstItem = false ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
