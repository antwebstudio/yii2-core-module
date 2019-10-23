<?php
use yii\helpers\Html;
use yii\web\View;
use ant\dynamicform\base\FieldTypes;
use ant\dynamicform\widgets\DynamicForm;
/*
	$model instance of ant\dynamicform\models\DynamicFormForm
*/
?>

<div id="<?= $this->context->id ?>widget-dynamic-form" class="widget-dynamicform">
	<div id="<?= $this->context->id ?>dynamic-field-container" class="dynamic-field-container">
		<?php foreach ($this->context->getFields() as $key => $dynamicField): ?>
			<?= $this->render('row', [
				'url' => $url,
				'fieldNamePrefix' => $fieldNamePrefix,
				'key' => $key,
				'widgetId' => $this->context->id,
				'model' => $dynamicField,
				'form' => $form
			]) ?>
		<?php endforeach ?>
		
		<?= Html::hiddenInput($fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][]') ?>
	</div>

	<?=Html::a('New Field', 'javascript:void(0);', [
		'id' => $this->context->id . 'new-dynamic-field-button',
	    'class' => 'btn btn-default btn-xs ',
	    'data-key' => isset($key) ? (int)str_replace('new', '', $key) + 1 : 1
	])?>
</div>

<?php \ant\widgets\JsBlock::begin(['pos' => View::POS_READY, 'key' => 'widget-dynamic-form' . $this->context->id]) ?>
<script>
(function(){

	var widget = $('#<?=$this->context->id;?>widget-dynamic-form');
	var addNewBtn = widget.find('#<?=$this->context->id;?>new-dynamic-field-button');
	var container = widget.find('#<?=$this->context->id;?>dynamic-field-container');

	addNewBtn.on('click', function(event){

		var btn = $(this);

		var key = 'new' + btn.data('key');

		$.ajax({
			url: '<?=$url;?>',
			data:
			{
				action: 'getrow',
				widgetId: '<?=$this->context->id;?>',
				key: key,
				url: '<?=$url;?>',
				fieldNamePrefix: '<?=$fieldNamePrefix;?>'
			},
			type: 'GET',
			beforeSend: function(){},
			success: function(html){
				container.append($(html).hide().fadeIn(300));

				btn.data('key', (btn.data('key') + 1));
			},
			complete: function(){}
		});
	});
}());
</script>
<?php \ant\widgets\JsBlock::end() ?>