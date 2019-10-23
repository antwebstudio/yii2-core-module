<?php
use yii\helpers\Html;
use yii\web\View;

use ant\dynamicform\base\FieldTypes;
?>
<div class="dynamic-field-row">
    <div class="row">
        <div class="col-lg-2">
    		<?=$form->field($model, 'label')->textInput([
    			'id' => $widgetId . $key . 'dynamic-field-label',
    			'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][label]'
    		]); ?>
    	</div>

        <div class="col-lg-2">
			<div>
				<?php 
					$options = []; 
					if (!isset($model->class)) {
						$options[\ant\dynamicform\fieldtypes\classes\TextField::className()] =  ['Selected'=>'selected'];
					}
				?>
				<?=$form->field($model, 'class')->dropdownList(FieldTypes::getDropDownList(), [
					'id' => $widgetId . $key . 'dynamic-field-class',
					'class' => 'form-control',
					'options' => $options,
					'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][class]',
				]); ?>
			</div>
			<div>
				<?=$form->field($model, 'required')->checkbox([
					'id' => $widgetId . $key . 'dynamic-field-required',
					'name' => $fieldNamePrefix . '[dynamicFormForm][DynamicFormForm][dynamicFields][' . $key . '][DynamicField][required]',
				]); ?>
			</div>
    	</div>

        <div id="<?= $widgetId . $key ?>dynamic-field-setting-container" class="col-lg-7">
            <?php if ($model->class): ?>
				<?php $className = $model->class; ?>
				<?= $className::render([
					'form' => $form,
					'widgetId' => $widgetId,
					'model' => new $model->class($model->setting),
					'key' => $key,
					'fieldNamePrefix' => $fieldNamePrefix,
				]) ?>
            <?php endif ?>
        </div>

        <div class="col-lg-1">
    		<div class="form-group">
    			<?= Html::a('<i class="fa fa-close"></i>', 'javascript:void(0);', [
    				'class' => 'btn btn-danger',
                    'onclick' => '$(this).closest(".dynamic-field-row").fadeOut(300, function(){$(this).remove()})'
    			]) ?>
    		</div>
    	</div>
    </div>
</div>

<?php \ant\widgets\JsBlock::begin() ?>
<script>
(function(){
    var widget = $('#<?=$widgetId;?>widget-dynamic-form');
    var container = widget.find('#<?=$widgetId;?>dynamic-field-container');
    var classSelect = container.find('#<?=$widgetId . $key;?>dynamic-field-class');
    var settingContainer = container.find('#<?=$widgetId . $key;?>dynamic-field-setting-container');

    classSelect.on('change', function(){
        var select = $(this);

        $.ajax({
            url: '<?=$url;?>',
            data:
            {
                action: 'getsetting',
                widgetId: '<?=$widgetId;?>',
                key: '<?=$key;?>',
                url: '<?=$url;?>',
                fieldNamePrefix: '<?=$fieldNamePrefix;?>',
                params:
                {
                    class: select.val()
                }
            },
            type: 'GET',
            beforeSend: function(){},
            success: function(html){
                settingContainer.html($(html).hide().fadeIn(300));
            },
            complete: function(){}
        });
    });

    <?php if(!$model->class): ?>
		classSelect.trigger('change');
    <?php endif; ?>
}());
</script>
<?php \ant\widgets\JsBlock::end() ?>
