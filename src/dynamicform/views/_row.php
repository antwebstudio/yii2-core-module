<?php  
use yii\helpers\Html;
use ant\dynamicform\base\FieldTypes;
use ant\dynamicform\widgets\DynamicForm;
use ant\dynamicform\models\DynamicField;

$model = isset($model->dynamicFields[$key]) ? $model->dynamicFields[$key] : new DynamicField();
?>

<div class="row <?=DynamicForm::getScriptPrefix(); ?>dynamic-field">
	<div class="col-lg-2">
		<?=$form->field($model, 'label')->textInput([
			'id' => DynamicForm::getScriptPrefix($key) . 'dynamic-field-label',
			'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][label]'
		]); ?>
	</div>

	<div class="col-lg-2">
		<?=$form->field($model, 'class')->dropdownList(FieldTypes::getDropDownList(), [
			'id' => DynamicForm::getScriptPrefix($key) . 'dynamic-field-class',
			'class' => 'form-control ' . DynamicForm::getScriptPrefix() . 'dynamic-field-class-select',
			'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][class]',
			'data-key'=> $key
		]); ?>
	</div>

	<div class="col-lg-7 <?=DynamicForm::getScriptPrefix(); ?>dynamic-setting-content"><?=$content; ?></div>
	
	<div class="col-lg-1">
		<div class="form-group">
			<?=Html::a('<i class="fa fa-close"></i>', 'javascript:void(0);', [
				'class' => 'btn btn-danger ' . DynamicForm::getScriptPrefix() . 'dynamic-field-remove-button'
			])?>
		</div>
	</div>
</div>