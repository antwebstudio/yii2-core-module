<?php
namespace ant\dynamicform\widgets\actions;

use Yii;
use yii\base\Action;

use yii\widgets\ActiveForm;
use ant\dynamicform\models\DynamicField;

class DynamicFormAction extends Action
{
    public $view = '@ant/dynamicform/widgets/views/row';

    private $_actionMap =
    [
        'getsetting' => 'getSetting',
        'getrow' => 'getRow',
    ];

    public function init()
    {
        parent::init();
    }

    public function run($action, $widgetId, $url, $key, $fieldNamePrefix, array $params = [])
    {
        if(array_key_exists($action, $this->_actionMap)){
            return $this->{$this->_actionMap[$action]}($widgetId, $url, $key, $fieldNamePrefix, $params);
        }
    }

    private function getRow($widgetId, $url, $key, $fieldNamePrefix, $params)
    {
        return Yii::$app->view->renderAjax($this->view, [
            'widgetId' => $widgetId,
            'model' => new DynamicField(),
            'form' => new ActiveForm(),
            'url' => $url,
            'key' => $key,
            'fieldNamePrefix' => $fieldNamePrefix,
        ]);
    }

    private function getSetting($widgetId, $url, $key, $fieldNamePrefix, $params)
    {
        return $params['class']::render([
            'widgetId' => $widgetId,
            'form' => new ActiveForm(),
            'model' => new $params['class'],
            'key' => $key,
            'fieldNamePrefix' => $fieldNamePrefix,
        ]);
    }
}
