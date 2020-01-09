<?php
namespace ant\file\widgets;

use yii\helpers\Json;
use yii\helpers\Html;
use yii\jui\JuiAsset;

class Upload extends \trntv\filekit\widget\Upload {
    public $fields = [];
    public $form;
    public $customFieldDivCss = 'upload-custom-field-group';

    protected $_fieldObjects = [];
	
	protected static function getInputName($attribute) {
		if (!preg_match(Html::$attributeRegex, $attribute, $matches)) {
            throw new \InvalidArgumentException('Attribute name must contain word characters only.');
        }
		$prefix = $matches[1];
        $attribute = $matches[2];
        $suffix = $matches[3];
		
		return $prefix . "[$attribute]" . $suffix;
	}

    public function init() {
        $this->getView()->registerCss('.'.$this->customFieldDivCss.' { display: none; }');
        
        if (!isset($this->clientOptions['fields'])) {
			foreach ($this->fields as &$field) {
				$field['name'] = self::getInputName($field['name']);
			}
            $this->clientOptions['fields'] = $this->fields;
        }
		
		$this->clientOptions['removeIcon'] = 'fas fa-times-circle';
		$this->clientOptions['plusIcon'] = 'fas fa-plus-circle';
		$this->clientOptions['downIcon'] = 'fas fa-arrow-circle-down';
        return parent::init();
    }
    
    public function run() {
        /*$html = '';
        for ($i = 0; $i < count($this->model->{$this->attribute}); $i++) {
            $formAttributes = $this->getAttributesFor($i, $this->fields);

            if (count($formAttributes)) {
                $html .= Html::tag('div', \kartik\builder\Form::widget([
                    'model' => $this->model,
                    'form' => $this->form,
                    'attributes' => $formAttributes,
                ]), ['id' => 'widget-'.$this->id.'-'.$i, 'class' => 'upload-custom-field-group']);
            }
        }*/
        
        return $this->_run();
    }

    /**
     * Registers required script for the plugin to work as jQuery File Uploader
     */
    public function registerClientScript()
    {
		if (isset(Yii::$app->params['bsVersion']) && Yii::$app->params['bsVersion'] >= 4) {
			UploadBootstrap4Asset::register($this->getView());
		} else {
			UploadAsset::register($this->getView());
		}
        $options = Json::encode($this->clientOptions);
        if ($this->sortable) {
            JuiAsset::register($this->getView());
        }
        $this->getView()->registerJs("jQuery('#{$this->getId()}').yiiUploadKit({$options});");
    }

    protected function _run() {
        $this->registerClientScript();
        $content = Html::beginTag('div');
		
		// This hidden input is needed to update the file field when it is empty (all files is removed)
        $content .= Html::hiddenInput($this->name, null, [
            'class' => 'empty-value',
            'id' => $this->hiddenInputId === null ? $this->options['id'] : $this->hiddenInputId
        ]);
        $content .= Html::fileInput($this->getFileInputName(), null, [
            'name' => $this->getFileInputName(),
            'id' => $this->getId(),
            'multiple' => $this->multiple
        ]);
        $content .= Html::endTag('div');
        return $content;
    }

    protected function getAttributesFor($index, $fieldConfigs) {
        $fields = [];
        foreach ($fieldConfigs as $name => $config) {
            $order = $this->model->{$this->attribute}[$index]['order'];
            $config['options']['name'] = $this->name.'['.$index.']['.$name.']';
            $fields[$this->attribute.'['.$order.']['.$name.']'] = $config;
        }
        return $fields;
    }
}